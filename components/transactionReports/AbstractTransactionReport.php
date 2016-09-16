<?php

namespace app\components\transactionReports;

use Yii;
use yii\base\Object;
use app\models\Account;
use app\models\Transaction;

abstract class AbstractTransactionReport extends Object
{
    public $timestamp;

    public $timestamp_to;

    abstract public function execute();

    public function exportGetColumns()
    {
    }

    public function exportPrepareData($data)
    {
    }

    protected $_accounts;

    protected function getAccounts()
    {
        if (!$this->_accounts) {
            $this->_accounts = Account::find()
                ->permission()
                ->joinWith(['currency'])
                ->orderBy([
                    'name' => SORT_ASC,
                    'currency.code' => SORT_ASC,
                ])
                ->indexBy('id')
                ->all();
        }

        return $this->_accounts;
    }

    protected $_transactions;

    protected function getTransactions()
    {
        if (!$this->_transactions) {
            $this->getAccounts();
            $this->_transactions = Transaction::find()
                ->joinWith('account')
                ->where([
                    'and',
                    ['account_id' => array_keys($this->_accounts)],
                    ['>=', 'timestamp', $this->timestamp],
                    ['<=', 'timestamp', $this->timestamp_to + SECONDS_IN_DAY - 1],
                ])
                ->orderBy(['timestamp' => SORT_ASC, 'id' => SORT_ASC])
                ->all();
        }

        return $this->_transactions;
    }

    protected $_previous_transactions = [];

    protected function getAccountPreviousTransaction($account_id)
    {
        if (!array_key_exists($account_id, $this->_previous_transactions)) {
            $this->_previous_transactions[$account_id] = Transaction::find()
                ->where([
                    'and',
                    ['account_id' => $account_id],
                    ['<', 'timestamp', $this->timestamp],
                ])
                ->orderBy([
                    'timestamp' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
                ->limit(1)
                ->one();
        }

        return $this->_previous_transactions[$account_id];
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

    protected function getBaseCurrencyRates()
    {
        $component = Yii::$app->currency;
        
        $base_currency_id = $component->getBaseCurrencyId();

        $rates = [
            'opening' => $component->getPeriodRates($this->timestamp, $this->timestamp),
            'closing' => $component->getPeriodRates($this->timestamp_to, $this->timestamp_to),
        ];
        
        foreach ($rates as $schema => $_rates) {
            foreach ($_rates as $currency_id => $currency_rates) {
                $rates[$schema][$currency_id] = $currency_rates[$base_currency_id];
            }
        }

        return $rates;
    }

}
