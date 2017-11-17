<?php

namespace app\models\export\reports;

use app\models\export\ExportFormAbstract;
use Yii;

/**
 * @see CounterpartyTurnoversReport
 */
class CounterpartyTurnoversReportExport extends ExportFormAbstract
{

    public function getColumnsSchema()
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

    protected function prepareData($data)
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
