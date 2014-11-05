<?php

namespace Be2Bill\Model;

use Be2Bill\Model\Base\Be2billConfigQuery as BaseBe2billConfigQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'be2bill_config' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Be2billConfigQuery extends BaseBe2billConfigQuery
{
    public static function read($name, $default = null)
    {
        $value = self::create()->findOneByName($name);

        return $value ? $value->getValue() : $default;
    }

    public static function set($name, $value)
    {
        $config = self::create()->findOneByName($name);

        if (null == $config) {
            $config = new Be2billConfig();

            $config->setName($name);
        }

        $config->setValue($value)->save();
    }
}
