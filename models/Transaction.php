<?php

namespace app\models;

use Yii;
use app\models\query\TransactionQuery;

/**
 * This is the model class for table "transaction".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $classification_id
 * @property integer $counterparty_id
 * @property integer $user_id
 * @property integer $related_id
 * @property integer $timestamp
 * @property integer $created_at
 * @property string $inflow
 * @property string $outflow
 * @property string $opening_balance
 * @property string $balance
 * @property integer $has_attachments
 * @property string $description
 *
 * @property Account $account
 * @property Classification $classification
 * @property Counterparty $counterparty
 * @property Transaction $related
 * @property Transaction[] $transactions
 * @property User $user
 */
class Transaction extends AbstractModel
{

    const SCENARIO_RECALCULATE = 'recalculate';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
    }

    public function behaviors()
    {
        return [
            'app\behaviors\TransactionBehavior',
            'app\behaviors\TimestampConvertBehavior',
            [
                'class' => 'app\behaviors\TimestampBehavior',
                'updatedAtAttribute' => false,
            ],
            'app\behaviors\AttachmentsBehavior',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [

            // Defaults
            [
                ['timestamp'],
                'default', 'value' => time(),
                'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_RECALCULATE],
            ],
            [
                ['user_id'],
                'default',
                'value' => function($model) {
                    return Yii::$app->user->getId();
                },
            ],

            // Required
            [
                ['account_id', 'timestamp'],
                'required'
            ],
            [
                ['id'],
                'canUpdateValidation',
                'on' => self::SCENARIO_DEFAULT,
                'skipOnEmpty' => false, 
                'skipOnError' => false,
            ],
            [
                ['classification_id'],
                'required',
                'on' => self::SCENARIO_DEFAULT,
            ],
            [
                ['inflow'],
                'required',
                'on' => self::SCENARIO_DEFAULT,
                'when' => function($model){
                    return in_array($model->classification_id, Classification::find()->inflow()->ids());
                },
            ],
            [
                ['outflow'],
                'required',
                'on' => self::SCENARIO_DEFAULT,
                'when' => function($model){
                    return in_array($model->classification_id, Classification::find()->outflow()->ids());
                },
            ],
            [ // We need inflow or outflow
                ['outflow'],
                'outflowValidation',
                'skipOnEmpty' => false, 
                'skipOnError' => false,
            ],

            // Common
            [
                ['account_id', 'classification_id', 'user_id', 'counterparty_id', 'related_id', 'has_attachments'],
                'integer',
            ],
            [
                ['inflow', 'outflow', 'opening_balance', 'balance'],
                'number',
            ],
            [
                ['description'],
                'string',
            ],

            /*
            // Relations
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['classification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classification::className(), 'targetAttribute' => ['classification_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['counterparty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counterparty::className(), 'targetAttribute' => ['counterparty_id' => 'id']],
            [['related_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::className(), 'targetAttribute' => ['related_id' => 'id']],
            */
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => __('ID'),
            'account_id' => __('Account'),
            'classification_id' => __('Classification'),
            'counterparty_id' => __('Counterparty'),
            'related_id' => __('Related'),
            'user_id' => __('User'),
            'timestamp' => __('Time'),
            'inflow' => __('Inflow'),
            'outflow' => __('Outflow'),
            'opening_balance' => __('Opening balance'),
            'balance' => __('Closing balance'),
            'has_attachments' => __('Has attachments'),
            'description' => __('Description'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassification()
    {
        return $this->hasOne(Classification::className(), ['id' => 'classification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparty()
    {
        return $this->hasOne(Counterparty::className(), ['id' => 'counterparty_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelated()
    {
        return $this->hasOne(self::className(), ['id' => 'related_id']);
    }

    /**
     * @inheritdoc
     * @return TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionQuery(get_called_class());
    }


    /**
     * Virtual fields
     */

    public function getCanUpdate()
    {
        if ($this->isNewRecord) {
            return true;
        }

        $days = Yii::$app->params['transaction_update_days'];
        return $days * SECONDS_IN_DAY + $this->created_at > time();
    }


    /**
     * Virtual fields: Formatter
     */

    public function getInflowValue()
    {
        if (floatval($this->inflow)) {
            return Yii::$app->formatter->asDecimal($this->inflow, 2) . ' ' . $this->getCurrencySymbol();
        }
    }

    public function getOutflowValue()
    {
        if (floatval($this->outflow)) {
            return Yii::$app->formatter->asDecimal($this->outflow, 2) . ' ' . $this->getCurrencySymbol();
        }
    }

    public function getOpeningBalanceValue()
    {
        // return Yii::$app->formatter->asCurrency($this->opening_balance, $this->account->currency->code);
        return Yii::$app->formatter->asDecimal($this->opening_balance, 2) . ' ' . $this->getCurrencySymbol();
    }

    public function getBalanceValue()
    {
        return Yii::$app->formatter->asDecimal($this->balance, 2) . ' ' . $this->getCurrencySymbol();
    }

    protected function getCurrencySymbol()
    {
        return $this->account ? $this->account->currency->symbol : null;
    }


    /**
     * Virtual fields: Converter
     */

    public function getInflowConverted($currency_id = null)
    {
        return $this->convert($this->inflow, $currency_id);
    }

    public function getOutflowConverted($currency_id = null)
    {
        return $this->convert($this->outflow, $currency_id);
    }

    public function getOpeningBalanceConverted($currency_id = null)
    {
        return $this->convert($this->opening_balance, $currency_id);
    }

    public function getBalanceConverted($currency_id = null)
    {
        return $this->convert($this->balance, $currency_id);
    }

    public function convert($value, $currency_id = null)
    {
        $value = floatval($value);
        if ($currency_id === null) {
            $currency_id = Yii::$app->currency->getBaseCurrencyId();
        }

        if ($value && $this->account->currency_id != $currency_id) {
            return Yii::$app->currency->convert($value, $this->account->currency_id, $currency_id, $this->timestamp);
        }

        return $value;
    }


    /**
     * Common methods
     */

    public function canUpdateValidation($attribute)
    {
        $allow = $this->getCanUpdate();
        if (!$allow) {
            $this->addError($attribute, __('Update time is expired'));
        }
        return $allow;
    }

    public function outflowValidation($attribute)
    {
        $inflow_empty = self::isEmpty($this->inflow);
        $outflow_empty = self::isEmpty($this->outflow);
        $allow = $inflow_empty != $outflow_empty;
        if (!$allow) {
            $this->addError($attribute, __('You need to fill inflow OR outflow'));
        }
        return $allow;
    }

    public static function isEmpty($value)
    {
        return empty(floatval($value));
    }
}
