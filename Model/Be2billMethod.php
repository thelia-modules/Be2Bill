<?php

namespace Be2Bill\Model;

use Be2Bill\Model\Base\Be2billMethod as BaseBe2billMethod;

class Be2billMethod extends BaseBe2billMethod
{
    public function getData()
    {
        $data = @unserialize(parent::getData());

        return $data;
    }

    public function setData($v)
    {
        $data = $v;

        if (null === $data) {
            $data = $v;
        }

        $data = serialize($data);

        return parent::setData($data);
    }
}
