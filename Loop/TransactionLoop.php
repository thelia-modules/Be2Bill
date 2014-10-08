<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 08/10/2014
 * Time: 10:39
 */

namespace Be2Bill\Loop;


use Be2Bill\Model\Be2billTransactionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class TransactionLoop extends BaseLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \Be2Bill\Model\Be2BillTransaction $transaction */
        foreach ($loopResult->getResultDataCollection() as $transaction) {

            $loopResultRow = new LoopResultRow($transaction);

            $customer = $transaction->getCustomer()->getLastname().' '.$transaction->getCustomer()->getFirstname();

            $loopResultRow->set('ORDERID', $transaction->getOrderId())
                ->set('DATE', $transaction->getCreatedAt('d/m/Y H:i:s'))
                ->set('TRANSACTIONID', $transaction->getTransactionId())
                ->set('AMOUNT', $transaction->getAmount())
                ->set('CUSTOMERID', $transaction->getCustomerId())
                ->set('CUSTOMER', $customer)
                ->set('CUSTOMEREMAIL', $transaction->getClientemail())
                ->set('REFUNDED', $transaction->getRefunded())
                ->set('REFUNDEDBY', $transaction->getRefundedby());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('interval'),
            Argument::createAnyTypeArgument('date')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $be2BillTransactionQuery = new Be2billTransactionQuery();
        if (null == $this->getInterval()) {
            $be2BillTransactionQuery->recentlyCreated()->orderByCreatedAt(Criteria::DESC);
        }

        return $be2BillTransactionQuery;
    }
}