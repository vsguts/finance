<?php

namespace app\components\reports\transactions;

use Yii;

class ClassificationCategoryTurnovers extends AbstractTransactionReport
{

    public function getReportName()
    {
        return __('Classification category turnovers (UAH)');
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
        $data['categories'] = [];

        $data['currency_id'] = Yii::$app->currency->getCurrencyId('UAH');
        if (!$data['currency_id']) {
            $data['currency_id'] = Yii::$app->currency->getBaseCurrencyId();
        }

        $classifications = $this->getClassifications();
        $categories = $this->getClassificationCategories();

        foreach ($categories as $category) {
            $data['categories'][$category->id]['category'] = $category;
            $data['categories'][$category->id] += $template;
        }
        $data['categories'][null] = $template;
        $data['categories'][null]['category'] = '';
        foreach ($classifications as $classification) {
            $data['categories'][$classification->category_id]['classifications'][$classification->id]['classification'] = $classification;
            $data['categories'][$classification->category_id]['classifications'][$classification->id] += $template;
        }

        $getSections = function($transaction) use(&$data, $classifications) {
            $classification = $classifications[$transaction->classification_id];
            return [
                & $data,
                & $data['categories'][$classification->category_id],
                & $data['categories'][$classification->category_id]['classifications'][$classification->id],
            ];
        };

        foreach ($this->getTransactions() as $transaction) {
            foreach ($getSections($transaction) as & $section) {
                $section['inflow'] += $transaction->convert($transaction->inflow, $data['currency_id']);
                $section['outflow'] += $transaction->convert($transaction->outflow, $data['currency_id']);
                $section['transactions'] ++;
            }
        }

        // Finish changes: Calculate difference and remove empty items
        $data['difference'] = $data['inflow'] - $data['outflow'];
        foreach ($data['categories'] as $category_key => &$category) {
            if (!floatval($category['transactions'])) {
                unset($data['categories'][$category_key]);
                continue;
            }
            $category['difference'] = $category['inflow'] - $category['outflow'];
            foreach ($category['classifications'] as $classification_key => &$classification) {
                if (!floatval($classification['transactions'])) {
                    unset($data['categories'][$category_key]['classifications'][$classification_key]);
                    continue;
                }
                $classification['difference'] = $classification['inflow'] - $classification['outflow'];
            }
        }

        return $data;
    }

    public function exportGetColumns()
    {
        return [
            'Classification' => 'category.name',
            'Account' => 'classification.name',
            'Original currency' => 'classification.currency.code',
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
        foreach ($data['categories'] as $category) {
            foreach ($category['classifications'] as $classification) {
                $classification['category'] = $category['category'];
                $classification['base_currency_code'] = $base_currency_code;
                $result[] = $classification;
            }
        }
        return $result;
    }

}
