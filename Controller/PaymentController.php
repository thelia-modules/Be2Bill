<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 03/10/2014
 * Time: 10:11
 */

namespace Be2Bill\Controller;

use Be2Bill\Be2Bill;
use Be2Bill\Model\Be2billTransaction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Model\OrderStatusQuery;
use Thelia\Module\BasePaymentModuleController;

class PaymentController extends BasePaymentModuleController
{
    /**
     * Return a module identifier used to calculate the name of the log file,
     * and in the log messages.
     *
     * @return string the module code
     */
    protected function getModuleCode()
    {
        return "Be2Bill";
    }

    public function processBe2BillRequest()
    {
        $request = $this->getRequest()->request;

        // Récupération du hash recu par Be2Bill
        $be2BillHash = $request->get('HASH');

        // Création d'un hash a partir des paramétres reçus
        $request->remove('HASH');
        $params = $request->all();
        $orderId = intval($request->get('ORDERID'));

        // retrieve the method used to pay
        $methodName = Be2Bill::getOrderMethod($orderId);

        $hash = Be2Bill::be2BillHash($params, $methodName);

        $this
            ->getLog()
            ->addInfo(
                $this->getTranslator()->trans(
                    "Be2Bill platform request received for order ID %id.",
                    array('%id' => $orderId),
                    Be2Bill::MODULE_DOMAIN
                )
            )
        ;

        if (null !== $order = $this->getOrder($orderId)) {
            // Check the authencity of the request
            if ($be2BillHash == $hash) {
                // Payment was accepted
                if ($request->get('EXECCODE') == 0000) {

                    if ($order->isPaid()) {
                        $this->getLog()->addInfo(
                            $this->getTranslator()->trans(
                                "Order ID %id is already paid.",
                                array('%id' => $orderId),
                                Be2Bill::MODULE_DOMAIN
                            )
                        );
                    } else {
                        $this->getLog()->addInfo(
                            $this->getTranslator()->trans(
                                "Order ID %id payment was successful.",
                                array('%id' => $orderId),
                                Be2Bill::MODULE_DOMAIN
                            )
                        );

                        $this->confirmPayment($orderId);

                        // save the transaction ref
                        $order->setTransactionRef($request->get('TRANSACTIONID'));
                        $order->save();
                    }

                } else {

                    $this->getLog()->addError(
                        $this
                            ->getTranslator()
                            ->trans(
                                "Transaction %transaction for order ID %id failed. [%code] : %message",
                                [
                                    '%transaction' => $request->get('TRANSACTIONID'),
                                    '%id' => $orderId,
                                    '%code' => $request->get('EXECCODE'),
                                    '%message' => $request->get('MESSAGE'),
                                ],
                                Be2Bill::MODULE_DOMAIN
                            )
                    );

                    if ($request->get('EXECCODE') == 4004) {
                        if (!$order->isCancelled() && !$order->isPaid()) {
                            $this->getLog()->addInfo(
                                $this->getTranslator()->trans(
                                    "Cancelling order ID %id - payment failed.",
                                    array('%id' => $orderId),
                                    Be2Bill::MODULE_DOMAIN
                                )
                            );

                            $this->cancelPayment($orderId);
                        }
                    }
                }

                // save the transaction
                $this->getLog()->addInfo(
                    $this->getTranslator()->trans(
                        "Saving transaction %transaction for order ID %id.",
                        [
                            '%transaction' => $request->get('TRANSACTIONID'),
                            '%id' => $orderId
                        ],
                        Be2Bill::MODULE_DOMAIN
                    )
                );

                $transaction = new Be2billTransaction();
                $transaction->setCustomerId($request->get('CLIENTIDENT'))
                    ->setOrderId($request->get('ORDERID'))
                    ->setTransactionId($request->get('TRANSACTIONID'))
                    ->setMethodName($methodName)
                    ->setOperationtype($request->get('OPERATIONTYPE'))
                    ->setDsecure($request->get('3DSECURE'))
                    ->setExeccode($request->get('EXECCODE'))
                    ->setMessage($request->get('MESSAGE'))
                    ->setAmount($request->get('AMOUNT') / 100)
                    ->setClientemail($request->get('CLIENTEMAIL'))
                    ->setCardcode($request->get('CARDCODE'))
                    ->setCardvaliditydate($request->get('CARDVALIDITYDATE'))
                    ->setCardfullname($request->get('CARDFULLNAME'))
                    ->setCardtype($request->get('CARDTYPE'))
                    ->setTransaction(json_encode($params))
                ;

                $transaction->save();

                return Response::create('OK');
            } else {
                $this->getLog()->addError(
                    $this->getTranslator()
                        ->trans("Response could not be authentified.", array(), Be2Bill::MODULE_DOMAIN)
                );
            }
        }

        return Response::create('ERROR');
    }

    public function redirectBe2BillRequest()
    {
        $request = $this->getRequest()->query;

        $be2BillHash = $request->get('HASH');

        $request->remove('HASH');
        $params = $request->all();
        $methodName = Be2Bill::getOrderMethod($request->get('ORDERID'));

        $hash = Be2Bill::be2BillHash($params, $methodName);

        if ($be2BillHash == $hash) {
            if ($request->get('EXECCODE') == 0000) {
                $this->redirectToSuccessPage($params['ORDERID']);
            } else {
                $message = $this->getTranslator()->trans(
                    'Erreur n° %code : %message',
                    array(
                        '%code' => $params['EXECCODE'],
                        '%message' => $params['MESSAGE']
                    ),
                    Be2Bill::MODULE_DOMAIN
                )
                ;
                $this->redirectToFailurePage($params['ORDERID'], $message);
            }
        }
    }

    public function redirectBe2BillCancel()
    {
        $request = $this->getRequest()->query;

        //$this->cancelPayment($request->get('ORDERID'));

        $this->redirectToFailurePage(
            $request->get('ORDERID'),
            $this->getTranslator()->trans(
                'Votre commande n° %commande a bien était annulé.',
                array('%commande' => $request->get('ORDERID')),
                Be2Bill::MODULE_DOMAIN
            )
        );
    }

    /**
     * Patch for buggy Thelia method
     *
     * @param int $order_id
     */
    public function cancelPayment($order_id)
    {
        $order_id = intval($order_id);

        if (null !== $order = $this->getOrder($order_id)) {
            $this->getLog()->addInfo(
                $this->getTranslator()->trans(
                    "Processing cancelation of payment for order ref. %ref",
                    array('%ref' => $order->getRef())
                )
            );

            $event = new OrderEvent($order);

            $event->setStatus(OrderStatusQuery::getNotPaidStatus()->getId());

            $this->getLog()->addInfo(
                $this->getTranslator()->trans(
                    "Order ref. %ref is now unpaid.",
                    array('%ref' => $order->getRef())
                )
            );

            $this->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $event);
        }
    }
}
