<?php

namespace app\models;

use Yii;
use yii\db\Query;
use app\models\query\ClassificationQuery;

class Classification extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classification';
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
            [['name'], 'required'],
            [['type', 'notes'], 'string'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => __('ID'),
            'name' => __('Name'),
            'type' => __('Type'),
            'notes' => __('Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['classification_id' => 'id']);
    }


    /**
     * @inheritdoc
     * @return ClassificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClassificationQuery(get_called_class());
    }

}
