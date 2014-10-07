<?php

namespace Be2Bill\Model\Base;

use \Exception;
use \PDO;
use Be2Bill\Model\Be2billTransaction as ChildBe2billTransaction;
use Be2Bill\Model\Be2billTransactionQuery as ChildBe2billTransactionQuery;
use Be2Bill\Model\Map\Be2billTransactionTableMap;
use Be2Bill\Model\Thelia\Model\Customer;
use Be2Bill\Model\Thelia\Model\Order;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'be2bill_transaction' table.
 *
 *
 *
 * @method     ChildBe2billTransactionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildBe2billTransactionQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildBe2billTransactionQuery orderByCustomerId($order = Criteria::ASC) Order by the customer_id column
 * @method     ChildBe2billTransactionQuery orderByTransactionId($order = Criteria::ASC) Order by the transaction_id column
 * @method     ChildBe2billTransactionQuery orderByOperationtype($order = Criteria::ASC) Order by the operationtype column
 * @method     ChildBe2billTransactionQuery orderByDsecure($order = Criteria::ASC) Order by the dsecure column
 * @method     ChildBe2billTransactionQuery orderByExeccode($order = Criteria::ASC) Order by the execcode column
 * @method     ChildBe2billTransactionQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method     ChildBe2billTransactionQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method     ChildBe2billTransactionQuery orderByClientemail($order = Criteria::ASC) Order by the clientemail column
 * @method     ChildBe2billTransactionQuery orderByCardcode($order = Criteria::ASC) Order by the cardcode column
 * @method     ChildBe2billTransactionQuery orderByCardvaliditydate($order = Criteria::ASC) Order by the cardvaliditydate column
 * @method     ChildBe2billTransactionQuery orderByCardfullname($order = Criteria::ASC) Order by the cardfullname column
 * @method     ChildBe2billTransactionQuery orderByCardtype($order = Criteria::ASC) Order by the cardtype column
 * @method     ChildBe2billTransactionQuery orderByRefunded($order = Criteria::ASC) Order by the refunded column
 * @method     ChildBe2billTransactionQuery orderByRefundedby($order = Criteria::ASC) Order by the refundedby column
 * @method     ChildBe2billTransactionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildBe2billTransactionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildBe2billTransactionQuery groupById() Group by the id column
 * @method     ChildBe2billTransactionQuery groupByOrderId() Group by the order_id column
 * @method     ChildBe2billTransactionQuery groupByCustomerId() Group by the customer_id column
 * @method     ChildBe2billTransactionQuery groupByTransactionId() Group by the transaction_id column
 * @method     ChildBe2billTransactionQuery groupByOperationtype() Group by the operationtype column
 * @method     ChildBe2billTransactionQuery groupByDsecure() Group by the dsecure column
 * @method     ChildBe2billTransactionQuery groupByExeccode() Group by the execcode column
 * @method     ChildBe2billTransactionQuery groupByMessage() Group by the message column
 * @method     ChildBe2billTransactionQuery groupByAmount() Group by the amount column
 * @method     ChildBe2billTransactionQuery groupByClientemail() Group by the clientemail column
 * @method     ChildBe2billTransactionQuery groupByCardcode() Group by the cardcode column
 * @method     ChildBe2billTransactionQuery groupByCardvaliditydate() Group by the cardvaliditydate column
 * @method     ChildBe2billTransactionQuery groupByCardfullname() Group by the cardfullname column
 * @method     ChildBe2billTransactionQuery groupByCardtype() Group by the cardtype column
 * @method     ChildBe2billTransactionQuery groupByRefunded() Group by the refunded column
 * @method     ChildBe2billTransactionQuery groupByRefundedby() Group by the refundedby column
 * @method     ChildBe2billTransactionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildBe2billTransactionQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildBe2billTransactionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBe2billTransactionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBe2billTransactionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBe2billTransactionQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildBe2billTransactionQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildBe2billTransactionQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildBe2billTransactionQuery leftJoinCustomer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customer relation
 * @method     ChildBe2billTransactionQuery rightJoinCustomer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customer relation
 * @method     ChildBe2billTransactionQuery innerJoinCustomer($relationAlias = null) Adds a INNER JOIN clause to the query using the Customer relation
 *
 * @method     ChildBe2billTransaction findOne(ConnectionInterface $con = null) Return the first ChildBe2billTransaction matching the query
 * @method     ChildBe2billTransaction findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBe2billTransaction matching the query, or a new ChildBe2billTransaction object populated from the query conditions when no match is found
 *
 * @method     ChildBe2billTransaction findOneById(int $id) Return the first ChildBe2billTransaction filtered by the id column
 * @method     ChildBe2billTransaction findOneByOrderId(int $order_id) Return the first ChildBe2billTransaction filtered by the order_id column
 * @method     ChildBe2billTransaction findOneByCustomerId(int $customer_id) Return the first ChildBe2billTransaction filtered by the customer_id column
 * @method     ChildBe2billTransaction findOneByTransactionId(int $transaction_id) Return the first ChildBe2billTransaction filtered by the transaction_id column
 * @method     ChildBe2billTransaction findOneByOperationtype(string $operationtype) Return the first ChildBe2billTransaction filtered by the operationtype column
 * @method     ChildBe2billTransaction findOneByDsecure(string $dsecure) Return the first ChildBe2billTransaction filtered by the dsecure column
 * @method     ChildBe2billTransaction findOneByExeccode(string $execcode) Return the first ChildBe2billTransaction filtered by the execcode column
 * @method     ChildBe2billTransaction findOneByMessage(string $message) Return the first ChildBe2billTransaction filtered by the message column
 * @method     ChildBe2billTransaction findOneByAmount(string $amount) Return the first ChildBe2billTransaction filtered by the amount column
 * @method     ChildBe2billTransaction findOneByClientemail(string $clientemail) Return the first ChildBe2billTransaction filtered by the clientemail column
 * @method     ChildBe2billTransaction findOneByCardcode(string $cardcode) Return the first ChildBe2billTransaction filtered by the cardcode column
 * @method     ChildBe2billTransaction findOneByCardvaliditydate(string $cardvaliditydate) Return the first ChildBe2billTransaction filtered by the cardvaliditydate column
 * @method     ChildBe2billTransaction findOneByCardfullname(string $cardfullname) Return the first ChildBe2billTransaction filtered by the cardfullname column
 * @method     ChildBe2billTransaction findOneByCardtype(string $cardtype) Return the first ChildBe2billTransaction filtered by the cardtype column
 * @method     ChildBe2billTransaction findOneByRefunded(boolean $refunded) Return the first ChildBe2billTransaction filtered by the refunded column
 * @method     ChildBe2billTransaction findOneByRefundedby(string $refundedby) Return the first ChildBe2billTransaction filtered by the refundedby column
 * @method     ChildBe2billTransaction findOneByCreatedAt(string $created_at) Return the first ChildBe2billTransaction filtered by the created_at column
 * @method     ChildBe2billTransaction findOneByUpdatedAt(string $updated_at) Return the first ChildBe2billTransaction filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildBe2billTransaction objects filtered by the id column
 * @method     array findByOrderId(int $order_id) Return ChildBe2billTransaction objects filtered by the order_id column
 * @method     array findByCustomerId(int $customer_id) Return ChildBe2billTransaction objects filtered by the customer_id column
 * @method     array findByTransactionId(int $transaction_id) Return ChildBe2billTransaction objects filtered by the transaction_id column
 * @method     array findByOperationtype(string $operationtype) Return ChildBe2billTransaction objects filtered by the operationtype column
 * @method     array findByDsecure(string $dsecure) Return ChildBe2billTransaction objects filtered by the dsecure column
 * @method     array findByExeccode(string $execcode) Return ChildBe2billTransaction objects filtered by the execcode column
 * @method     array findByMessage(string $message) Return ChildBe2billTransaction objects filtered by the message column
 * @method     array findByAmount(string $amount) Return ChildBe2billTransaction objects filtered by the amount column
 * @method     array findByClientemail(string $clientemail) Return ChildBe2billTransaction objects filtered by the clientemail column
 * @method     array findByCardcode(string $cardcode) Return ChildBe2billTransaction objects filtered by the cardcode column
 * @method     array findByCardvaliditydate(string $cardvaliditydate) Return ChildBe2billTransaction objects filtered by the cardvaliditydate column
 * @method     array findByCardfullname(string $cardfullname) Return ChildBe2billTransaction objects filtered by the cardfullname column
 * @method     array findByCardtype(string $cardtype) Return ChildBe2billTransaction objects filtered by the cardtype column
 * @method     array findByRefunded(boolean $refunded) Return ChildBe2billTransaction objects filtered by the refunded column
 * @method     array findByRefundedby(string $refundedby) Return ChildBe2billTransaction objects filtered by the refundedby column
 * @method     array findByCreatedAt(string $created_at) Return ChildBe2billTransaction objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildBe2billTransaction objects filtered by the updated_at column
 *
 */
abstract class Be2billTransactionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Be2Bill\Model\Base\Be2billTransactionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Be2Bill\\Model\\Be2billTransaction', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBe2billTransactionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBe2billTransactionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Be2Bill\Model\Be2billTransactionQuery) {
            return $criteria;
        }
        $query = new \Be2Bill\Model\Be2billTransactionQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildBe2billTransaction|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = Be2billTransactionTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildBe2billTransaction A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ORDER_ID, CUSTOMER_ID, TRANSACTION_ID, OPERATIONTYPE, DSECURE, EXECCODE, MESSAGE, AMOUNT, CLIENTEMAIL, CARDCODE, CARDVALIDITYDATE, CARDFULLNAME, CARDTYPE, REFUNDED, REFUNDEDBY, CREATED_AT, UPDATED_AT FROM be2bill_transaction WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildBe2billTransaction();
            $obj->hydrate($row);
            Be2billTransactionTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildBe2billTransaction|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(Be2billTransactionTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(Be2billTransactionTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @see       filterByOrder()
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the customer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerId(1234); // WHERE customer_id = 1234
     * $query->filterByCustomerId(array(12, 34)); // WHERE customer_id IN (12, 34)
     * $query->filterByCustomerId(array('min' => 12)); // WHERE customer_id > 12
     * </code>
     *
     * @see       filterByCustomer()
     *
     * @param     mixed $customerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCustomerId($customerId = null, $comparison = null)
    {
        if (is_array($customerId)) {
            $useMinMax = false;
            if (isset($customerId['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::CUSTOMER_ID, $customerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerId['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::CUSTOMER_ID, $customerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CUSTOMER_ID, $customerId, $comparison);
    }

    /**
     * Filter the query on the transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTransactionId(1234); // WHERE transaction_id = 1234
     * $query->filterByTransactionId(array(12, 34)); // WHERE transaction_id IN (12, 34)
     * $query->filterByTransactionId(array('min' => 12)); // WHERE transaction_id > 12
     * </code>
     *
     * @param     mixed $transactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByTransactionId($transactionId = null, $comparison = null)
    {
        if (is_array($transactionId)) {
            $useMinMax = false;
            if (isset($transactionId['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::TRANSACTION_ID, $transactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($transactionId['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::TRANSACTION_ID, $transactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::TRANSACTION_ID, $transactionId, $comparison);
    }

    /**
     * Filter the query on the operationtype column
     *
     * Example usage:
     * <code>
     * $query->filterByOperationtype('fooValue');   // WHERE operationtype = 'fooValue'
     * $query->filterByOperationtype('%fooValue%'); // WHERE operationtype LIKE '%fooValue%'
     * </code>
     *
     * @param     string $operationtype The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByOperationtype($operationtype = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($operationtype)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $operationtype)) {
                $operationtype = str_replace('*', '%', $operationtype);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::OPERATIONTYPE, $operationtype, $comparison);
    }

    /**
     * Filter the query on the dsecure column
     *
     * Example usage:
     * <code>
     * $query->filterByDsecure('fooValue');   // WHERE dsecure = 'fooValue'
     * $query->filterByDsecure('%fooValue%'); // WHERE dsecure LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dsecure The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByDsecure($dsecure = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dsecure)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dsecure)) {
                $dsecure = str_replace('*', '%', $dsecure);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::DSECURE, $dsecure, $comparison);
    }

    /**
     * Filter the query on the execcode column
     *
     * Example usage:
     * <code>
     * $query->filterByExeccode('fooValue');   // WHERE execcode = 'fooValue'
     * $query->filterByExeccode('%fooValue%'); // WHERE execcode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $execcode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByExeccode($execcode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($execcode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $execcode)) {
                $execcode = str_replace('*', '%', $execcode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::EXECCODE, $execcode, $comparison);
    }

    /**
     * Filter the query on the message column
     *
     * Example usage:
     * <code>
     * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
     * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $message The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByMessage($message = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($message)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $message)) {
                $message = str_replace('*', '%', $message);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::MESSAGE, $message, $comparison);
    }

    /**
     * Filter the query on the amount column
     *
     * Example usage:
     * <code>
     * $query->filterByAmount('fooValue');   // WHERE amount = 'fooValue'
     * $query->filterByAmount('%fooValue%'); // WHERE amount LIKE '%fooValue%'
     * </code>
     *
     * @param     string $amount The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($amount)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $amount)) {
                $amount = str_replace('*', '%', $amount);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query on the clientemail column
     *
     * Example usage:
     * <code>
     * $query->filterByClientemail('fooValue');   // WHERE clientemail = 'fooValue'
     * $query->filterByClientemail('%fooValue%'); // WHERE clientemail LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clientemail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByClientemail($clientemail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clientemail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clientemail)) {
                $clientemail = str_replace('*', '%', $clientemail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CLIENTEMAIL, $clientemail, $comparison);
    }

    /**
     * Filter the query on the cardcode column
     *
     * Example usage:
     * <code>
     * $query->filterByCardcode('fooValue');   // WHERE cardcode = 'fooValue'
     * $query->filterByCardcode('%fooValue%'); // WHERE cardcode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cardcode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCardcode($cardcode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cardcode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cardcode)) {
                $cardcode = str_replace('*', '%', $cardcode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CARDCODE, $cardcode, $comparison);
    }

    /**
     * Filter the query on the cardvaliditydate column
     *
     * Example usage:
     * <code>
     * $query->filterByCardvaliditydate('fooValue');   // WHERE cardvaliditydate = 'fooValue'
     * $query->filterByCardvaliditydate('%fooValue%'); // WHERE cardvaliditydate LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cardvaliditydate The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCardvaliditydate($cardvaliditydate = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cardvaliditydate)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cardvaliditydate)) {
                $cardvaliditydate = str_replace('*', '%', $cardvaliditydate);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CARDVALIDITYDATE, $cardvaliditydate, $comparison);
    }

    /**
     * Filter the query on the cardfullname column
     *
     * Example usage:
     * <code>
     * $query->filterByCardfullname('fooValue');   // WHERE cardfullname = 'fooValue'
     * $query->filterByCardfullname('%fooValue%'); // WHERE cardfullname LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cardfullname The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCardfullname($cardfullname = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cardfullname)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cardfullname)) {
                $cardfullname = str_replace('*', '%', $cardfullname);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CARDFULLNAME, $cardfullname, $comparison);
    }

    /**
     * Filter the query on the cardtype column
     *
     * Example usage:
     * <code>
     * $query->filterByCardtype('fooValue');   // WHERE cardtype = 'fooValue'
     * $query->filterByCardtype('%fooValue%'); // WHERE cardtype LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cardtype The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCardtype($cardtype = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cardtype)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cardtype)) {
                $cardtype = str_replace('*', '%', $cardtype);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CARDTYPE, $cardtype, $comparison);
    }

    /**
     * Filter the query on the refunded column
     *
     * Example usage:
     * <code>
     * $query->filterByRefunded(true); // WHERE refunded = true
     * $query->filterByRefunded('yes'); // WHERE refunded = true
     * </code>
     *
     * @param     boolean|string $refunded The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByRefunded($refunded = null, $comparison = null)
    {
        if (is_string($refunded)) {
            $refunded = in_array(strtolower($refunded), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::REFUNDED, $refunded, $comparison);
    }

    /**
     * Filter the query on the refundedby column
     *
     * Example usage:
     * <code>
     * $query->filterByRefundedby('fooValue');   // WHERE refundedby = 'fooValue'
     * $query->filterByRefundedby('%fooValue%'); // WHERE refundedby LIKE '%fooValue%'
     * </code>
     *
     * @param     string $refundedby The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByRefundedby($refundedby = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($refundedby)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $refundedby)) {
                $refundedby = str_replace('*', '%', $refundedby);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::REFUNDEDBY, $refundedby, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(Be2billTransactionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(Be2billTransactionTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Be2Bill\Model\Thelia\Model\Order object
     *
     * @param \Be2Bill\Model\Thelia\Model\Order|ObjectCollection $order The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \Be2Bill\Model\Thelia\Model\Order) {
            return $this
                ->addUsingAlias(Be2billTransactionTableMap::ORDER_ID, $order->getId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(Be2billTransactionTableMap::ORDER_ID, $order->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrder() only accepts arguments of type \Be2Bill\Model\Thelia\Model\Order or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Order relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Order');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Order');
        }

        return $this;
    }

    /**
     * Use the Order relation Order object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Be2Bill\Model\Thelia\Model\OrderQuery A secondary query class using the current class as primary query
     */
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\Be2Bill\Model\Thelia\Model\OrderQuery');
    }

    /**
     * Filter the query by a related \Be2Bill\Model\Thelia\Model\Customer object
     *
     * @param \Be2Bill\Model\Thelia\Model\Customer|ObjectCollection $customer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function filterByCustomer($customer, $comparison = null)
    {
        if ($customer instanceof \Be2Bill\Model\Thelia\Model\Customer) {
            return $this
                ->addUsingAlias(Be2billTransactionTableMap::CUSTOMER_ID, $customer->getId(), $comparison);
        } elseif ($customer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(Be2billTransactionTableMap::CUSTOMER_ID, $customer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomer() only accepts arguments of type \Be2Bill\Model\Thelia\Model\Customer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Customer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function joinCustomer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Customer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Customer');
        }

        return $this;
    }

    /**
     * Use the Customer relation Customer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Be2Bill\Model\Thelia\Model\CustomerQuery A secondary query class using the current class as primary query
     */
    public function useCustomerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customer', '\Be2Bill\Model\Thelia\Model\CustomerQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBe2billTransaction $be2billTransaction Object to remove from the list of results
     *
     * @return ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function prune($be2billTransaction = null)
    {
        if ($be2billTransaction) {
            $this->addUsingAlias(Be2billTransactionTableMap::ID, $be2billTransaction->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the be2bill_transaction table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            Be2billTransactionTableMap::clearInstancePool();
            Be2billTransactionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildBe2billTransaction or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildBe2billTransaction object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(Be2billTransactionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        Be2billTransactionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            Be2billTransactionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(Be2billTransactionTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(Be2billTransactionTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(Be2billTransactionTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(Be2billTransactionTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(Be2billTransactionTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildBe2billTransactionQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(Be2billTransactionTableMap::CREATED_AT);
    }

} // Be2billTransactionQuery
