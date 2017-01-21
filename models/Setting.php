<?php

namespace app\models;

use app\models\components\StorageAccessTrait;
use app\models\form\SettingsForm;

/**
 * This is the model class for table "setting".
 *
 * @property string $name
 * @property string $value
 */
class Setting extends AbstractModel
{
    use StorageAccessTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['value'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => __('Name'),
            'value' => __('Value'),
        ];
    }

    public static function settings()
    {
        return (new SettingsForm)->attributes;
    }

}
