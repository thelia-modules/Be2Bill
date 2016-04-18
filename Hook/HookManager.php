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


namespace Be2Bill\Hook;

use Be2Bill\Be2Bill;
use Be2Bill\Model\Be2billConfigQuery;
use Be2Bill\Model\Be2billMethodQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\AddressQuery;

/**
 * Class HookManager
 * @package Be2Bill\Hook
 * @author Julien Chanséaume <julien@thelia.net>
 */
class HookManager extends BaseHook
{
    const MAX_TRACE_SIZE = 1000;

    public function onOrderInvoice(HookRenderEvent $event)
    {
        if ('yes' === Be2billConfigQuery::read('activated')) {

            $methodName = $this->getSession()->get('be2bill-method');

            $data = [
                'module_id' => Be2Bill::getModuleId(),
                'paypal' => ('yes' === Be2billConfigQuery::read('paypal')),
                'value' => $methodName,
            ];

            if ('yes' === Be2billConfigQuery::read('ntimes')) {
                $data['ntimes'] = true;
                $amount = $this->getCartAmount();

                $amounts = Be2Bill::getNTimesDates($amount);
                $data['ntimes_dates'] = [];
                foreach ($amounts as $amount) {
                    $data['ntimes_dates'][] = [
                        'date' => $amount[0],
                        'amount' => $amount[1] / 100
                    ];
                }
            }

            $render = $this->render(
                'order-invoice.html',
                $data
            );

            $event->add($render);
        }

    }

    public function onOrderInvoiceJavascriptInitialization(HookRenderEvent $event)
    {
        if ('yes' === Be2billConfigQuery::read('activated') && 'yes' === Be2billConfigQuery::read('paypal')) {

            $render = $this->render(
                'order-invoice-js.html',
                [
                    'module_id' => Be2Bill::getModuleId(),
                ]
            );

            $event->add($render);
        }

    }


    public function onOrderEditPaymentModuleBottom(HookRenderEvent $event)
    {
        $templateData = $event->getArguments();

        // get extra data
        $method = Be2billMethodQuery::create()->findOneByOrderId($event->getArgument('order_id'));
        if (null !== $method) {
            $data = $method->getData();
            $templateData["params"] = $data;
        } else {
            $templateData["params"] = [];
        }

        $event->add(
            $this->render(
                'payment-information.html',
                $templateData
            )
        );
    }

    /**
     * Retrieve the amount of the cart with taxes, postage, discount,
     * (no direct access)
     *
     * @return float
     */
    protected function getCartAmount()
    {
        $currentDeliveryAddress = AddressQuery::create()->findPk(
            $this->getOrder()->getChoosenDeliveryAddress()
        );

        $amount = $this->getCart()->getTaxedAmount(
            $currentDeliveryAddress->getCountry(),
            true,
            $currentDeliveryAddress->getState()
        );

        $amount += (float)$this->getOrder()->getPostage();

        return $amount;
    }

    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $module_id = self::getModule()->getModuleId();

        $logFilePath = Be2Bill::getLogFilePath();

        $traces = @file_get_contents($logFilePath);

        if (false === $traces) {
            $traces = $this->translator->trans("The log file doesn't exists yet.", [], Be2Bill::MODULE_DOMAIN);
        } elseif (empty($traces)) {
            $traces = $this->translator->trans("The log file is empty.", [], Be2Bill::MODULE_DOMAIN);
        } else {
            // Limit to 1MO
            $traces = array_reverse(explode("\n", $traces));

            if (count($traces) > self::MAX_TRACE_SIZE) {
                $traces = array_slice($traces, 0, self::MAX_TRACE_SIZE);

                $traces[] = $this->translator->trans(
                    "(Previous log is in %file file.)\n",
                    ['%file' => $logFilePath],
                    Be2Bill::MODULE_DOMAIN
                );
            }

            $traces = implode("\n", $traces);
        }

        $event->add(
            $this->render(
                "module_configuration.html",
                [
                    'module_id' => $module_id,
                    'trace_content' => $traces
                ]
            )
        );
    }

    public function onModuleConfigJs(HookRenderEvent $event)
    {
        $module_id = self::getModule()->getModuleId();

        $event->add($this->render("module-config-js.html", ['module_id' => $module_id]));
    }
}
