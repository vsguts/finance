<?php

namespace app\models;

use app\models\components\LookupTrait;

/**
 * This is the model class for table "counterparty".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $notes
 *
 * @property CounterpartyCategory $category
 * @property Transaction[] $transactions
 */
class Counterparty extends AbstractModel
{
    use LookupTrait;

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
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category_id'], 'integer'],
            [['notes'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CounterpartyCategory::class, 'targetAttribute' => ['category_id' => 'id']],
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
        return $this->hasOne(CounterpartyCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['counterparty_id' => 'id']);
    }


}
