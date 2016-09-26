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


namespace Be2Bill\Service;

use Be2Bill\Be2Bill;
use Thelia\Core\Translation\Translator;

/**
 * Class OperationTypeService
 * @package Be2Bill\Service
 * @author Julien Chanséaume <julien@thelia.net>
 */
class OperationTypeService
{
    /** @var Translator $translator */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getOperationTypeList()
    {
        $operationTypeList = [
            // to be translatable from backoffice
            // ->trans("Payment")
            "payment" => "Payment",
            // ->trans("Authorization")
            "authorization" => "Authorization",
            // ->trans("Capture")
            "capture" => "Capture",
            // ->trans("Refund")
            "refund" => "Refund",
            // ->trans("Stop payment in installments")
            "stopntimes" => "Stop payment in installments",
        ];

        return $operationTypeList;
    }

    public function getTitle($operationType)
    {
        $operationTypeList = $this->getOperationTypeList();
        if (isset($operationTypeList[$operationType])) {
            return self::trans($operationTypeList[$operationType]);
        }

        return $this->trans('Unknown operation type');
    }

    protected function trans($id, $parameters = [])
    {
        if (null === $this->translator) {
            $this->translator = Translator::getInstance();
        }

        return $this->translator->trans($id, $parameters, Be2Bill::MODULE_DOMAIN);
    }
}