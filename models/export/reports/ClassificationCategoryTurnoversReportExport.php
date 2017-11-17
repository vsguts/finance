<?php

namespace app\models\export\reports;

use app\models\export\ExportFormAbstract;
use Yii;

/**
 * @see ClassificationCategoryTurnoversReport
 */
class ClassificationCategoryTurnoversReportExport extends ExportFormAbstract
{

    public function getColumnsSchema()
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

    protected function prepareData($data)
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
