<?php

namespace app\models\behaviors;

use app\models\Account;
use app\models\Transaction;
use yii\base\Behavior;

class AccountBehavior extends Behavior
{

    /**
     * @var Account
     */
    public $owner;

    public function recalculateTransactionBalances()
    {
        $balance = $this->owner->init_balance;

        $query = Transaction::find()
            ->where(['bank_account_id' => $this->owner->id])
            ->sorted(SORT_ASC);

        $num = function ($val) {
            return round($val, 2);
        };

        /** @var \app\models\Transaction $transaction */
        foreach ($query->each() as $transaction) {
            $transaction->scenario = Transaction::SCENARIO_RECALCULATE;

            $current_opening_balance = $transaction->opening_balance;
            $current_balance = $transaction->balance;

            $transaction->opening_balance = $balance;
            $transaction->calculateBalance();
            $balance = $transaction->balance;

            if (
                $num($current_opening_balance) != $num($transaction->opening_balance)
                || $num($current_balance) != $num($transaction->balance)
            ) {
                $transaction->save();
            }
        }
    }

}
