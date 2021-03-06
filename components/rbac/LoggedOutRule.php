<?php

namespace app\components\rbac;

class LoggedOutRule extends AbstractRule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return empty($user);
    }
}
