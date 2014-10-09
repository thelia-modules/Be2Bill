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
use Be2Bill\Model\Be2billTransaction;
use Be2Bill\Model\Be2billTransactionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Controller\Admin\BaseAdminController;

class Be2BillController extends BaseAdminController
{

    public function listAjaxTransaction()
    {
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
        $request = $this->getRequest()->request;
        $transaction_id = $request->get('transaction-id');
        $order_id = $request->get('order-id');
        $amount = $request->get('transaction-amount')*100;

        $params = array(
            'method' => 'refund',
            'params' => array(
                'DESCRIPTION' => 'Be2Bnd',
                'IDENTIFIER' => Be2billConfigQuery::read('identifier'),
                'OPERATIONTYPE' => 'refund',
                'ORDERID' => $order_id,
                'TRANSACTIONID' => $transaction_id,
                'VERSION' => '2.0',
            )
        );

        $params['params']['HASH'] = "azdfzsdefsfqsfsqdfqdsfdqqg";

        $resource =curl_init();

        curl_setopt($resource, CURLOPT_URL, Be2billConfigQuery::read('url', false));
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_POSTFIELDS, http_build_query($params['params']));

        $serialized = curl_exec($resource);

        if( $serialized !== false ) {
            $result = json_decode( $serialized,true );
            var_dump($serialized);
            // 3DS
            if( $result['EXECCODE'] == '0001' )
            {
                echo base64_decode( $result['3DSECUREHTML'] );
            }
            // Wallet
            elseif( $result['EXECCODE'] == '0002' )
            {
                echo base64_decode( $result['REDIRECTHTML'] );
            }
            else
            {
                if( $result['EXECCODE'] == '0000' )
                {
                    var_dump($result);
                }
                else
                {
                    var_dump($result);
                }
            }
        }

    }
}
