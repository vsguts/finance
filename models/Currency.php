<?php

namespace app\models;

/**
 * This is the model class for table "currency".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $symbol
 *
 * @property Account[] $accounts
 * @property CurrencyRate[] $currencyRates
 */
class Currency extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 32],
            [['symbol'], 'string', 'max' => 64],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'name' => __('Name'),
            'code' => __('Code'),
            'symbol' => __('Symbol'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['currency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyRates()
    {
        return $this->hasMany(CurrencyRate::className(), ['currency_id' => 'id']);
    }

}
