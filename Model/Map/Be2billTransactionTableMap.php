<?php

namespace Be2Bill\Model\Map;

use Be2Bill\Model\Be2billTransaction;
use Be2Bill\Model\Be2billTransactionQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'be2bill_transaction' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class Be2billTransactionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Be2Bill.Model.Map.Be2billTransactionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'be2bill_transaction';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Be2Bill\\Model\\Be2billTransaction';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Be2Bill.Model.Be2billTransaction';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 18;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 18;

    /**
     * the column name for the ID field
     */
    const ID = 'be2bill_transaction.ID';

    /**
     * the column name for the ORDER_ID field
     */
    const ORDER_ID = 'be2bill_transaction.ORDER_ID';

    /**
     * the column name for the CUSTOMER_ID field
     */
    const CUSTOMER_ID = 'be2bill_transaction.CUSTOMER_ID';

    /**
     * the column name for the TRANSACTION_ID field
     */
    const TRANSACTION_ID = 'be2bill_transaction.TRANSACTION_ID';

    /**
     * the column name for the OPERATIONTYPE field
     */
    const OPERATIONTYPE = 'be2bill_transaction.OPERATIONTYPE';

    /**
     * the column name for the DSECURE field
     */
    const DSECURE = 'be2bill_transaction.DSECURE';

    /**
     * the column name for the EXECCODE field
     */
    const EXECCODE = 'be2bill_transaction.EXECCODE';

    /**
     * the column name for the MESSAGE field
     */
    const MESSAGE = 'be2bill_transaction.MESSAGE';

    /**
     * the column name for the AMOUNT field
     */
    const AMOUNT = 'be2bill_transaction.AMOUNT';

    /**
     * the column name for the CLIENTEMAIL field
     */
    const CLIENTEMAIL = 'be2bill_transaction.CLIENTEMAIL';

    /**
     * the column name for the CARDCODE field
     */
    const CARDCODE = 'be2bill_transaction.CARDCODE';

    /**
     * the column name for the CARDVALIDITYDATE field
     */
    const CARDVALIDITYDATE = 'be2bill_transaction.CARDVALIDITYDATE';

    /**
     * the column name for the CARDFULLNAME field
     */
    const CARDFULLNAME = 'be2bill_transaction.CARDFULLNAME';

    /**
     * the column name for the CARDTYPE field
     */
    const CARDTYPE = 'be2bill_transaction.CARDTYPE';

    /**
     * the column name for the REFUNDED field
     */
    const REFUNDED = 'be2bill_transaction.REFUNDED';

    /**
     * the column name for the REFUNDEDBY field
     */
    const REFUNDEDBY = 'be2bill_transaction.REFUNDEDBY';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'be2bill_transaction.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'be2bill_transaction.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'OrderId', 'CustomerId', 'TransactionId', 'Operationtype', 'Dsecure', 'Execcode', 'Message', 'Amount', 'Clientemail', 'Cardcode', 'Cardvaliditydate', 'Cardfullname', 'Cardtype', 'Refunded', 'Refundedby', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'orderId', 'customerId', 'transactionId', 'operationtype', 'dsecure', 'execcode', 'message', 'amount', 'clientemail', 'cardcode', 'cardvaliditydate', 'cardfullname', 'cardtype', 'refunded', 'refundedby', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(Be2billTransactionTableMap::ID, Be2billTransactionTableMap::ORDER_ID, Be2billTransactionTableMap::CUSTOMER_ID, Be2billTransactionTableMap::TRANSACTION_ID, Be2billTransactionTableMap::OPERATIONTYPE, Be2billTransactionTableMap::DSECURE, Be2billTransactionTableMap::EXECCODE, Be2billTransactionTableMap::MESSAGE, Be2billTransactionTableMap::AMOUNT, Be2billTransactionTableMap::CLIENTEMAIL, Be2billTransactionTableMap::CARDCODE, Be2billTransactionTableMap::CARDVALIDITYDATE, Be2billTransactionTableMap::CARDFULLNAME, Be2billTransactionTableMap::CARDTYPE, Be2billTransactionTableMap::REFUNDED, Be2billTransactionTableMap::REFUNDEDBY, Be2billTransactionTableMap::CREATED_AT, Be2billTransactionTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'ORDER_ID', 'CUSTOMER_ID', 'TRANSACTION_ID', 'OPERATIONTYPE', 'DSECURE', 'EXECCODE', 'MESSAGE', 'AMOUNT', 'CLIENTEMAIL', 'CARDCODE', 'CARDVALIDITYDATE', 'CARDFULLNAME', 'CARDTYPE', 'REFUNDED', 'REFUNDEDBY', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'order_id', 'customer_id', 'transaction_id', 'operationtype', 'dsecure', 'execcode', 'message', 'amount', 'clientemail', 'cardcode', 'cardvaliditydate', 'cardfullname', 'cardtype', 'refunded', 'refundedby', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'OrderId' => 1, 'CustomerId' => 2, 'TransactionId' => 3, 'Operationtype' => 4, 'Dsecure' => 5, 'Execcode' => 6, 'Message' => 7, 'Amount' => 8, 'Clientemail' => 9, 'Cardcode' => 10, 'Cardvaliditydate' => 11, 'Cardfullname' => 12, 'Cardtype' => 13, 'Refunded' => 14, 'Refundedby' => 15, 'CreatedAt' => 16, 'UpdatedAt' => 17, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'orderId' => 1, 'customerId' => 2, 'transactionId' => 3, 'operationtype' => 4, 'dsecure' => 5, 'execcode' => 6, 'message' => 7, 'amount' => 8, 'clientemail' => 9, 'cardcode' => 10, 'cardvaliditydate' => 11, 'cardfullname' => 12, 'cardtype' => 13, 'refunded' => 14, 'refundedby' => 15, 'createdAt' => 16, 'updatedAt' => 17, ),
        self::TYPE_COLNAME       => array(Be2billTransactionTableMap::ID => 0, Be2billTransactionTableMap::ORDER_ID => 1, Be2billTransactionTableMap::CUSTOMER_ID => 2, Be2billTransactionTableMap::TRANSACTION_ID => 3, Be2billTransactionTableMap::OPERATIONTYPE => 4, Be2billTransactionTableMap::DSECURE => 5, Be2billTransactionTableMap::EXECCODE => 6, Be2billTransactionTableMap::MESSAGE => 7, Be2billTransactionTableMap::AMOUNT => 8, Be2billTransactionTableMap::CLIENTEMAIL => 9, Be2billTransactionTableMap::CARDCODE => 10, Be2billTransactionTableMap::CARDVALIDITYDATE => 11, Be2billTransactionTableMap::CARDFULLNAME => 12, Be2billTransactionTableMap::CARDTYPE => 13, Be2billTransactionTableMap::REFUNDED => 14, Be2billTransactionTableMap::REFUNDEDBY => 15, Be2billTransactionTableMap::CREATED_AT => 16, Be2billTransactionTableMap::UPDATED_AT => 17, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'ORDER_ID' => 1, 'CUSTOMER_ID' => 2, 'TRANSACTION_ID' => 3, 'OPERATIONTYPE' => 4, 'DSECURE' => 5, 'EXECCODE' => 6, 'MESSAGE' => 7, 'AMOUNT' => 8, 'CLIENTEMAIL' => 9, 'CARDCODE' => 10, 'CARDVALIDITYDATE' => 11, 'CARDFULLNAME' => 12, 'CARDTYPE' => 13, 'REFUNDED' => 14, 'REFUNDEDBY' => 15, 'CREATED_AT' => 16, 'UPDATED_AT' => 17, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'order_id' => 1, 'customer_id' => 2, 'transaction_id' => 3, 'operationtype' => 4, 'dsecure' => 5, 'execcode' => 6, 'message' => 7, 'amount' => 8, 'clientemail' => 9, 'cardcode' => 10, 'cardvaliditydate' => 11, 'cardfullname' => 12, 'cardtype' => 13, 'refunded' => 14, 'refundedby' => 15, 'created_at' => 16, 'updated_at' => 17, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('be2bill_transaction');
        $this->setPhpName('Be2billTransaction');
        $this->setClassName('\\Be2Bill\\Model\\Be2billTransaction');
        $this->setPackage('Be2Bill.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('ORDER_ID', 'OrderId', 'INTEGER', 'order', 'ID', true, null, null);
        $this->addForeignKey('CUSTOMER_ID', 'CustomerId', 'INTEGER', 'customer', 'ID', true, null, null);
        $this->addColumn('TRANSACTION_ID', 'TransactionId', 'INTEGER', true, null, null);
        $this->addColumn('OPERATIONTYPE', 'Operationtype', 'VARCHAR', true, 255, null);
        $this->addColumn('DSECURE', 'Dsecure', 'VARCHAR', true, 255, null);
        $this->addColumn('EXECCODE', 'Execcode', 'VARCHAR', true, 255, null);
        $this->addColumn('MESSAGE', 'Message', 'VARCHAR', true, 255, null);
        $this->addColumn('AMOUNT', 'Amount', 'VARCHAR', true, 255, null);
        $this->addColumn('CLIENTEMAIL', 'Clientemail', 'VARCHAR', true, 255, null);
        $this->addColumn('CARDCODE', 'Cardcode', 'VARCHAR', true, 255, null);
        $this->addColumn('CARDVALIDITYDATE', 'Cardvaliditydate', 'VARCHAR', true, 255, null);
        $this->addColumn('CARDFULLNAME', 'Cardfullname', 'VARCHAR', true, 255, null);
        $this->addColumn('CARDTYPE', 'Cardtype', 'VARCHAR', true, 255, null);
        $this->addColumn('REFUNDED', 'Refunded', 'BOOLEAN', true, 1, false);
        $this->addColumn('REFUNDEDBY', 'Refundedby', 'VARCHAR', false, 255, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Be2Bill\\Model\\Thelia\\Model\\Order', RelationMap::MANY_TO_ONE, array('order_id' => 'id', ), 'CASCADE', 'RESTRICT');
        $this->addRelation('Customer', '\\Be2Bill\\Model\\Thelia\\Model\\Customer', RelationMap::MANY_TO_ONE, array('customer_id' => 'id', ), 'CASCADE', 'RESTRICT');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? Be2billTransactionTableMap::CLASS_DEFAULT : Be2billTransactionTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (Be2billTransaction object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = Be2billTransactionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = Be2billTransactionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + Be2billTransactionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = Be2billTransactionTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            Be2billTransactionTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = Be2billTransactionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = Be2billTransactionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                Be2billTransactionTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(Be2billTransactionTableMap::ID);
            $criteria->addSelectColumn(Be2billTransactionTableMap::ORDER_ID);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CUSTOMER_ID);
            $criteria->addSelectColumn(Be2billTransactionTableMap::TRANSACTION_ID);
            $criteria->addSelectColumn(Be2billTransactionTableMap::OPERATIONTYPE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::DSECURE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::EXECCODE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::MESSAGE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::AMOUNT);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CLIENTEMAIL);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CARDCODE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CARDVALIDITYDATE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CARDFULLNAME);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CARDTYPE);
            $criteria->addSelectColumn(Be2billTransactionTableMap::REFUNDED);
            $criteria->addSelectColumn(Be2billTransactionTableMap::REFUNDEDBY);
            $criteria->addSelectColumn(Be2billTransactionTableMap::CREATED_AT);
            $criteria->addSelectColumn(Be2billTransactionTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.CUSTOMER_ID');
            $criteria->addSelectColumn($alias . '.TRANSACTION_ID');
            $criteria->addSelectColumn($alias . '.OPERATIONTYPE');
            $criteria->addSelectColumn($alias . '.DSECURE');
            $criteria->addSelectColumn($alias . '.EXECCODE');
            $criteria->addSelectColumn($alias . '.MESSAGE');
            $criteria->addSelectColumn($alias . '.AMOUNT');
            $criteria->addSelectColumn($alias . '.CLIENTEMAIL');
            $criteria->addSelectColumn($alias . '.CARDCODE');
            $criteria->addSelectColumn($alias . '.CARDVALIDITYDATE');
            $criteria->addSelectColumn($alias . '.CARDFULLNAME');
            $criteria->addSelectColumn($alias . '.CARDTYPE');
            $criteria->addSelectColumn($alias . '.REFUNDED');
            $criteria->addSelectColumn($alias . '.REFUNDEDBY');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(Be2billTransactionTableMap::DATABASE_NAME)->getTable(Be2billTransactionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(Be2billTransactionTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(Be2billTransactionTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new Be2billTransactionTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Be2billTransaction or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Be2billTransaction object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Be2Bill\Model\Be2billTransaction) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(Be2billTransactionTableMap::DATABASE_NAME);
            $criteria->add(Be2billTransactionTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = Be2billTransactionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { Be2billTransactionTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { Be2billTransactionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the be2bill_transaction table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return Be2billTransactionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Be2billTransaction or Criteria object.
     *
     * @param mixed               $criteria Criteria or Be2billTransaction object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Be2billTransaction object
        }

        if ($criteria->containsKey(Be2billTransactionTableMap::ID) && $criteria->keyContainsValue(Be2billTransactionTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.Be2billTransactionTableMap::ID.')');
        }


        // Set the correct dbName
        $query = Be2billTransactionQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // Be2billTransactionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
Be2billTransactionTableMap::buildTableMap();
