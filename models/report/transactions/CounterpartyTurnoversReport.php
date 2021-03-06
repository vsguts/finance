<?php

namespace app\models\report\transactions;

class CounterpartyTurnoversReport extends AbstractTransactionsReport
{

    public function report($params = [])
    {
        $params = $this->processParams($params);
        $this->load($params);
        $this->validate();

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
                & $data['counterparties'][$transaction['counterparty_id']],
                & $data['counterparties'][$transaction['counterparty_id']]['accounts'][$transaction['account_id']],
            ];
        };

        foreach ($this->getTransactionsData() as $transaction) {
            if (!$transaction['counterparty_id']) {
                continue;
            }
            foreach ($getSections($transaction) as & $section) {
                $section['inflow'] += $transaction['inflow_converted'];
                $section['outflow'] += $transaction['outflow_converted'];
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

}
