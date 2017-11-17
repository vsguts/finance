<?php

namespace app\models\report\transactions;

class ClassificationTurnoversReport extends AbstractTransactionsReport
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
        $data['classifications'] = [];

        foreach ($this->getClassifications() as $classification) {
            $data['classifications'][$classification->id]['classification'] = $classification;
            $data['classifications'][$classification->id] += $template;
            foreach ($this->getAccounts() as $account) {
                $data['classifications'][$classification->id]['accounts'][$account->id]['account'] = $account;
                $data['classifications'][$classification->id]['accounts'][$account->id] += $template;
            }
        }

        $getSections = function($transaction) use(&$data) {
            return [
                & $data,
                & $data['classifications'][$transaction['classification_id']],
                & $data['classifications'][$transaction['classification_id']]['accounts'][$transaction['account_id']],
            ];
        };

        foreach ($this->getTransactionsData() as $transaction) {
            foreach ($getSections($transaction) as & $section) {
                $section['inflow'] += $transaction['inflow_converted'];
                $section['outflow'] += $transaction['outflow_converted'];
                $section['transactions'] ++;
            }
        }

        // Finish changes: Calculate difference and remove empty items
        $data['difference'] = $data['inflow'] - $data['outflow'];
        foreach ($data['classifications'] as $classification_key => &$classification) {
            if (!floatval($classification['transactions'])) {
                unset($data['classifications'][$classification_key]);
                continue;
            }
            $classification['difference'] = $classification['inflow'] - $classification['outflow'];
            foreach ($classification['accounts'] as $account_key => &$account) {
                if (!floatval($account['transactions'])) {
                    unset($data['classifications'][$classification_key]['accounts'][$account_key]);
                    continue;
                }
                $account['difference'] = $account['inflow'] - $account['outflow'];
            }
        }

        return $data;
    }

}
