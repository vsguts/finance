<?php

namespace app\behaviors;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Transaction;
use app\models\Counterparty;
use app\models\Classification;

class TransactionBehavior extends Behavior
{
    public $force_fill_counterparty = false;

    // Fixes Asset fields
    public $name;
    public $number;

    const TIMESTAMP_OFFSET = 59;

    public function attributeLabels()
    {
        return [
            'name' => __('FXA name'),
            'number' => __('FXA inventory number'),
        ];
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT   => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE   => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_DELETE    => 'afterDelete',
        ];
    }

    public function beforeInsert($event)
    {
        $this->calculateBalances();
    }

    public function beforeUpdate($event)
    {
        $this->calculateBalance();
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

    protected function calculateBalances()
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
