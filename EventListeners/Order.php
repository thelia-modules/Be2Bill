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


namespace Be2Bill\EventListeners;

use Be2Bill\Be2Bill;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;

/**
 * Class Order
 * @package Be2Bill\EventListeners
 * @author Julien Chanséaume <julien@thelia.net>
 */
class Order implements EventSubscriberInterface
{
    /** @var Request null  */
    protected $request = null;

    /**
     * Order constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setPaymentModule(OrderEvent $event)
    {
        if ($event->getPaymentModule() === Be2Bill::getModuleId()) {
            // check if Paypal method is selected, or something else
            $be2billMethod = $this->request->get('be2bill-method', '');
            $this->request->getSession()->set('be2bill-method', $be2billMethod);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_SET_PAYMENT_MODULE => ['setPaymentModule', 128]
        ];
    }
}
