<?php

namespace app\components\reports\transactions;

use Yii;

class ClassificationTurnovers extends AbstractTransactionReport
{

    public function getReportName()
    {
        return __('Classification turnovers');
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
                & $data['classifications'][$transaction->classification_id],
                & $data['classifications'][$transaction->classification_id]['accounts'][$transaction->account_id],
            ];
        };

        foreach ($this->getTransactions() as $transaction) {
            foreach ($getSections($transaction) as & $section) {
                $section['inflow'] += $transaction->inflowConverted;
                $section['outflow'] += $transaction->outflowConverted;
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

    public function exportGetColumns()
    {
        return [
            'Classification' => 'classification.name',
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
        foreach ($data['classifications'] as $classification) {
            foreach ($classification['accounts'] as $account) {
                $account['classification'] = $classification['classification'];
                $account['base_currency_code'] = $base_currency_code;
                $result[] = $account;
            }
        }
        return $result;
    }

}
