<?php

namespace app\models;

use app\models\behaviors\AccountBehavior;
use app\models\components\LookupTrait;
use app\models\contracts\LabeledModel;
use app\models\query\AccountQuery;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property integer $currency_id
 * @property string $status
 * @property string $name
 * @property string $bank
 * @property string $account_number
 * @property string $init_balance
 * @property string $import_processor
 * @property string $notes
 * @property bool $label_enabled
 * @property string $label_bg_color
 * @property string $label_text_color
 *
 * @property Currency $currency
 * @property Transaction[] $transactions
 *
 * @mixin AccountBehavior
 */
class Account extends AbstractModel implements LabeledModel
{
    use LookupTrait;

    const STATUS_ACTIVE = 'active';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            AccountBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency_id', 'name'], 'required'],
            [['currency_id'], 'integer'],
            [['name', 'bank', 'account_number'], 'string', 'max' => 64],
            [['label_bg_color', 'label_text_color'], 'string', 'max' => 7],
            [['status'], 'default', 'value' => 'active'],
            [['status'], 'in', 'range' => ['active', 'disabled']],
            [['label_enabled'], 'boolean'],
            [['import_processor', 'notes'], 'string'],
            [['init_balance'], 'number'],
            [['init_balance'], 'default', 'value' => 0],
            [['label_enabled'], 'default', 'value' => false],
            [['label_bg_color'], 'default', 'value' => '#777777'],
            [['label_text_color'], 'default', 'value' => '#ffffff'],

            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency_id' => 'id']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'currency_id' => __('Currency'),
            'status' => __('Status'), 
            'name' => __('Name'), 
            'bank' => __('Bank'),
            'account_number' => __('Account number'),
            'init_balance' => __('Init balance'),
            'import_processor' => __('Import processor'),
            'notes' => __('Notes'),
            'label_enabled' => __('Enable label'),
            'label_text_color' => __('Text color'),
            'label_bg_color' => __('Background color'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['account_id' => 'id']);
    }


    /**
     * @inheritdoc
     * @return AccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountQuery(get_called_class());
    }


    /**
     * Virtual fields
     */

    public function getFullName()
    {
        return implode(' / ', array_filter([$this->name, $this->currency->code, $this->bank]));
    }

    public function getLabelEnabled(): bool
    {
        return (bool) $this->label_enabled;
    }

    public function getLabelBgColor(): string
    {
        return $this->label_bg_color;
    }

    public function getLabelTextColor(): string
    {
        return $this->label_text_color;
    }
}
