<?php

namespace app\models;

use Yii;
use app\models\query\AccountQuery;

class Account extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'app\behaviors\LookupBehavior',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency_id', 'name'], 'required'],
            [['currency_id'], 'integer'],
            [['name', 'bank', 'account_number'], 'string', 'max' => 64],
            [['import_processor', 'notes'], 'string'],
            [['init_balance'], 'default', 'value' => 0],
            [['init_balance'], 'number'],

            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'currency_id' => __('Currency'),
            'name' => __('Name'), 
            'bank' => __('Bank'),
            'account_number' => __('Account number'),
            'init_balance' => __('Init Balance'),
            'import_processor' => __('Import processor'),
            'notes' => __('Notes'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['account_id' => 'id']);
    }


    /**
     * @inheritdoc
     * @return AccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountQuery(get_called_class());
    }


    /**
     * Virtual fields
     */

    public function getFullName()
    {
        return implode(' / ', array_filter([$this->name, $this->currency->code, $this->bank]));
    }

}
