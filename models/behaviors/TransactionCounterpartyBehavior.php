<?php

namespace common\models\behaviors\transactions;

use common\helpers\Text;
use common\models\BankAccount;
use common\models\CounterpartyRule;
use common\models\Transaction;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class TransactionCounterpartyBehavior extends Behavior
{
    /**
     * @var Transaction
     */
    public $owner;

    public $forceFillCounterparty = false;

    protected static $counterpartyRulesSchema = null;

    protected static $fillCounterpartyBankAccountIds;


    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'fillCounterParty',
        ];
    }

    public function fillCounterParty()
    {
        $model = $this->owner;

        if (!self::$fillCounterpartyBankAccountIds) {
            self::$fillCounterpartyBankAccountIds = BankAccount::find()->nonCash()->nonTransit()->ids();
        }

        if (in_array($model->bank_account_id, self::$fillCounterpartyBankAccountIds)) { // Bank
            if (
                !$model->counterparty_manual
                && (!$model->counterparty_id || $this->forceFillCounterparty)
            ) {
                $model->counterparty_id = $this->findCounterpartyId();
            }
        } else { // Cash, Transit
            $model->counterparty_id = null;
        }
    }

    private function findCounterpartyId()
    {
        $model = $this->owner;
        if ($transaction_value = floatval($model->inflow)) {
            $transaction_direction = CounterpartyRule::DIRECTION_IN;
        } else {
            $transaction_direction = CounterpartyRule::DIRECTION_OUT;
            $transaction_value = floatval($model->outflow);
        }

        $transaction_description = preg_replace('/\s+/', ' ', strtolower(trim($model->description)));

        $schema = self::getCounterpartyRulesSchema($model->bank_account_id, $transaction_direction);

        foreach ($schema as $counterparty_id => $rules) {
            foreach ($rules as $conditions) {
                $conditions_passed = true;
                foreach ($conditions as $name => $value) {
                    $passed = false;
                    if ($name == 'value_from') {
                        if ($value <= $transaction_value) {
                            $passed = true;
                        }
                    } elseif ($name == 'value_to') {
                        if ($value >= $transaction_value) {
                            $passed = true;
                        }
                    } elseif ($name == 'equal') {
                        foreach ($value as $line) {
                            if ($transaction_description == $line) {
                                $passed = true;
                                break;
                            }
                        }
                    } elseif ($name == 'contains') {
                        foreach ($value as $line) {
                            if (strpos($transaction_description, $line) !== false) {
                                $passed = true;
                                break;
                            }
                        }
                    } elseif ($name == 'excludes') {
                        $passed = true;
                        foreach ($value as $line) {
                            if (strpos($transaction_description, $line) !== false) {
                                $passed = false;
                                break;
                            }
                        }
                    } elseif ($name == 'empty') {
                        if (empty($transaction_description)) {
                            $passed = true;
                        }
                    }
                    if (!$passed) {
                        $conditions_passed = false;
                    }
                }
                if ($conditions_passed) {
                    return $counterparty_id;
                }
            }
        }
    }

    private static function getCounterpartyRulesSchema($bank_account_id, $direction)
    {
        if (!isset(self::$counterpartyRulesSchema)) {
            self::$counterpartyRulesSchema = [];

            $rules = CounterpartyRule::find()
                ->sorted(SORT_ASC)
                ->asArray()
                ->all();

            $all_bank_account_ids = BankAccount::find()->ids();

            foreach ($rules as $rule) {

                // Numbers
                foreach (['value_from', 'value_to'] as $field) {
                    $rule[$field] = floatval($rule[$field]);
                }

                // Text lines
                foreach (['equal', 'contains', 'excludes'] as $field) {
                    if ($rule[$field]) {
                        $rule[$field] = Text::prepareLines($rule[$field], true);
                    }
                }

                // All fields
                $conditions = [];
                foreach (['value_from', 'value_to', 'equal', 'contains', 'excludes', 'empty'] as $field) {
                    if ($rule[$field]) {
                        $conditions[$field] = $rule[$field];
                    }
                }

                $bank_account_ids = explode(',', $rule['bank_account_ids']);
                if (in_array('all', $bank_account_ids)) {
                    $bank_account_ids = $all_bank_account_ids;
                }

                if ($conditions && $bank_account_ids) {
                    foreach ($bank_account_ids as $account_id) {
                        if ($rule['inflow']) {
                            self::$counterpartyRulesSchema[$account_id][CounterpartyRule::DIRECTION_IN][$rule['counterparty_id']][] = $conditions;
                        }
                        if ($rule['outflow']) {
                            self::$counterpartyRulesSchema[$account_id][CounterpartyRule::DIRECTION_OUT][$rule['counterparty_id']][] = $conditions;
                        }
                    }
                }
            }
        }

        return isset(self::$counterpartyRulesSchema[$bank_account_id][$direction]) ? self::$counterpartyRulesSchema[$bank_account_id][$direction] : [];
    }


}
