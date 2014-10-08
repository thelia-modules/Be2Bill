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


use Be2Bill\Model\Be2billConfigQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleImageQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Module\BaseModule;

class Be2Bill extends AbstractPaymentModule
{

    const CONFIRMATION_MESSAGE_NAME = 'be2bill_payment_confirmation';


    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, array(
            __DIR__ . DS . 'Config'.DS.'thelia.sql'
        ));

        // Create payment confirmation message from templates, if not already defined
        $email_templates_dir = __DIR__.DS.'I18n'.DS.'email-templates'.DS;

        if (null == MessageQuery::create()->findOneByName(self::CONFIRMATION_MESSAGE_NAME)) {

            $message = new Message();

            $message->setName(self::CONFIRMATION_MESSAGE_NAME)

                ->setLocale('en_US')
                ->setTitle('Be2Bill payment confirmation')
                ->setSubject('Payment of order {$order_ref}')
                ->setHtmlMessage(file_get_contents($email_templates_dir.'en.html'))
                ->setTextMessage(file_get_contents($email_templates_dir.'en.txt'))

                ->setLocale('fr_FR')
                ->setTitle('Confirmation de paiement par Be2Bill')
                ->setSubject('Confirmation du paiement de votre commande {$order_ref}')
                ->setHtmlMessage(file_get_contents($email_templates_dir.'fr.html'))
                ->setTextMessage(file_get_contents($email_templates_dir.'fr.txt'))

                ->save();
        }

        $module = $this->getModuleModel();

        if (ModuleImageQuery::create()->filterByModule($module)->count() == 0) {
            $this->deployImageFolder($module, sprintf('%s/images', __DIR__), $con);
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
        $be2bill_params = $this->getBe2BillParameters($order, 'payment');
        $be2bill_params['HASH'] = $this->be2BillHash($be2bill_params);


        if (false === $platformUrl = Be2billConfigQuery::read('url', false)) {
            throw new \InvalidArgumentException("The platform URL is not defined, please check Be2Bill module configuration.");
        }

        return $this->generateGatewayFormResponse($order, $platformUrl, $be2bill_params);

    }

    public function getBe2BillParameters(Order $order, $operationtype)
    {

        $amount = round($order->getTotalAmount(), 2)*100;


        $customer = $order->getCustomer();

        $invoiceAddress = $order->getOrderAddressRelatedByInvoiceOrderAddressId();

        $be2bill_params = array(
            'AMOUNT'        => $amount,
            'CLIENTADDRESS' => trim($invoiceAddress->getAddress1() . ' ' . $invoiceAddress->getAddress2() . ' ' . $invoiceAddress->getAddress3()),
            'CLIENTIDENT'   => $customer->getId(),
            'DESCRIPTION'   => 'Commande Be2Bill',
            'IDENTIFIER'    => Be2billConfigQuery::read('identifier'),
            'ORDERID'       => $order->getId(),
            'VERSION'       => '2.0',
            'OPERATIONTYPE' => 'payment',
            '3DSECURE'      => Be2billConfigQuery::read('3dsecure')
        );

        return $be2bill_params;
    }

    public static function be2BillHash(array $params)
    {

        ksort($params);

        $password = Be2billConfigQuery::read('password');
        $clear_string = $password;

        foreach ($params as $key => $value) {
            $clear_string .= $key . '=' . trim($value) . $password;
        }

        return hash('sha256', $clear_string);

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
        return true;
    }
}
