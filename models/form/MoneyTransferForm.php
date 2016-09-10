<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\Account;
use app\models\Transaction;
use app\models\Classification;

class MoneyTransferForm extends Model
{
    public $account_id_from;
    public $account_id_to;
    public $classification_id;
    public $value_from;
    public $value;
    public $timestamp;
    public $description;

    // Update
    public $transaction;
    public $transaction_id_from;
    public $transaction_id_to;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'app\behaviors\TimestampConvertBehavior',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Defaults
            [
                ['timestamp'],
                'default', 'value' => time()
            ],
            [
                ['classification_id'],
                'default',
                'value' => function($model) {
                    if ($this->currenciesDiffer()) {
                        $ids = Classification::find()->conversion()->ids();
                    } else {
                        $ids = Classification::find()->transfer()->ids();
                    }
                    return $ids ? reset($ids) : null;
                },
            ],

            // Required
            [
                ['account_id_from', 'account_id_to', 'value', 'timestamp'],
                'required'
            ],
            [
                ['value_from'],
                'required',
                'when' => function($model) {
                    return $model->currenciesDiffer();
                }
            ],
            [
                ['value_from'],
                'prepareValueFrom',
                'skipOnEmpty' => false, 
                'skipOnError' => false,
            ],

            // Common
            [
                ['account_id_from', 'account_id_to', 'classification_id'],
                'integer',
            ],
            [
                ['value_from', 'value'],
                'number',
            ],
            [
                ['description'],
                'string',
            ],

        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id_from' => __('From account'),
            'account_id_to' => __('To account'),
            'classification_id' => __('Classification'),
            'value_from' => __('Value from'),
            'value' => __('Value to'),
            'timestamp' => __('Date'),
            'description' => __('Description'),
            'user' => __('User'),
        ];
    }

    public static function findOne($id)
    {
        $transaction = Transaction::findOne($id);
        if (!$transaction || !$transaction->related) {
            return false;
        }
        if (floatval($transaction->outflow)) {
            $from = $transaction;
            $to = $transaction->related;
        } else {
            $to = $transaction;
            $from = $transaction->related;
        }

        $model = new MoneyTransferForm;
        $model->load($from->attributes, '');
        $model->transaction = $transaction;
        $model->transaction_id_from = $from->id;
        $model->transaction_id_to = $to->id;
        $model->account_id_from = $from->account_id;
        $model->account_id_to = $to->account_id;
        $model->value_from = $from->outflow;
        $model->value = $to->inflow;
        return $model;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        if ($this->transaction_id_from) {
            $from = Transaction::findOne($this->transaction_id_from);
        } else {
            $from = new Transaction;
        }
        $from->account_id = $this->account_id_from;

        if ($this->transaction_id_to) {
            $to = Transaction::findOne($this->transaction_id_to);
        } else {
            $to = new Transaction;
        }
        $to->account_id = $this->account_id_to;

        // Common fields: classification_id, timestamp, description
        $from->load($this->attributes, '');
        $to->load($this->attributes, '');

        $from->outflow = $this->value_from;
        $to->inflow = $this->value;

        $res_from = $from->save();
        $res_to = $to->save();

        if (!$res_from) {
            $this->addErrors($from->errors);
        }

        if (!$res_to) {
            $this->addErrors($to->errors);
        }

        if ($res_from && $res_to) {
            if (!$this->transaction_id_from) {
                $from->related_id = $to->id;
                $from->save();
            }
            if (!$this->transaction_id_to) {
                $to->related_id = $from->id;
                $to->save();
            }
        }

        return $res_from && $res_to;
    }

    public function prepareValueFrom($attribute, $params)
    {
        if (!$this->currenciesDiffer()) {
            $this->value_from = $this->value;
        }
        return true;
    }

    public function currenciesDiffer()
    {
        return self::getAccountCurrency($this->account_id_from) != self::getAccountCurrency($this->account_id_to);
    }

    protected static $_account_currencies = [];

    protected static function getAccountCurrency($account_id)
    {
        if (!$account_id) {
            return 0;
        }
        if (!isset(self::$_account_currencies[$account_id])) {
            self::$_account_currencies[$account_id] = Account::findOne($account_id)->currency->id;
        }
        return self::$_account_currencies[$account_id];
    }
}
