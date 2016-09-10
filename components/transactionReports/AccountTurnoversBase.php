<?php

namespace app\components\transactionReports;

use Yii;
use yii\base\Object;

class AccountTurnoversBase extends AbstractTransactionReport
{
    public $report_name = 'Account turnovers in USD';

    public function execute()
    {
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
        ];

        foreach ($this->getAccounts() as $account) {
            $data['accounts'][$account->id] = $data['totals'];
            $data['accounts'][$account->id]['account'] = $account;
        }

        foreach ($this->getTransactions() as $transaction) {
            $account = & $data['accounts'][$transaction['account_id']];
            if (empty($account['transactions'])) {
                $account['opening_balance'] =
                    $transaction->opening_balance
                    * $data['rates']['opening'][$transaction->account->currency_id]
                ;
            }
            $account['closing_balance'] =
                $transaction->balance
                * $data['rates']['closing'][$transaction->account->currency_id]
            ;

            $account['inflow'] += $transaction->inflowConverted;
            $account['outflow'] += $transaction->outflowConverted;
            $account['transactions'] ++;
        }
        unset($account);

        foreach ($data['accounts'] as &$account) {
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
            $account['difference'] = $account['closing_balance'] - $account['opening_balance'];

            foreach (array_keys($data['totals']) as $key) {
                $data['totals'][$key] += $account[$key];
            }
        }

        return $data;
    }

    public function exportGetColumns()
    {
        return [
            'Name' => 'account.name',
            'Bank' => 'account.bank',
            'Account number' => 'account.account_number',
            'Currency' => 'account.currency.code',
            'Base currency' => 'currency_code',
            'Opening exchange rate' => 'rate_open',
            'Closing exchange rate' => 'rate_close',
            'Transactions' => 'transactions',
            'Opening balance' => 'opening_balance|simpleMoney',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Realized Forex' => 'forex|simpleMoney',
            'Closing balance' => 'closing_balance|simpleMoney',
            'Difference' => 'difference|simpleMoney',
        ];
    }

    public function exportPrepareData($data)
    {
        foreach ($data['accounts'] as &$account) {
            $account['currency_code'] = $data['currency']->code;
            $account['rate_open'] = 1;
            $account['rate_close'] = 1;
            if ($data['currency']->id != $account['account']->currency->id) {
                $account['rate_open'] = round($data['rates']['opening'][$account['account']->currency->id], 8);
                $account['rate_close'] = round($data['rates']['closing'][$account['account']->currency->id], 8);
            }
        }
        return $data['accounts'];
    }

}
