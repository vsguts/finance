<?php

namespace app\models\export\reports;

use app\models\report\transactions\AccountTurnoversReport;
use app\models\export\ExportFormAbstract;

/**
 * @see AccountTurnoversReport
 */
class AccountTurnoversReportExport extends ExportFormAbstract
{

    public function getColumnsSchema()
    {
        return [
            'Account ID' => 'account.id',
            'Name' => 'account.name',
            'Currency' => 'account.currency.code',
            'Transactions' => 'transactions',
            'Opening balance' => 'opening_balance|simpleMoney',
            'Inflow' => 'inflow|simpleMoney',
            'Outflow' => 'outflow|simpleMoney',
            'Closing balance' => 'closing_balance|simpleMoney',
            'Difference' => 'difference|simpleMoney',
        ];
    }

    protected function prepareData($data)
    {
        return $data['accounts'];
    }

}
