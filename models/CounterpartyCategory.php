<?php

namespace app\models;

use Yii;
use app\models\query\CounterpartyCategoryQuery;

class CounterpartyCategory extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'counterparty_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['notes'], 'string'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'notes' => 'Notes',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparties()
    {
        return $this->hasMany(Counterparty::className(), ['category_id' => 'id']);
    }


}
