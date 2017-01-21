<?php

namespace app\helpers;

use yii\data\DataProviderInterface;

class Statistic
{
    public static function sum(DataProviderInterface $provider, $field)
    {
        $result = 0;
        foreach ($provider->getModels() as $model) {
            $result += $model->$field;
        }

        return $result;
    }
}
