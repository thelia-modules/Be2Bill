<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Be2Bill;

use Be2Bill\Events\Be2BillEvents;
use Be2Bill\Events\PaymentParametersEvent;
use Be2Bill\Model\Be2billConfigQuery;
use Be2Bill\Model\Be2billMethod;
use Be2Bill\Model\Be2billMethodQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Finder\Finder;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Log\Tlog;
use Thelia\Model\LangQuery;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleImageQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;

class Be2Bill extends AbstractPaymentModule
{
    const MODULE_DOMAIN = 'be2bill';

    const CONFIRMATION_MESSAGE_NAME = 'be2bill_payment_confirmation';

    const URL_PAYMENT_FORM = '/front/form/process';
    const URL_SERVER_TO_SERVER = '/front/service/rest/process';

    // CB
    const METHOD_CLASSIC = '';
    // PayPal
    const METHOD_PAYPAL = 'paypal';
    // Not really a new method, but it uses CB with multiple payments
    const METHOD_NTIMES = 'ntimes';

    /** @var Tlog|null  */
    protected static $logger = null;

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        try {
            Be2billConfigQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database->insertSql(null, array(__DIR__ . DS . 'Config' . DS . 'thelia.sql'));
        }

        // initialize config var
        $this->initializeConfig();

        // create message
        if (null === MessageQuery::create()->findOneByName(self::CONFIRMATION_MESSAGE_NAME)) {
            $translator = Translator::getInstance();

            $message = new Message();
            $message
                ->setName(self::CONFIRMATION_MESSAGE_NAME)
                ->setHtmlTemplateFileName('be2bill-payment-confirmation.html')
                ->setHtmlLayoutFileName('')
                ->setTextTemplateFileName('be2bill-payment-confirmation.txt')
                ->setTextLayoutFileName('')
                ->setSecured(0);

            $languages = LangQuery::create()->find();

            foreach ($languages as $language) {
                $locale = $language->getLocale();

                $message->setLocale($locale);
                $message->setSubject(
                    $translator->trans('Payment of order {$order_ref}.', [], self::MODULE_DOMAIN, $locale)
                );
                $message->setTitle(
                    $translator->trans('Be2Bill payment confirmation', [], self::MODULE_DOMAIN, $locale)
                );
            }

            $message->save();
        }


        $module = $this->getModuleModel();

        if (ModuleImageQuery::create()->filterByModule($module)->count() == 0) {
            $this->deployImageFolder($module, sprintf('%s/images', __DIR__), $con);
        }
    }

    /**
     * @inheritdoc
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        parent::update($currentVersion, $newVersion, $con);

        // add missing variables
        $this->initializeConfig();

        $finder = (new Finder)
            ->files()
            ->name('#.*?\.sql#')
            ->in(__DIR__ . DS . 'Config' . DS . 'update');

        $database = new Database($con);

        /** @var \Symfony\Component\Finder\SplFileInfo $updateSQLFile */
        foreach ($finder as $updateSQLFile) {
            if (version_compare($currentVersion, str_replace('.sql', '', $updateSQLFile->getFilename()), '<')) {
                $database->insertSql(
                    null,
                    [
                        $updateSQLFile->getPathname()
                    ]
                );
            }
        }
    }

    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is send to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway.
     * On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  \Thelia\Model\Order $order processed order
     * @return null|\Thelia\Core\HttpFoundation\Response
     */
    public function pay(Order $order)
    {
        if ('yes' !== Be2billConfigQuery::read('activated', 'yes')) {
            throw new \InvalidArgumentException("be2bill is not a valid payment method.");
        }

        $paymentMethod = $this->getRequest()->getSession()->get(
            'be2bill-method',
            self::METHOD_CLASSIC
        );

        $be2billParams = $this->getBe2BillParameters($order, $paymentMethod, 'payment');

        if (false === $platformUrl = Be2billConfigQuery::read('url', false)) {
            throw new \InvalidArgumentException("The platform URL is not defined, please check Be2Bill module configuration.");
        }

        try {
            // save the method used
            self::setOrderMethod($order->getId(), $paymentMethod, $be2billParams);

            // PayPal method
            if (self::METHOD_PAYPAL === $paymentMethod) {
                return $this->generatePayPalResponse($order, $be2billParams);
            }

            // classical method
            return $this->generateGatewayFormResponse(
                $order,
                "https://" . Be2billConfigQuery::read('url') . self::URL_PAYMENT_FORM,
                $be2billParams
            );

            // throw new \InvalidArgumentException("This is not a valid payment method for be2bill module.");

        } catch (\Exception $ex) {
            self::getLogger()->error(
                "be2bill pay method failed {code} : {message}",
                [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage()
                ]
            );

            return new \Thelia\Core\HttpFoundation\Response(
                '',
                302,
                [
                    'location' => $this->getPaymentFailurePageUrl(
                        $order->getId(),
                        Translator::getInstance()->trans(
                            "Sorry, something did not worked with be2bill. Please try again, or use another payment type",
                            [],
                            self::MODULE_DOMAIN
                        )
                    )
                ]
            );
        }

    }

    protected function generatePayPalResponse(Order $order, array $params)
    {
        $resource = curl_init();

        if (false === $platformUrl = Be2billConfigQuery::read('url', false)) {
            throw new \InvalidArgumentException(
                "The PayPal platform URL is not defined, please check be2bill module configuration."
            );
        }

        $requestParams = [
            'method' => 'payment',
            'params' => $params
        ];

        curl_setopt($resource, CURLOPT_URL, "https://" . $platformUrl . self::URL_SERVER_TO_SERVER);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_POSTFIELDS, http_build_query($requestParams));

        $serialized = curl_exec($resource);

        if ($serialized !== false) {
            $result = json_decode($serialized, true);

            if ($result['EXECCODE'] == '0002') {
                return new Response(base64_decode($result['REDIRECTHTML']));
            }

            throw new \RuntimeException(
                sprintf("The be2bill paypal method failed [%s] : %s", $result['EXECCODE'], $result['MESSAGE'])
            );
        }

        throw new \RuntimeException("The be2bill paypal method failed");
    }

    public function getBe2BillParameters(Order $order, $method = "", $operationType = 'payment')
    {
        $amount = floor(($order->getTotalAmount() * 100));

        $customer = $order->getCustomer();

        $invoiceAddress = $order->getOrderAddressRelatedByInvoiceOrderAddressId();

        $be2billParams = array(
            'AMOUNT' => $amount,
            'CLIENTADDRESS' => trim($invoiceAddress->getAddress1() . ' ' . $invoiceAddress->getAddress2() . ' ' . $invoiceAddress->getAddress3()),
            'CLIENTIDENT' => $customer->getId(),
            'DESCRIPTION' => Be2billConfigQuery::read('description'),
            'ORDERID' => $order->getId(),
            'VERSION' => '2.0',
            'OPERATIONTYPE' => $operationType,
            'LANGUAGE' => strtoupper($order->getLang()->getCode()),
        );

        if ("" === $method) {
            $be2billParams['IDENTIFIER'] = Be2billConfigQuery::read('identifier');

            $be2billParams['3DSECURE'] = Be2billConfigQuery::read('3dsecure');
        }

        if (self::METHOD_NTIMES === $method) {
            $be2billParams['IDENTIFIER'] = Be2billConfigQuery::read('identifier');
            $be2billParams['3DSECURE'] = Be2billConfigQuery::read('3dsecure');

            // compute amounts
            unset($be2billParams['AMOUNT']);
            $amounts = self::getNTimesDates($order->getTotalAmount());
            foreach ($amounts as $amount) {
                $key = sprintf('AMOUNTS[%s]', $amount[0]->format('Y-m-d'));
                $be2billParams[$key] = $amount[1];
            }
        }

        if (self::METHOD_PAYPAL === $method) {
            $be2billParams['IDENTIFIER'] = Be2billConfigQuery::read('paypal-identifier');

            $be2billParams['CLIENTEMAIL'] = $customer->getEmail();
            $be2billParams['CLIENTIP'] = $this->getRequest()->getClientIp();
            $be2billParams['CLIENTUSERAGENT'] = $this->getRequest()->server->get('HTTP_USER_AGENT');
        }

        // dispatch event to change parameters if needed
        $event = new PaymentParametersEvent($order, $method, $be2billParams);
        $this->getDispatcher()->dispatch(
            Be2BillEvents::BE2BILL_PAYMENT_PARAMETERS,
            $event
        );
        $be2billParams = $event->getParameters();

        $be2billParams['HASH'] = self::be2BillHash($be2billParams, $method);

        return $be2billParams;
    }

    public static function be2BillHash(array $params, $methodName = '')
    {
        ksort($params);

        if (null === $password = Be2billConfigQuery::read($methodName . '-password', null)) {
            $password = Be2billConfigQuery::read('password', null);
        }

        $clearString = $password;

        foreach ($params as $key => $value) {
            if (is_array($value) == true) {
                ksort($value);
                foreach ($value as $index => $val) {
                    $clearString .= $key . '[' . $index . ']=' . $val . $password;
                }
            } else {
                $clearString .= $key . '=' . $value . $password;
            }
        }

        return hash('sha256', $clearString);
    }

    /**
     * Compute date and amount of every payments
     *
     * @param Order $order
     */
    public static function getNTimesDates($amount, $count = 3, $interval = 'P1M')
    {
        $amounts = [];

        $paymentCount = intval(Be2billConfigQuery::read('ntimes-count', $count));
        $paymentInterval = Be2billConfigQuery::read('ntimes-interval', $interval);

        $amount = round($amount * 100);

        // amounts
        $recurrentAmount = floor($amount / $paymentCount);
        $firstAmount = $amount - ($recurrentAmount * ($paymentCount - 1));


        // dates
        $dateStart = new \DateTime('now');
        $dateInterval = new \DateInterval($paymentInterval);
        $period = new \DatePeriod($dateStart, $dateInterval, $paymentCount - 1);

        foreach ($period as $date) {
            $amounts[] = [
                $date,
                $recurrentAmount
            ];
        }
        $amounts[0][1] = $firstAmount;

        return $amounts;
    }


    /**
     *
     * This method is call on Payment loop.
     *
     * If you return true, the payment method will de display
     * If you return false, the payment method will not be display
     *
     * @return boolean
     */
    public function isValidPayment()
    {
        return 'yes' === Be2billConfigQuery::read('activated', 'yes');
    }


    private function initializeConfig()
    {
        if (null === Be2billConfigQuery::read('activated')) {
            Be2billConfigQuery::set('activated', 'yes');
        }

        if (null === Be2billConfigQuery::read('3dsecure')) {
            Be2billConfigQuery::set('3dsecure', 'no');
        }

        if (null === Be2billConfigQuery::read('paypal')) {
            Be2billConfigQuery::set('paypal', 'no');
        }
    }

    public static function setOrderMethod($orderId, $methodName, $parameters)
    {
        if (null === $method = Be2billMethodQuery::create()->findOneByOrderId($orderId)) {
            $method = new Be2billMethod();
            $method->setOrderId($orderId);
        }

        $config = Be2billConfigQuery::dump(['password', 'paypal-password']);

        $data = [
            "parameters" => $parameters,
            "config" => $config
        ];

        $method->setData($data);
        $method->setMethod($methodName);
        $method->save();
    }

    public static function getOrderMethod($orderId, $default = '')
    {
        if (null !== $method = Be2billMethodQuery::create()->findOneByOrderId($orderId)) {
            return $method->getMethod();
        }

        return $default;
    }

    /**
     *
     * @param $methodName
     * @return string
     */
    public static function getMethodTitle($methodName)
    {
        // ->trans('paypal')
        // ->trans('ntimes')
        return Translator::getInstance()->trans($methodName, [], self::MODULE_DOMAIN);
    }


    public static function getLogFilePath()
    {
        $logFilePath = sprintf(THELIA_ROOT."log".DS."%s.log", strtolower(self::getModuleCode()));

        return $logFilePath;
    }
    /**
     * @return Tlog
     */
    public static function getLogger()
    {
        if (self::$logger == null) {
            self::$logger = Tlog::getNewInstance();

            // same as the log in payment controller
            $logFilePath = self::getLogFilePath();

            self::$logger->setPrefix("#LEVEL: #DATE #HOUR: ");
            self::$logger->setDestinations("\\Thelia\\Log\\Destination\\TlogDestinationRotatingFile");
            self::$logger->setConfig("\\Thelia\\Log\\Destination\\TlogDestinationRotatingFile", 0, $logFilePath);
            self::$logger->setLevel(Tlog::INFO);
        }

        return self::$logger;
    }

}
