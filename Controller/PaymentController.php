<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 03/10/2014
 * Time: 10:11
 */

namespace Be2Bill\Controller;


use Be2Bill\Be2Bill;
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

    public function processBe2BillRequest($type)
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

                if ($type == 'notif') {

                    // Payment was accepted
                    if ($request->get('EXECCODE') === 0000) {

                        if ($order->isPaid()) {
                            $this->getLog()->addInfo($this->getTranslator()->trans("Order ID %id is already paid.", array('%id' => $order_id)));

                        } else {
                            $this->getLog()->addInfo($this->getTranslator()->trans("Order ID %id payment was succesful.", array('%id' => $order_id)));

                            $this->confirmPayment($order_id);

                        }

                    // Payment was canceled
                    } elseif ($request->get('EXECCODE') === 4004) {

                        $this->cancelPayment($order_id);

                    // Payment was not accepted
                    } else {

                        $this->getLog()->addError($this->getTranslator()->trans("Order ID %id payment failed.", array('%id' => $order_id)));

                    }
                }
                echo 'OK';

            } else {
                $this->getLog()->addError($this->getTranslator()->trans("Response could not be authentified."));
                echo 'ERROR';
            }
        }
    }

    public function redirectBe2BillRequest($type)
    {
        $request = $this->getRequest()->query;

        $be2BillHash = $request->get('HASH');

        $request->remove('HASH');
        $params = $request->all();
        $hash = Be2Bill::be2BillHash($params);


        if ($be2BillHash == $hash) {
            if ($type == 'success') {
                $this->redirectToSuccessPage($params['ORDERID']);
            } else {
                $message = $this->getTranslator()->trans('Error n° %code', array('%code' => $params['EXECCODE']));
                $this->redirectToFailurePage($params['ORDERID'], $message);
            }
        }

    }

}