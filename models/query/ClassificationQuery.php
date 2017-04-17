<?php

namespace app\models\query;

class ClassificationQuery extends ActiveQuery
{

    public function inflow()
    {
        return $this->andWhere(['type' => 'inflow']);
    }

    public function outflow()
    {
        return $this->andWhere(['type' => 'outflow']);
    }

    public function transfer()
    {
        return $this->andWhere(['type' => 'transfer']);
    }

    public function conversion()
    {
        return $this->andWhere(['type' => 'conversion']);
    }

    public function inout()
    {
        return $this->andWhere(['type' => ['inflow', 'outflow']]);
    }

    public function simple()
    {
        return $this
            ->select(['id', 'name', 'type'])
            ->indexBy('id')
            ->asArray();
    }

}
