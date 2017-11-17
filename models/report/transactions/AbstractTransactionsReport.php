<?php

namespace app\models\report\transactions;

use app\models\Account;
use app\models\Classification;
use app\models\ClassificationCategory;
use app\models\Counterparty;
use app\models\report\ReportAbstract;
use app\models\Transaction;

abstract class AbstractTransactionsReport extends ReportAbstract
{
    protected $previousTransactions = [];
    protected $accounts;
    protected $_transactions;
    protected $transactionsDetails;
    protected $_classifications;
    protected $_classification_categories;
    protected $_counterparties;

    protected function getAccounts()
    {
        if (!$this->accounts) {
            $this->accounts = Account::find()
                ->permission()
                ->sorted(SORT_ASC)
                ->indexBy('id')
                ->all();
        }

        return $this->accounts;
    }

    /**
     * @return Transaction[]
     */
    protected function getTransactions()
    {
        if (!$this->_transactions) {
            $this->getAccounts();
            $this->_transactions = Transaction::find()
                ->joinWith('account')
                ->where([
                    'and',
                    ['account_id' => array_keys($this->accounts)],
                    ['>=', 'timestamp', $this->timestamp],
                    ['<=', 'timestamp', $this->timestamp_to + SECONDS_IN_DAY - 1],
                ])
                ->sorted(SORT_ASC)
                ->all();
        }

        return $this->_transactions;
    }

    protected function getTransactionsData()
    {
        if (!isset($this->transactionsDetails)) {
            $this->transactionsDetails = [];
            $accounts = $this->getAccounts();
            $query = Transaction::find()
                ->select([
                    'transaction.id',
                    'transaction.classification_id',
                    'transaction.counterparty_id',
                    'transaction.balance',
                    'transaction.opening_balance',
                    'transaction.inflow',
                    'transaction.outflow',
                    'transaction.timestamp',
                    'transaction.account_id'
                ])
                ->joinWith('account')
                ->where([
                    'and',
                    ['account_id' => array_keys($accounts)],
                    ['>=', 'timestamp', $this->getQueryTimestampFrom()],
                    ['<=', 'timestamp', $this->getQueryTimestampTo()],
                ])
                ->sorted(SORT_ASC);
            /** @var Transaction $transaction */
            foreach ($query->each() as $transaction) {
                $this->transactionsDetails[$transaction->id] = [
                    'account_id' => $transaction->account_id,
                    'classification_id' => $transaction->classification_id,
                    'counterparty_id' => $transaction->counterparty_id,
                    'opening_balance' => $transaction->opening_balance,
                    'inflow' => $transaction->inflow,
                    'outflow' => $transaction->outflow,
                    'balance' => $transaction->balance,
                    'inflow_converted' => $transaction->inflowConverted,
                    'outflow_converted' => $transaction->outflowConverted,
                    'balance_converted' => $transaction->balanceConverted,
                    'timestamp' => $transaction->timestamp,

                    'account.currency_id' => $transaction->account->currency_id,
                ];
            }
        }

        return $this->transactionsDetails;
    }

    protected function getAccountPreviousTransaction($account_id)
    {
        if (!array_key_exists($account_id, $this->previousTransactions)) {
            $this->previousTransactions[$account_id] = Transaction::find()
                ->where([
                    'and',
                    ['account_id' => $account_id],
                    ['<', 'timestamp', $this->timestamp],
                ])
                ->sorted(SORT_DESC)
                ->limit(1)
                ->one();
        }

        return $this->previousTransactions[$account_id];
    }

    /**
     * @return Classification[]
     */
    protected function getClassifications()
    {
        if (!$this->_classifications) {
            $this->_classifications = Classification::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_classifications;
    }

    /**
     * @return ClassificationCategory[]
     */
    protected function getClassificationCategories()
    {
        if (!$this->_classification_categories) {
            $this->_classification_categories = ClassificationCategory::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_classification_categories;
    }

    /**
     * @return Counterparty[]
     */
    protected function getCounterparties()
    {
        if (!$this->_counterparties) {
            $this->_counterparties = Counterparty::find()
                ->orderBy(['name' => SORT_ASC])
                ->indexBy('id')
                ->all();
        }

        return $this->_counterparties;
    }

    protected function prepareDatesByMask($mask)
    {
        $dates = [];

        $timestamp = $this->timestamp;
        while ($timestamp <= $this->timestamp_to) {
            $date = date($mask, $timestamp);
            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }
            $timestamp += SECONDS_IN_DAY;
        }
        return $dates;
    }

    protected function getChartDateMask()
    {
        $diff = $this->timestamp_to - $this->timestamp;
        if ($diff > 10 * 365 * SECONDS_IN_DAY) {
            return 'Y'; // Year
        } elseif ($diff > 10 * 30 * SECONDS_IN_DAY) {
            return 'M Y'; // month
        } else {
            return 'd M'; // day
        }
    }

}
