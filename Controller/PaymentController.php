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
use Be2Bill\Model\Be2billTransactionQuery;
use Thelia\Module\BasePaymentModuleController;
use Thelia\Core\HttpFoundation\Response;

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

        //Récupération du hash recu par Be2Bill
        $be2BillHash = $request->get('HASH');

        //Création d'un hash a partir des paramétres reçus
        $request->remove('HASH');
        $params = $request->all();
        $hash = Be2Bill::be2BillHash($params);

        $order_id = intval($request->get('ORDERID'));

        $this->getLog()->addInfo($this->getTranslator()->trans("Be2Bill platform request received for order ID %id.", array('%id' => $order_id)));

        if (null !== $order = $this->getOrder($order_id)) {

            //Check the authencity of the request
            if ($be2BillHash == $hash) {

                // Payment was accepted
                if ($request->get('EXECCODE') == 0000) {

                    if ($order->isPaid()) {
                        $this->getLog()->addInfo($this->getTranslator()->trans("Order ID %id is already paid.", array('%id' => $order_id)));

                    } else {
                        $this->getLog()->addInfo($this->getTranslator()->trans("Order ID %id payment was succesful.", array('%id' => $order_id)));

                        $this->confirmPayment($order_id);

                        $transaction = new Be2billTransaction();
                        $transaction->setCustomerId($request->get('CLIENTIDENT'))
                            ->setOrderId($request->get('ORDERID'))
                            ->setTransactionId($request->get('TRANSACTIONID'))
                            ->setOperationtype($request->get('OPERATIONTYPE'))
                            ->setDsecure($request->get('3DSECURE'))
                            ->setExeccode($request->get('EXECCODE'))
                            ->setMessage($request->get('MESSAGE'))
                            ->setAmount($request->get('AMOUNT')/100)
                            ->setClientemail($request->get('CLIENTEMAIL'))
                            ->setCardcode($request->get('CARDCODE'))
                            ->setCardvaliditydate($request->get('CARDVALIDITYDATE'))
                            ->setCardfullname($request->get('CARDFULLNAME'))
                            ->setCardtype($request->get('CARDTYPE'));

                        $transaction->save();

                    }

                // Payment was canceled
                } elseif ($request->get('EXECCODE') == 4004) {

                    $this->cancelPayment($order_id);

                // Payment was not accepted
                } else {
                    $this->getLog()->addError($this->getTranslator()->trans("Order ID %id payment failed.", array('%id' => $order_id)));
                }
                return Response::create('OK');

            } else {
                $this->getLog()->addError($this->getTranslator()->trans("Response could not be authentified."));
                return Response::create('ERROR');
            }
        }
    }

    public function redirectBe2BillRequest()
    {
        $request = $this->getRequest()->query;

        $be2BillHash = $request->get('HASH');

        $request->remove('HASH');
        $params = $request->all();
        $hash = Be2Bill::be2BillHash($params);


        if ($be2BillHash == $hash) {
            if ($request->get('EXECCODE') == 0000) {
                $this->redirectToSuccessPage($params['ORDERID']);
            } else {
                $message = $this->getTranslator()->trans('Error n° %code : %message', array('%code' => $params['EXECCODE'], '%message' => $params['MESSAGE']));
                $this->redirectToFailurePage($params['ORDERID'], $message);
            }
        }

    }

    public function redirectBe2BillCancel()
    {
        $request = $this->getRequest()->query;

        $this->cancelPayment($request->get('ORDERID'));
    }



}
