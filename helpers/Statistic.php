<?php

namespace app\helpers;

use yii\base\Object;
use yii\data\DataProviderInterface;

class Statistic extends Object
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
