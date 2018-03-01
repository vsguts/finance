<?php

namespace app\models\export\reports;

use app\models\report\transactions\AccountTurnoversBaseReport;
use app\models\export\ExportFormAbstract;

/**
 * @see AccountTurnoversBaseReport
 */
class AccountTurnoversBaseReportExport extends ExportFormAbstract
{

    public function getColumnsSchema()
    {
        return [
            'Account ID' => 'account.id',
            'Name' => 'account.name',
            'Currency' => 'account.currency.code',
            'Base currency' => 'currency_code',
            'Opening exchange rate' => 'rate_open',
            'Closing exchange rate' => 'rate_close',
            'Transactions' => 'transactions',
            'Opening balance' => 'opening_balance|simpleMoney',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Realized Forex' => 'forex|simpleMoney',
            'Closing balance' => 'closing_balance|simpleMoney',
            'Difference' => 'difference|simpleMoney',
        ];
    }

    protected function prepareData($data)
    {
        foreach ($data['accounts'] as &$account) {
            $account['currency_code'] = $data['currency']->code;
            $account['rate_open'] = 1;
            $account['rate_close'] = 1;
            if ($data['currency']->id != $account['account']->currency->id) {
                $account['rate_open'] = round($data['rates']['opening'][$account['account']->currency->id], 8);
                $account['rate_close'] = round($data['rates']['closing'][$account['account']->currency->id], 8);
            }
        }
        return $data['accounts'];
    }

}
