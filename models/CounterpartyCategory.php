<?php

namespace app\models;

/**
 * This is the model class for table "counterparty_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $notes
 *
 * @property Counterparty[] $counterparties
 */
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
            'id' => __('ID'),
            'name' => __('Name'),
            'notes' => __('Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparties()
    {
        return $this->hasMany(Counterparty::class, ['category_id' => 'id']);
    }

}
