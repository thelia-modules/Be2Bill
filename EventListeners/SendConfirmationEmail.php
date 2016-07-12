<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 03/10/2014
 * Time: 12:22
 */

namespace Be2Bill\EventListeners;

use Be2Bill\Be2Bill;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\MessageQuery;

class SendConfirmationEmail extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    public function updateOrderStatus(OrderEvent $event)
    {
        $be2bill = new Be2Bill();

        if ($event->getOrder()->isPaid() && $be2bill->isPaymentModuleFor($event->getOrder())) {
            if (null != $message = MessageQuery::create()
                    ->filterByName(Be2Bill::CONFIRMATION_MESSAGE_NAME)
                    ->findOne()
            ) {

                $this->getMailer()->sendEmailToCustomer(
                    Be2Bill::CONFIRMATION_MESSAGE_NAME,
                    $event->getOrder()->getCustomer(),
                    [
                        'order_id' => $event->getOrder()->getId(),
                        'order_ref' => $event->getOrder()->getRef()
                    ]
                );

            } else {
                throw new \Exception(sprintf("Failed to load message '%s'.", Be2Bill::CONFIRMATION_MESSAGE_NAME));
            }
        } else {
            Tlog::getInstance()->debug("No confirmation email sent (order not paid, or not the proper payment module.");
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("updateOrderStatus", 128)
        );
    }
}
