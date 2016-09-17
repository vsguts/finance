<?php

namespace app\components\reports\transactions;

use Yii;
use yii\base\Object;

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
            'opening_balance' => 0,
            'inflow' => 0,
            'outflow' => 0,
            'closing_balance' => 0,
            'difference' => 0,
        ];

        $data = $template;

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
                $section['opening_balance'] += $transaction->openingBalanceConverted;
                $section['inflow'] += $transaction->inflowConverted;
                $section['outflow'] += $transaction->outflowConverted;
                $section['closing_balance'] += $transaction->balanceConverted;
                $section['transactions'] ++;
            }
        }

        // Finish changes: Calculate difference and remove empty items
        $data['difference'] = $data['closing_balance'] - $data['opening_balance'];
        foreach ($data['classifications'] as $classification_key => &$classification) {
            if (!floatval($classification['transactions'])) {
                unset($data['classifications'][$classification_key]);
                continue;
            }
            $classification['difference'] = $classification['closing_balance'] - $classification['opening_balance'];
            foreach ($classification['accounts'] as $account_key => &$account) {
                if (!floatval($account['transactions'])) {
                    unset($data['classifications'][$classification_key]['accounts'][$account_key]);
                    continue;
                }
                $account['difference'] = $account['closing_balance'] - $account['opening_balance'];
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
            'Opening balance' => 'opening_balance|simpleMoney',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Closing balance' => 'closing_balance|simpleMoney',
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
