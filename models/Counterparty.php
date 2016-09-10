<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\query\CounterpartyQuery;

class Counterparty extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'counterparty';
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
        return [
            [
                ['name'],
                'required'
            ],
            [
                ['category_id'],
                'integer'
            ],
            [
                ['notes'],
                'string'
            ],
            [
                ['name'],
                'string',
                'max' => 128
            ],
            
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CounterpartyCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'category_id' => __('Category'),
            'name' => __('Name'),
            'notes' => __('Notes'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CounterpartyCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['counterparty_id' => 'id']);
    }


}
