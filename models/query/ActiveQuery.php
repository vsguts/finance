<?php

namespace app\models\query;

class ActiveQuery extends \yii\db\ActiveQuery
{
    protected static $idsCache = [];

    public function ids()
    {
        $key = $this->createCommand()->getRawSql();

        if (!isset(self::$idsCache[$key])) {
            self::$idsCache[$key] = $this
                ->select('id')
                ->column();
        }

        return self::$idsCache[$key];
    }

    public function scroll($params = [])
    {
        $params = array_merge([
            'field' => 'name',
            'empty' => false,
            'without' => false,
            'without_key' => '0',
            'all' => false,
            'all_key' => '0',
            'data' => null,
        ], $params);

        $data = $params['data'];

        if (is_null($data)) {
            $data = $this
                ->select($params['field'])
                ->orderBy([$params['field'] => SORT_ASC])
                ->indexBy('id')
                ->column();
        }

        if ($params['all']) {
            $data = [$params['all_key'] => '- ' . __('All') . ' -'] + $data;
        }

        if ($params['without']) {
            $data = [$params['without_key'] => '- ' . __('Without') . ' -'] + $data;
        }

        if ($params['empty']) {
            $label = ' -- ';
            if (is_string($params['empty'])) {
                $label = ' - ' . $params['empty'] . ' - ';
            }
            $data = ['' => $label] + $data;
        }

        return $data;
    }

    public function scrollOne($id, $params = [])
    {
        $data = $this->scroll($params);
        return isset($data[$id]) ? $data[$id] : null;
    }

    /**
     * Override this if need
     * @return self
     */
    public function permission()
    {
        return $this;
    }

    /**
     * Override this if need
     * Default sorting by name field
     * @return self
     */
    public function sorted()
    {
        return $this->orderBy(['name' => SORT_ASC]);
    }

}
