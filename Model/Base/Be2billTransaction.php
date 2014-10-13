<?php

namespace Be2Bill\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Be2Bill\Model\Be2billTransaction as ChildBe2billTransaction;
use Be2Bill\Model\Be2billTransactionQuery as ChildBe2billTransactionQuery;
use Be2Bill\Model\Map\Be2billTransactionTableMap;
use Be2Bill\Model\Thelia\Model\Customer as ChildCustomer;
use Be2Bill\Model\Thelia\Model\Order as ChildOrder;
use Be2Bill\Model\Thelia\Model\CustomerQuery;
use Be2Bill\Model\Thelia\Model\OrderQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class Be2billTransaction implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Be2Bill\\Model\\Map\\Be2billTransactionTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the order_id field.
     * @var        int
     */
    protected $order_id;

    /**
     * The value for the customer_id field.
     * @var        int
     */
    protected $customer_id;

    /**
     * The value for the transaction_id field.
     * @var        string
     */
    protected $transaction_id;

    /**
     * The value for the operationtype field.
     * @var        string
     */
    protected $operationtype;

    /**
     * The value for the dsecure field.
     * @var        string
     */
    protected $dsecure;

    /**
     * The value for the execcode field.
     * @var        string
     */
    protected $execcode;

    /**
     * The value for the message field.
     * @var        string
     */
    protected $message;

    /**
     * The value for the amount field.
     * @var        string
     */
    protected $amount;

    /**
     * The value for the clientemail field.
     * @var        string
     */
    protected $clientemail;

    /**
     * The value for the cardcode field.
     * @var        string
     */
    protected $cardcode;

    /**
     * The value for the cardvaliditydate field.
     * @var        string
     */
    protected $cardvaliditydate;

    /**
     * The value for the cardfullname field.
     * @var        string
     */
    protected $cardfullname;

    /**
     * The value for the cardtype field.
     * @var        string
     */
    protected $cardtype;

    /**
     * The value for the refunded field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $refunded;

    /**
     * The value for the refundedby field.
     * @var        string
     */
    protected $refundedby;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Order
     */
    protected $aOrder;

    /**
     * @var        Customer
     */
    protected $aCustomer;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->refunded = false;
    }

    /**
     * Initializes internal state of Be2Bill\Model\Base\Be2billTransaction object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Be2billTransaction</code> instance.  If
     * <code>obj</code> is an instance of <code>Be2billTransaction</code>, delegates to
     * <code>equals(Be2billTransaction)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Be2billTransaction The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Be2billTransaction The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [order_id] column value.
     *
     * @return   int
     */
    public function getOrderId()
    {

        return $this->order_id;
    }

    /**
     * Get the [customer_id] column value.
     *
     * @return   int
     */
    public function getCustomerId()
    {

        return $this->customer_id;
    }

    /**
     * Get the [transaction_id] column value.
     *
     * @return   string
     */
    public function getTransactionId()
    {

        return $this->transaction_id;
    }

    /**
     * Get the [operationtype] column value.
     *
     * @return   string
     */
    public function getOperationtype()
    {

        return $this->operationtype;
    }

    /**
     * Get the [dsecure] column value.
     *
     * @return   string
     */
    public function getDsecure()
    {

        return $this->dsecure;
    }

    /**
     * Get the [execcode] column value.
     *
     * @return   string
     */
    public function getExeccode()
    {

        return $this->execcode;
    }

    /**
     * Get the [message] column value.
     *
     * @return   string
     */
    public function getMessage()
    {

        return $this->message;
    }

    /**
     * Get the [amount] column value.
     *
     * @return   string
     */
    public function getAmount()
    {

        return $this->amount;
    }

    /**
     * Get the [clientemail] column value.
     *
     * @return   string
     */
    public function getClientemail()
    {

        return $this->clientemail;
    }

    /**
     * Get the [cardcode] column value.
     *
     * @return   string
     */
    public function getCardcode()
    {

        return $this->cardcode;
    }

    /**
     * Get the [cardvaliditydate] column value.
     *
     * @return   string
     */
    public function getCardvaliditydate()
    {

        return $this->cardvaliditydate;
    }

    /**
     * Get the [cardfullname] column value.
     *
     * @return   string
     */
    public function getCardfullname()
    {

        return $this->cardfullname;
    }

    /**
     * Get the [cardtype] column value.
     *
     * @return   string
     */
    public function getCardtype()
    {

        return $this->cardtype;
    }

    /**
     * Get the [refunded] column value.
     *
     * @return   boolean
     */
    public function getRefunded()
    {

        return $this->refunded;
    }

    /**
     * Get the [refundedby] column value.
     *
     * @return   string
     */
    public function getRefundedby()
    {

        return $this->refundedby;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [order_id] column.
     *
     * @param      int $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setOrderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id !== $v) {
            $this->order_id = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::ORDER_ID] = true;
        }

        if ($this->aOrder !== null && $this->aOrder->getId() !== $v) {
            $this->aOrder = null;
        }


        return $this;
    } // setOrderId()

    /**
     * Set the value of [customer_id] column.
     *
     * @param      int $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCustomerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->customer_id !== $v) {
            $this->customer_id = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CUSTOMER_ID] = true;
        }

        if ($this->aCustomer !== null && $this->aCustomer->getId() !== $v) {
            $this->aCustomer = null;
        }


        return $this;
    } // setCustomerId()

    /**
     * Set the value of [transaction_id] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setTransactionId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->transaction_id !== $v) {
            $this->transaction_id = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::TRANSACTION_ID] = true;
        }


        return $this;
    } // setTransactionId()

    /**
     * Set the value of [operationtype] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setOperationtype($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->operationtype !== $v) {
            $this->operationtype = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::OPERATIONTYPE] = true;
        }


        return $this;
    } // setOperationtype()

    /**
     * Set the value of [dsecure] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setDsecure($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dsecure !== $v) {
            $this->dsecure = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::DSECURE] = true;
        }


        return $this;
    } // setDsecure()

    /**
     * Set the value of [execcode] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setExeccode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->execcode !== $v) {
            $this->execcode = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::EXECCODE] = true;
        }


        return $this;
    } // setExeccode()

    /**
     * Set the value of [message] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setMessage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->message !== $v) {
            $this->message = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::MESSAGE] = true;
        }


        return $this;
    } // setMessage()

    /**
     * Set the value of [amount] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setAmount($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->amount !== $v) {
            $this->amount = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::AMOUNT] = true;
        }


        return $this;
    } // setAmount()

    /**
     * Set the value of [clientemail] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setClientemail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->clientemail !== $v) {
            $this->clientemail = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CLIENTEMAIL] = true;
        }


        return $this;
    } // setClientemail()

    /**
     * Set the value of [cardcode] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCardcode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cardcode !== $v) {
            $this->cardcode = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CARDCODE] = true;
        }


        return $this;
    } // setCardcode()

    /**
     * Set the value of [cardvaliditydate] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCardvaliditydate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cardvaliditydate !== $v) {
            $this->cardvaliditydate = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CARDVALIDITYDATE] = true;
        }


        return $this;
    } // setCardvaliditydate()

    /**
     * Set the value of [cardfullname] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCardfullname($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cardfullname !== $v) {
            $this->cardfullname = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CARDFULLNAME] = true;
        }


        return $this;
    } // setCardfullname()

    /**
     * Set the value of [cardtype] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCardtype($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cardtype !== $v) {
            $this->cardtype = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::CARDTYPE] = true;
        }


        return $this;
    } // setCardtype()

    /**
     * Sets the value of the [refunded] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param      boolean|integer|string $v The new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setRefunded($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->refunded !== $v) {
            $this->refunded = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::REFUNDED] = true;
        }


        return $this;
    } // setRefunded()

    /**
     * Set the value of [refundedby] column.
     *
     * @param      string $v new value
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setRefundedby($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->refundedby !== $v) {
            $this->refundedby = $v;
            $this->modifiedColumns[Be2billTransactionTableMap::REFUNDEDBY] = true;
        }


        return $this;
    } // setRefundedby()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[Be2billTransactionTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[Be2billTransactionTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->refunded !== false) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : Be2billTransactionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : Be2billTransactionTableMap::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : Be2billTransactionTableMap::translateFieldName('CustomerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->customer_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : Be2billTransactionTableMap::translateFieldName('TransactionId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->transaction_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : Be2billTransactionTableMap::translateFieldName('Operationtype', TableMap::TYPE_PHPNAME, $indexType)];
            $this->operationtype = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : Be2billTransactionTableMap::translateFieldName('Dsecure', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dsecure = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : Be2billTransactionTableMap::translateFieldName('Execcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->execcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : Be2billTransactionTableMap::translateFieldName('Message', TableMap::TYPE_PHPNAME, $indexType)];
            $this->message = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : Be2billTransactionTableMap::translateFieldName('Amount', TableMap::TYPE_PHPNAME, $indexType)];
            $this->amount = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : Be2billTransactionTableMap::translateFieldName('Clientemail', TableMap::TYPE_PHPNAME, $indexType)];
            $this->clientemail = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : Be2billTransactionTableMap::translateFieldName('Cardcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cardcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : Be2billTransactionTableMap::translateFieldName('Cardvaliditydate', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cardvaliditydate = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : Be2billTransactionTableMap::translateFieldName('Cardfullname', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cardfullname = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : Be2billTransactionTableMap::translateFieldName('Cardtype', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cardtype = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : Be2billTransactionTableMap::translateFieldName('Refunded', TableMap::TYPE_PHPNAME, $indexType)];
            $this->refunded = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : Be2billTransactionTableMap::translateFieldName('Refundedby', TableMap::TYPE_PHPNAME, $indexType)];
            $this->refundedby = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : Be2billTransactionTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : Be2billTransactionTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 18; // 18 = Be2billTransactionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Be2Bill\Model\Be2billTransaction object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aOrder !== null && $this->order_id !== $this->aOrder->getId()) {
            $this->aOrder = null;
        }
        if ($this->aCustomer !== null && $this->customer_id !== $this->aCustomer->getId()) {
            $this->aCustomer = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildBe2billTransactionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aOrder = null;
            $this->aCustomer = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Be2billTransaction::setDeleted()
     * @see Be2billTransaction::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildBe2billTransactionQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(Be2billTransactionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(Be2billTransactionTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(Be2billTransactionTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(Be2billTransactionTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                Be2billTransactionTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aOrder !== null) {
                if ($this->aOrder->isModified() || $this->aOrder->isNew()) {
                    $affectedRows += $this->aOrder->save($con);
                }
                $this->setOrder($this->aOrder);
            }

            if ($this->aCustomer !== null) {
                if ($this->aCustomer->isModified() || $this->aCustomer->isNew()) {
                    $affectedRows += $this->aCustomer->save($con);
                }
                $this->setCustomer($this->aCustomer);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[Be2billTransactionTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . Be2billTransactionTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(Be2billTransactionTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::ORDER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_ID';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CUSTOMER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CUSTOMER_ID';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::TRANSACTION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TRANSACTION_ID';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::OPERATIONTYPE)) {
            $modifiedColumns[':p' . $index++]  = 'OPERATIONTYPE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::DSECURE)) {
            $modifiedColumns[':p' . $index++]  = 'DSECURE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::EXECCODE)) {
            $modifiedColumns[':p' . $index++]  = 'EXECCODE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::MESSAGE)) {
            $modifiedColumns[':p' . $index++]  = 'MESSAGE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::AMOUNT)) {
            $modifiedColumns[':p' . $index++]  = 'AMOUNT';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CLIENTEMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'CLIENTEMAIL';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDCODE)) {
            $modifiedColumns[':p' . $index++]  = 'CARDCODE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDVALIDITYDATE)) {
            $modifiedColumns[':p' . $index++]  = 'CARDVALIDITYDATE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDFULLNAME)) {
            $modifiedColumns[':p' . $index++]  = 'CARDFULLNAME';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDTYPE)) {
            $modifiedColumns[':p' . $index++]  = 'CARDTYPE';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::REFUNDED)) {
            $modifiedColumns[':p' . $index++]  = 'REFUNDED';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::REFUNDEDBY)) {
            $modifiedColumns[':p' . $index++]  = 'REFUNDEDBY';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(Be2billTransactionTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO be2bill_transaction (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'ORDER_ID':
                        $stmt->bindValue($identifier, $this->order_id, PDO::PARAM_INT);
                        break;
                    case 'CUSTOMER_ID':
                        $stmt->bindValue($identifier, $this->customer_id, PDO::PARAM_INT);
                        break;
                    case 'TRANSACTION_ID':
                        $stmt->bindValue($identifier, $this->transaction_id, PDO::PARAM_STR);
                        break;
                    case 'OPERATIONTYPE':
                        $stmt->bindValue($identifier, $this->operationtype, PDO::PARAM_STR);
                        break;
                    case 'DSECURE':
                        $stmt->bindValue($identifier, $this->dsecure, PDO::PARAM_STR);
                        break;
                    case 'EXECCODE':
                        $stmt->bindValue($identifier, $this->execcode, PDO::PARAM_STR);
                        break;
                    case 'MESSAGE':
                        $stmt->bindValue($identifier, $this->message, PDO::PARAM_STR);
                        break;
                    case 'AMOUNT':
                        $stmt->bindValue($identifier, $this->amount, PDO::PARAM_STR);
                        break;
                    case 'CLIENTEMAIL':
                        $stmt->bindValue($identifier, $this->clientemail, PDO::PARAM_STR);
                        break;
                    case 'CARDCODE':
                        $stmt->bindValue($identifier, $this->cardcode, PDO::PARAM_STR);
                        break;
                    case 'CARDVALIDITYDATE':
                        $stmt->bindValue($identifier, $this->cardvaliditydate, PDO::PARAM_STR);
                        break;
                    case 'CARDFULLNAME':
                        $stmt->bindValue($identifier, $this->cardfullname, PDO::PARAM_STR);
                        break;
                    case 'CARDTYPE':
                        $stmt->bindValue($identifier, $this->cardtype, PDO::PARAM_STR);
                        break;
                    case 'REFUNDED':
                        $stmt->bindValue($identifier, (int) $this->refunded, PDO::PARAM_INT);
                        break;
                    case 'REFUNDEDBY':
                        $stmt->bindValue($identifier, $this->refundedby, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = Be2billTransactionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getOrderId();
                break;
            case 2:
                return $this->getCustomerId();
                break;
            case 3:
                return $this->getTransactionId();
                break;
            case 4:
                return $this->getOperationtype();
                break;
            case 5:
                return $this->getDsecure();
                break;
            case 6:
                return $this->getExeccode();
                break;
            case 7:
                return $this->getMessage();
                break;
            case 8:
                return $this->getAmount();
                break;
            case 9:
                return $this->getClientemail();
                break;
            case 10:
                return $this->getCardcode();
                break;
            case 11:
                return $this->getCardvaliditydate();
                break;
            case 12:
                return $this->getCardfullname();
                break;
            case 13:
                return $this->getCardtype();
                break;
            case 14:
                return $this->getRefunded();
                break;
            case 15:
                return $this->getRefundedby();
                break;
            case 16:
                return $this->getCreatedAt();
                break;
            case 17:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Be2billTransaction'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Be2billTransaction'][$this->getPrimaryKey()] = true;
        $keys = Be2billTransactionTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getOrderId(),
            $keys[2] => $this->getCustomerId(),
            $keys[3] => $this->getTransactionId(),
            $keys[4] => $this->getOperationtype(),
            $keys[5] => $this->getDsecure(),
            $keys[6] => $this->getExeccode(),
            $keys[7] => $this->getMessage(),
            $keys[8] => $this->getAmount(),
            $keys[9] => $this->getClientemail(),
            $keys[10] => $this->getCardcode(),
            $keys[11] => $this->getCardvaliditydate(),
            $keys[12] => $this->getCardfullname(),
            $keys[13] => $this->getCardtype(),
            $keys[14] => $this->getRefunded(),
            $keys[15] => $this->getRefundedby(),
            $keys[16] => $this->getCreatedAt(),
            $keys[17] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aOrder) {
                $result['Order'] = $this->aOrder->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCustomer) {
                $result['Customer'] = $this->aCustomer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = Be2billTransactionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setOrderId($value);
                break;
            case 2:
                $this->setCustomerId($value);
                break;
            case 3:
                $this->setTransactionId($value);
                break;
            case 4:
                $this->setOperationtype($value);
                break;
            case 5:
                $this->setDsecure($value);
                break;
            case 6:
                $this->setExeccode($value);
                break;
            case 7:
                $this->setMessage($value);
                break;
            case 8:
                $this->setAmount($value);
                break;
            case 9:
                $this->setClientemail($value);
                break;
            case 10:
                $this->setCardcode($value);
                break;
            case 11:
                $this->setCardvaliditydate($value);
                break;
            case 12:
                $this->setCardfullname($value);
                break;
            case 13:
                $this->setCardtype($value);
                break;
            case 14:
                $this->setRefunded($value);
                break;
            case 15:
                $this->setRefundedby($value);
                break;
            case 16:
                $this->setCreatedAt($value);
                break;
            case 17:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = Be2billTransactionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setOrderId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCustomerId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setTransactionId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setOperationtype($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDsecure($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setExeccode($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setMessage($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setAmount($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setClientemail($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCardcode($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCardvaliditydate($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setCardfullname($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setCardtype($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setRefunded($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setRefundedby($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setCreatedAt($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setUpdatedAt($arr[$keys[17]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(Be2billTransactionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(Be2billTransactionTableMap::ID)) $criteria->add(Be2billTransactionTableMap::ID, $this->id);
        if ($this->isColumnModified(Be2billTransactionTableMap::ORDER_ID)) $criteria->add(Be2billTransactionTableMap::ORDER_ID, $this->order_id);
        if ($this->isColumnModified(Be2billTransactionTableMap::CUSTOMER_ID)) $criteria->add(Be2billTransactionTableMap::CUSTOMER_ID, $this->customer_id);
        if ($this->isColumnModified(Be2billTransactionTableMap::TRANSACTION_ID)) $criteria->add(Be2billTransactionTableMap::TRANSACTION_ID, $this->transaction_id);
        if ($this->isColumnModified(Be2billTransactionTableMap::OPERATIONTYPE)) $criteria->add(Be2billTransactionTableMap::OPERATIONTYPE, $this->operationtype);
        if ($this->isColumnModified(Be2billTransactionTableMap::DSECURE)) $criteria->add(Be2billTransactionTableMap::DSECURE, $this->dsecure);
        if ($this->isColumnModified(Be2billTransactionTableMap::EXECCODE)) $criteria->add(Be2billTransactionTableMap::EXECCODE, $this->execcode);
        if ($this->isColumnModified(Be2billTransactionTableMap::MESSAGE)) $criteria->add(Be2billTransactionTableMap::MESSAGE, $this->message);
        if ($this->isColumnModified(Be2billTransactionTableMap::AMOUNT)) $criteria->add(Be2billTransactionTableMap::AMOUNT, $this->amount);
        if ($this->isColumnModified(Be2billTransactionTableMap::CLIENTEMAIL)) $criteria->add(Be2billTransactionTableMap::CLIENTEMAIL, $this->clientemail);
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDCODE)) $criteria->add(Be2billTransactionTableMap::CARDCODE, $this->cardcode);
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDVALIDITYDATE)) $criteria->add(Be2billTransactionTableMap::CARDVALIDITYDATE, $this->cardvaliditydate);
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDFULLNAME)) $criteria->add(Be2billTransactionTableMap::CARDFULLNAME, $this->cardfullname);
        if ($this->isColumnModified(Be2billTransactionTableMap::CARDTYPE)) $criteria->add(Be2billTransactionTableMap::CARDTYPE, $this->cardtype);
        if ($this->isColumnModified(Be2billTransactionTableMap::REFUNDED)) $criteria->add(Be2billTransactionTableMap::REFUNDED, $this->refunded);
        if ($this->isColumnModified(Be2billTransactionTableMap::REFUNDEDBY)) $criteria->add(Be2billTransactionTableMap::REFUNDEDBY, $this->refundedby);
        if ($this->isColumnModified(Be2billTransactionTableMap::CREATED_AT)) $criteria->add(Be2billTransactionTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(Be2billTransactionTableMap::UPDATED_AT)) $criteria->add(Be2billTransactionTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(Be2billTransactionTableMap::DATABASE_NAME);
        $criteria->add(Be2billTransactionTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Be2Bill\Model\Be2billTransaction (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setOrderId($this->getOrderId());
        $copyObj->setCustomerId($this->getCustomerId());
        $copyObj->setTransactionId($this->getTransactionId());
        $copyObj->setOperationtype($this->getOperationtype());
        $copyObj->setDsecure($this->getDsecure());
        $copyObj->setExeccode($this->getExeccode());
        $copyObj->setMessage($this->getMessage());
        $copyObj->setAmount($this->getAmount());
        $copyObj->setClientemail($this->getClientemail());
        $copyObj->setCardcode($this->getCardcode());
        $copyObj->setCardvaliditydate($this->getCardvaliditydate());
        $copyObj->setCardfullname($this->getCardfullname());
        $copyObj->setCardtype($this->getCardtype());
        $copyObj->setRefunded($this->getRefunded());
        $copyObj->setRefundedby($this->getRefundedby());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Be2Bill\Model\Be2billTransaction Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildOrder object.
     *
     * @param                  ChildOrder $v
     * @return                 \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrder(ChildOrder $v = null)
    {
        if ($v === null) {
            $this->setOrderId(NULL);
        } else {
            $this->setOrderId($v->getId());
        }

        $this->aOrder = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildOrder object, it will not be re-added.
        if ($v !== null) {
            $v->addBe2billTransaction($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildOrder object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildOrder The associated ChildOrder object.
     * @throws PropelException
     */
    public function getOrder(ConnectionInterface $con = null)
    {
        if ($this->aOrder === null && ($this->order_id !== null)) {
            $this->aOrder = OrderQuery::create()->findPk($this->order_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrder->addBe2billTransactions($this);
             */
        }

        return $this->aOrder;
    }

    /**
     * Declares an association between this object and a ChildCustomer object.
     *
     * @param                  ChildCustomer $v
     * @return                 \Be2Bill\Model\Be2billTransaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomer(ChildCustomer $v = null)
    {
        if ($v === null) {
            $this->setCustomerId(NULL);
        } else {
            $this->setCustomerId($v->getId());
        }

        $this->aCustomer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCustomer object, it will not be re-added.
        if ($v !== null) {
            $v->addBe2billTransaction($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCustomer object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCustomer The associated ChildCustomer object.
     * @throws PropelException
     */
    public function getCustomer(ConnectionInterface $con = null)
    {
        if ($this->aCustomer === null && ($this->customer_id !== null)) {
            $this->aCustomer = CustomerQuery::create()->findPk($this->customer_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCustomer->addBe2billTransactions($this);
             */
        }

        return $this->aCustomer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->order_id = null;
        $this->customer_id = null;
        $this->transaction_id = null;
        $this->operationtype = null;
        $this->dsecure = null;
        $this->execcode = null;
        $this->message = null;
        $this->amount = null;
        $this->clientemail = null;
        $this->cardcode = null;
        $this->cardvaliditydate = null;
        $this->cardfullname = null;
        $this->cardtype = null;
        $this->refunded = null;
        $this->refundedby = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
        } // if ($deep)

        $this->aOrder = null;
        $this->aCustomer = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(Be2billTransactionTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildBe2billTransaction The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[Be2billTransactionTableMap::UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
