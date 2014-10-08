<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 07/10/2014
 * Time: 10:59
 */

namespace Be2Bill\Controller\admin;


use Be2Bill\Model\Be2billTransaction;
use Be2Bill\Model\Be2billTransactionQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Admin\BaseAdminController;

class Be2BillController extends BaseAdminController
{

    public function listAjaxTransaction()
    {
        $request = $this->getRequest()->query;

        $startDate = date('Y-m-d', strtotime($request->get('transaction-date')));
        $endDate = date('Y-m-d', strtotime($startDate.' '.'+ '.$request->get('transaction-interval').' days'));

        $transactionQuery = Be2billTransactionQuery::create();
        $transactions = $transactionQuery->filterByCreatedAt(array('min' => $startDate, 'max' => $endDate))->find();

        return $this->jsonResponse($transactions);

    }

    public function refundTransaction()
    {

    }
}
