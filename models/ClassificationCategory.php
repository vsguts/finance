<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "classification_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $notes
 *
 * @property Classification[] $classifications
 */
class ClassificationCategory extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classification_category';
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
            'id' => __('ID'),
            'name' => __('Name'),
            'notes' => __('Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassifications()
    {
        return $this->hasMany(Classification::class, ['category_id' => 'id']);
    }
}
