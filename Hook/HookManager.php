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
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

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

            $render = $this->render(
                'order-invoice.html',
                [
                    'module_id' => Be2Bill::getModuleId(),
                    'paypal' => ('yes' === Be2billConfigQuery::read('paypal')),
                    'value' => $methodName,
                ]
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
        $event->add(
            $this->render(
                'payment-information.html',
                $event->getArguments()
            )
        );
    }
}
