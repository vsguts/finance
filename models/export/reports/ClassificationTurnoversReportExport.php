<?php

namespace app\models\export\reports;

use app\models\report\transactions\AccountTurnoversReport;
use app\models\export\ExportFormAbstract;
use Yii;

/**
 * @see AccountTurnoversReport
 */
class ClassificationTurnoversReportExport extends ExportFormAbstract
{

    public function getColumnsSchema()
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

    protected function prepareData($data)
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
