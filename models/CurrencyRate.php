<?php

namespace app\models;

/**
 * This is the model class for table "currency_rate".
 *
 * @property integer $id
 * @property integer $currency_id
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property string $rate
 *
 * @property Currency $currency
 */
class CurrencyRate extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_id', 'year', 'month', 'day', 'rate'], 'required'],
            [['currency_id', 'year', 'month', 'day'], 'integer'],
            [['rate'], 'number'],
            [['currency_id', 'year', 'month', 'day'], 'unique', 'targetAttribute' => ['currency_id', 'year', 'month', 'day'], 'message' => 'The combination of Currency ID, Year, Month and Day has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => __('ID'),
            'currency_id' => __('Currency ID'),
            'year' => __('Year'),
            'month' => __('Month'),
            'day' => __('Day'),
            'rate' => __('Rate'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

}
