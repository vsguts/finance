<?php

namespace app\models\query;

class ClassificationQuery extends ActiveQuery
{

    public function dependent()
    {
        return $this->joinWith('category');
    }

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

    public function sorted()
    {
        return $this->orderBy(['name' => SORT_ASC]);
    }

    public function scroll($params = [])
    {
        $data = [];
        $field = !empty($params['field']) ? $params['field'] : 'extendedName';
        foreach ($this->dependent()->sorted()->all() as $model) {
            $data[$model->id] = $model->$field;
        }
        asort($data);
        $params['data'] = $data;
        return parent::scroll($params);
    }

}
