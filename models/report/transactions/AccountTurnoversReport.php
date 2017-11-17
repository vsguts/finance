<?php

namespace app\models\report\transactions;

use Yii;

class AccountTurnoversReport extends AbstractTransactionsReport
{

    public function report($params = [])
    {
        $params = $this->processParams($params);
        $this->load($params);
        $this->validate();

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

        foreach ($this->getTransactionsData() as $transactionDetails) {
            $account = & $data['accounts'][$transactionDetails['account_id']];
            if (empty($account['transactions'])) {
                $account['opening_balance'] = $transactionDetails['opening_balance'];
            }
            $account['closing_balance'] = $transactionDetails['balance'];
            $account['inflow'] += $transactionDetails['inflow'];
            $account['outflow'] += $transactionDetails['outflow'];
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

}
