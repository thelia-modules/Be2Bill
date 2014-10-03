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

namespace Be2Bill;


use Be2Bill\Model\Be2billConfigQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Module\BaseModule;

class Be2Bill extends AbstractPaymentModule
{

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */


    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, array(
            __DIR__ . DS . 'Config'.DS.'thelia.sql'
        ));
    }
    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is send to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway. On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  \Thelia\Model\Order $order processed order
     * @return null|\Thelia\Core\HttpFoundation\Response
     */
    public function pay(Order $order)
    {
        $be2bill_params = $this->getBe2BillParameters($order, 'payment');
        $be2bill_params['HASH'] = $this->be2BillHash($be2bill_params);

        if(false === $platformUrl = Be2billConfigQuery::read('url', false)){
            throw new \InvalidArgumentException("The platform URL is not defined, please check Be2Bill module configuration.");
        }

        return $this->generateGatewayFormResponse($order, $platformUrl, $be2bill_params);

    }

    public function getBe2BillParameters(Order $order, $operationtype)
    {

        $amount = $order->getTotalAmount();

        $customer = $order->getCustomer();

        $address = $customer->getDefaultAddress();

        $be2bill_params = array(
            'IDENTIFIER'    => Be2billConfigQuery::read('identifier'),
            'OPERATIONTYPE' => $operationtype,
            'DESCRIPTION'   => Be2billConfigQuery::read('description'),
            'ORDERID'       => $order->getId(),
            'AMOUNT'        => $amount,
            'VERSION'       => '2.0',
            'CLIENTIDENT'   => $customer->getId(),
            'CLIENTADDRESS' => trim($address->getAddress1() . ' ' . $address->getAddress2() . ' ' . $address->getAddress3()),
            'CLENTEMAIL'    => $customer->getEmail(),
            '3DSECURE'      => Be2billConfigQuery::read('3dsecure')
        );

        return $be2bill_params;
    }

    public static function be2BillHash(array $params){

        ksort($params);

        $password = Be2billConfigQuery::read('password');
        $clear_string = $password;
        foreach ($params as $key => $value)
        {
            $clear_string .= $key . '=' . $value . $password;
        }
        return hash('sha256', $clear_string);
    }



    /**
     *
     * This method is call on Payment loop.
     *
     * If you return true, the payment method will de display
     * If you return false, the payment method will not be display
     *
     * @return boolean
     */
    public function isValidPayment()
    {
        return true;
    }
}
