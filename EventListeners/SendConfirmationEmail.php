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
use Thelia\Core\Template\ParserInterface;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\Base\ConfigQuery;
use Thelia\Model\MessageQuery;

class SendConfirmationEmail extends BaseAction implements EventSubscriberInterface
{

    /**
     * @var MailerFactory
     */
    protected $mailer;

    /**
     * @var ParserInterface
     */
    protected $parser;

    public function __construct(ParserInterface $parser, MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    public function updateOrderStatus(OrderEvent $event)
    {
        $be2bill = new Be2Bill();

        if ($event->getOrder()->isPaid() && $be2bill->isPaymentModuleFor($event->getOrder())) {
            $contact_email = \Thelia\Model\ConfigQuery::read('store_email', false);

            Tlog::getInstance()->debug("Sending confirmation email from store contact e-mail $contact_email");

            if ($contact_email) {
                $message = MessageQuery::create()
                    ->filterByName(Be2Bill::CONFIRMATION_MESSAGE_NAME)
                    ->findOne();

                if (false === $message) {
                    throw new \Exception(sprintf("Failed to load message '%s'.", Be2Bill::CONFIRMATION_MESSAGE_NAME));
                }

                $order = $event->getOrder();
                $customer = $order->getCustomer();

                $this->parser->assign('order_id', $order->getId());
                $this->parser->assign('order_ref', $order->getRef());

                $message->setLocale($order->getLang()->getLocale());

                $instance = \Swift_Message::newInstance()
                            ->addTo($customer->getEmail(), $customer->getFirstname()." ".$customer->getLastname())
                            ->addFrom($contact_email, \Thelia\Model\ConfigQuery::read('store_name'))
                ;

                $message->buildMessage($this->parser, $instance);

                $this->getMailer()->send($instance);

                Tlog::getInstance()->debug("Confirmation email sent to customer ".$customer->getEmail());
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