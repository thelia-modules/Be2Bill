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
}
