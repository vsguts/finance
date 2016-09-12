<?php

namespace app\models\query;

use Yii;
use app\models\Account;

class AccountQuery extends ActiveQuery
{
    protected $permission_included = false;

    public function active($current_id = null)
    {
        $condition = ['status' => Account::STATUS_ACTIVE];
        if ($current_id) {
            $condition = [
                'or',
                $condition,
                ['account.id' => $current_id],
            ];
        }

        return $this->andWhere($condition);
    }

    public function permission()
    {
        if (!$this->permission_included) {
            $this->permission_included = true;
            $account_ids = Yii::$app->authManager->getUserObjects('accounts');
            if ($account_ids != 'all') {
                $this->andWhere(['account.id' => $account_ids ?: 0]);
            }
        }
        return $this;
    }

    public function dependent()
    {
        return $this
            ->permission()
            ->joinWith(['currency'])
        ;
    }

    public function sorted()
    {
        return $this
            ->dependent()
            ->orderBy([
                'account.name' => SORT_ASC,
                'currency.code' => SORT_ASC,
                'bank' => SORT_ASC,
            ])
        ;
    }

    public function scroll($params = [])
    {
        $data = [];
        $field = !empty($params['field']) ? $params['field'] : 'fullName';
        foreach ($this->sorted()->all() as $model) {
            $data[$model->id] = $model->$field;
        }
        asort($data);
        return parent::scroll($params, $data);
    }

}
