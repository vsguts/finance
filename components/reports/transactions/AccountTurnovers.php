<?php

namespace app\components\reports\transactions;

use Yii;

class AccountTurnovers extends AbstractTransactionReport
{

    public $position = 10;

    public function getReportName()
    {
        return __('Account turnovers');
    }

    public function execute()
    {
        $fields = ['opening_balance', 'inflow', 'outflow', 'closing_balance', 'transactions', 'difference'];
        $template = array_fill_keys($fields, 0);
        $currenciesTemplate = array_fill_keys(Yii::$app->currency->getCurrencyIds(), 0);

        $data = [
            'accounts' => [],
            'totals' => array_fill_keys($fields, $currenciesTemplate),
        ];

        foreach ($this->getAccounts() as $account) {
            $data['accounts'][$account->id] = $template;
            $data['accounts'][$account->id]['account'] = $account;
        }

        foreach ($this->getTransactions() as $transaction) {
            $account = & $data['accounts'][$transaction['account_id']];
            if (empty($account['transactions'])) {
                $account['opening_balance'] = $transaction->opening_balance;
            }
            $account['closing_balance'] = $transaction->balance;
            $account['inflow'] += $transaction->inflow;
            $account['outflow'] += $transaction->outflow;
            $account['transactions'] ++;
        }
        unset($account);

        foreach ($data['accounts'] as $key => &$account) {
            if (empty($account['transactions'])) {
                $last_transaction = $this->getAccountPreviousTransaction($account['account']->id);
                if ($last_transaction) {
                    $account['opening_balance'] = $last_transaction->balance;
                    $account['closing_balance'] = $last_transaction->balance;
                }
            }
            $account['difference'] = $account['closing_balance'] - $account['opening_balance'];
            // Remove empty
            if ($account['transactions'] || floatval($account['closing_balance'])) {
                foreach ($fields as $field) {
                    $data['totals'][$field][$account['account']->currency_id] += $account[$field];
                }
            } else {
                unset($data['accounts'][$key]);
            }
        }

        return $data;
    }

    public function exportGetColumns()
    {
        return [
            'Name' => 'account.name',
            'Currency' => 'account.currency.code',
            'Transactions' => 'transactions',
            'Opening balance' => 'opening_balance|simpleMoney',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Closing balance' => 'closing_balance|simpleMoney',
            'Difference' => 'difference|simpleMoney',
        ];
    }

    public function exportPrepareData($data)
    {
        return $data['accounts'];
    }

}
