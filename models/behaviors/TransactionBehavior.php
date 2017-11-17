<?php

namespace app\models\behaviors;

use app\models\Transaction;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class TransactionBehavior extends Behavior
{
    /**
     * @var Transaction
     */
    public $owner;

    const TIMESTAMP_OFFSET = 59;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterDelete',
        ];
    }

    public function beforeSave($event)
    {
        $this->calculateBalances();
    }

    public function afterDelete($event)
    {
        $model = $this->owner;
        if ($model->related) {
            $model->related->delete();
        }
    }

    public function calculateBalance()
    {
        $model = $this->owner;
        $model->balance = $model->opening_balance + $model->inflow - $model->outflow;
    }

    public function calculateBalances()
    {
        $model = $this->owner;

        if (!floatval($model->opening_balance) && !floatval($model->balance)) {
            $previos_transaction = $model::find()
                ->where([
                    'and',
                    ['account_id' => $model->account_id],
                    ['<=', 'timestamp', $model->timestamp + self::TIMESTAMP_OFFSET],
                ])
                ->orderBy(['timestamp' => SORT_DESC, 'id' => SORT_DESC])
                ->limit(1)
                ->one();
            if ($previos_transaction) {
                $model->opening_balance = $previos_transaction->balance;
            } else {
                $model->opening_balance = $model->account->init_balance;
            }
        }

        if (floatval($model->balance)) {
            $model->opening_balance = $model->balance - $model->inflow + $model->outflow;
        } else {
            $this->calculateBalance();
        }
    }

}
