<?php

namespace app\models;

use app\models\query\ClassificationQuery;

/**
 * This is the model class for table "classification".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $type
 * @property string $notes
 *
 * @property ClassificationCategory $category
 * @property Transaction[] $transactions
 */
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
            [['category_id'], 'integer'],
            [['type', 'notes'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClassificationCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => __('ID'),
            'category_id' => __('Category'),
            'name' => __('Name'),
            'type' => __('Type'),
            'notes' => __('Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ClassificationCategory::className(), ['id' => 'category_id']);
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
