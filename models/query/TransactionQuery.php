<?php

namespace app\models\query;

use Yii;

class TransactionQuery extends ActiveQuery
{
    protected $permission_included = false;

    public function permission()
    {
        if (!$this->permission_included) {
            $this->permission_included = true;
            $account_ids = Yii::$app->authManager->getUserObjects('accounts');
            if ($account_ids != 'all') {
                $this->andWhere(['transaction.account_id' => $account_ids ?: 0]);
            }
        }
        return $this;
    }

    public function dependent()
    {
        return $this
            ->permission()
            ->joinWith([
                'account',
                'account.currency',
                'counterparty',
                'classification',
            ])
        ;
    }

}
