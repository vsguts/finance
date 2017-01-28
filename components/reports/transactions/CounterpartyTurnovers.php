<?php

namespace app\components\reports\transactions;

use Yii;

class CounterpartyTurnovers extends AbstractTransactionReport
{
    public $position = 50;

    public function getReportName()
    {
        return __('Counterparty turnovers');
    }

    public function execute()
    {
        $template = [
            'transactions' => 0,
            'inflow' => 0,
            'outflow' => 0,
            'difference' => 0,
        ];

        $data = $template;
        $data['counterparties'] = [];

        foreach ($this->getCounterparties() as $counterparty) {
            $data['counterparties'][$counterparty->id]['counterparty'] = $counterparty;
            $data['counterparties'][$counterparty->id] += $template;
            foreach ($this->getAccounts() as $account) {
                $data['counterparties'][$counterparty->id]['accounts'][$account->id]['account'] = $account;
                $data['counterparties'][$counterparty->id]['accounts'][$account->id] += $template;
            }
        }

        $getSections = function($transaction) use(&$data) {
            return [
                & $data,
                & $data['counterparties'][$transaction->counterparty_id],
                & $data['counterparties'][$transaction->counterparty_id]['accounts'][$transaction->account_id],
            ];
        };

        foreach ($this->getTransactions() as $transaction) {
            if (!$transaction->counterparty_id) {
                continue;
            }
            foreach ($getSections($transaction) as & $section) {
                $section['inflow'] += $transaction->inflowConverted;
                $section['outflow'] += $transaction->outflowConverted;
                $section['transactions'] ++;
            }
        }

        // Finish changes: Calculate difference and remove empty items
        $data['difference'] = $data['inflow'] - $data['outflow'];
        foreach ($data['counterparties'] as $counterparty_key => &$counterparty) {
            if (!floatval($counterparty['transactions'])) {
                unset($data['counterparties'][$counterparty_key]);
                continue;
            }
            $counterparty['difference'] = $counterparty['inflow'] - $counterparty['outflow'];
            foreach ($counterparty['accounts'] as $account_key => &$account) {
                if (!floatval($account['transactions'])) {
                    unset($data['counterparties'][$counterparty_key]['accounts'][$account_key]);
                    continue;
                }
                $account['difference'] = $account['inflow'] - $account['outflow'];
            }
        }

        return $data;
    }

    public function exportGetColumns()
    {
        return [
            'Counterparty' => 'counterparty.name',
            'Account' => 'account.name',
            'Original currency' => 'account.currency.code',
            'Base currency' => 'base_currency_code',
            'Transactions' => 'transactions',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Difference' => 'difference|simpleMoney',
        ];
    }

    public function exportPrepareData($data)
    {
        $result = [];
        $base_currency_code = Yii::$app->currency->getBaseCurrencyCode();
        foreach ($data['counterparties'] as $counterparty) {
            foreach ($counterparty['accounts'] as $account) {
                $account['counterparty'] = $counterparty['counterparty'];
                $account['base_currency_code'] = $base_currency_code;
                $result[] = $account;
            }
        }
        return $result;
    }

}
