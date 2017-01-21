<?php

namespace app\components\reports\transactions;

class AccountTurnovers extends AbstractTransactionReport
{

    public function getReportName()
    {
        return __('Account turnovers');
    }

    public function execute()
    {
        $data = [
            'accounts' => [],
        ];

        foreach ($this->getAccounts() as $account) {
            $data['accounts'][$account->id] = [
                'account' => $account,
                'opening_balance' => 0,
                'inflow' => 0,
                'outflow' => 0,
                'closing_balance' => 0,
                'transactions' => 0,
                'difference' => 0,
            ];
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
            if (!$account['transactions'] && !floatval($account['closing_balance'])) {
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
