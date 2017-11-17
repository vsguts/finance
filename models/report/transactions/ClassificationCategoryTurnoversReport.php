<?php

namespace app\models\report\transactions;

use Yii;

class ClassificationCategoryTurnoversReport extends AbstractTransactionsReport
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
        $data['categories'] = [];

        $data['currency_id'] = Yii::$app->currency->getCurrencyId('UAH');
        if (!$data['currency_id']) {
            $data['currency_id'] = Yii::$app->currency->getBaseCurrencyId();
        }

        $classifications = $this->getClassifications();

        foreach ($this->getClassificationCategories() as $category) {
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

}
