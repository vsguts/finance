<?php

namespace app\models\report\transactions;

use Yii;

class AccountTurnoversBaseReport extends AbstractTransactionsReport
{

    public function report($params = [])
    {
        $params = $this->processParams($params);
        $this->load($params);
        $this->validate();

        $data = [
            'currency' => Yii::$app->currency->getBaseCurrency(),
            'rates' => $this->getBaseCurrencyRates(),
            'totals' => [
                'opening_balance' => 0,
                'inflow' => 0,
                'outflow' => 0,
                'forex' => 0,
                'closing_balance' => 0,
                'transactions' => 0,
                'difference' => 0,
            ],
            'accounts' => [],
        ];

        foreach ($this->getAccounts() as $account) {
            $data['accounts'][$account->id] = $data['totals'];
            $data['accounts'][$account->id]['account'] = $account;
        }

        foreach ($this->getTransactionsData() as $transactionDetails) {
            $account = & $data['accounts'][$transactionDetails['account_id']];
            if (empty($account['transactions'])) {
                $account['opening_balance'] =
                    $transactionDetails['opening_balance']
                    * $data['rates']['opening'][$transactionDetails['account.currency_id']]
                ;
            }
            $account['closing_balance'] =
                $transactionDetails['balance']
                * $data['rates']['closing'][$transactionDetails['account.currency_id']]
            ;

            $account['inflow'] += $transactionDetails['inflow_converted'];
            $account['outflow'] += $transactionDetails['outflow_converted'];
            $account['transactions'] ++;
        }
        unset($account);

        foreach ($data['accounts'] as $key => &$account) {
            if (empty($account['transactions'])) {
                $last_transaction = $this->getAccountPreviousTransaction($account['account']->id);
                if ($last_transaction) {
                    $account['opening_balance'] =
                        $last_transaction->balance
                        * $data['rates']['opening'][$last_transaction->account->currency_id]
                    ;
                    $account['closing_balance'] =
                        $last_transaction->balance
                        * $data['rates']['closing'][$last_transaction->account->currency_id]
                    ;
                }
            }
            if ($account['account']->currency_id != $data['currency']->id) {
                $account['forex'] =
                    $account['closing_balance']
                    - $account['opening_balance']
                    - $account['inflow']
                    + $account['outflow']
                ;
            }

            // Remove empty
            if (!$account['transactions'] && !floatval($account['closing_balance'])) {
                unset($data['accounts'][$key]);
                continue;
            }

            $account['difference'] = $account['closing_balance'] - $account['opening_balance'];

            foreach (array_keys($data['totals']) as $key) {
                $data['totals'][$key] += $account[$key];
            }
        }

        return $data;
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
