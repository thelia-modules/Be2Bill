<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 07/10/2014
 * Time: 10:59
 */

namespace Be2Bill\Controller\admin;

use Be2Bill\Be2Bill;
use Be2Bill\Model\Be2billConfigQuery;
use Be2Bill\Model\Be2billTransactionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;

class Be2BillController extends BaseAdminController
{
    public function listAjaxTransaction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Be2Bill', AccessManager::VIEW)) {
            return $response;
        }

        $request = $this->getRequest()->query;

        $startDate = date('Y-m-d', strtotime($request->get('transaction-date')));
        $endDate = date('Y-m-d', strtotime($startDate.' '.'+ '.$request->get('transaction-interval').' days'));

        $transactionQuery = Be2billTransactionQuery::create()
            ->leftJoinCustomer()
            ->withColumn('Customer.firstname', 'firstname')
            ->withColumn('Customer.lastname', 'lastname');

        $transactions = $transactionQuery->filterByCreatedAt(array('min' => $startDate, 'max' => $endDate))->orderByCreatedAt(Criteria::DESC)->find()->toJSON(false, true);

        return $this->jsonResponse($transactions);
    }

    public function refundTransaction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Be2Bill', AccessManager::UPDATE)) {
            return $response;
        }

        $request = $this->getRequest()->request;
        $transaction_id = $request->get('transaction-id');
        $order_id = $request->get('order-id');

        $translator = Translator::getInstance();

        $params = array(
            'method' => 'refund',
            'params' => array(
                'DESCRIPTION' => 'Remboursement Be 2 Bill',
                'IDENTIFIER' => Be2billConfigQuery::read('identifier'),
                'OPERATIONTYPE' => 'refund',
                'ORDERID' => $order_id,
                'TRANSACTIONID' => $transaction_id,
                'VERSION' => '2.0',
            )
        );
        $params['params']['HASH'] = Be2Bill::be2BillHash($params['params']);

        $resource =curl_init();

        curl_setopt($resource, CURLOPT_URL, "https://".Be2billConfigQuery::read('url').Be2Bill::URL_SERVER_TO_SERVER);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_POSTFIELDS, http_build_query($params));

        $serialized = curl_exec($resource);

        if ($serialized !== false) {
            $result = json_decode($serialized, true);

            if ($result['EXECCODE'] == '0000') {
                $admin = $this->getSecurityContext()->getAdminUser()->getUsername();
                $transaction = Be2billTransactionQuery::create()->findOneByTransactionId($transaction_id);
                $transaction->setRefunded(true);
                $transaction->setRefundedby($admin);

                $transaction->save();

                return $this->jsonResponse(json_encode(['orderId'=>$order_id, 'admin'=>$admin]));
            } else {
                return $this->jsonResponse(json_encode($result['MESSAGE']), 500);
            }
        } else {
            return $this->jsonResponse(json_encode($translator->trans('La commande n&#176; %orderId n\'as pas pu être remboursée.', ['%orderId' => $order_id], Be2Bill::MODULE_DOMAIN)), 500);
        }
    }
}
