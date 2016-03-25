<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Be2Bill\Events;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Order;

/**
 * Class PaymentParametersEvent
 * @package Be2Bill\Events
 * @author Julien Chanséaume <julien@thelia.net>
 */
class PaymentParametersEvent extends ActionEvent
{
    /** @var Order */
    protected $order;

    /** @var array be2bill parameters */
    protected $parameters;

    /** @var string payment method */
    protected $method;

    /**
     * PaymentParametersEvent constructor.
     * @param string $method
     * @param Order $order
     * @param array $parameters
     */
    public function __construct(Order $order, $method, array $parameters)
    {
        $this->order = $order;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

}
