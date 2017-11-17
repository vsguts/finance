<?php

namespace app\models\export;

class TransactionExport extends ExportFormAbstract
{
    public function getColumnsSchema()
    {
        return [
            'ID' => 'id',
            'Time' => 'timestamp|date',
            'Account' => 'account.fullName',
            'Classification' => 'classification.name',
            'Counterparty' => 'counterparty.name',
            'Currency' => 'account.currency.code',
            'Opening balance' => 'opening_balance',
            'Inflow' => 'inflow',
            'Outflow' => 'outflow',
            'Closing balance' => 'balance',
            'Opening balance USD' => 'openingBalanceConverted|simpleMoney',
            'Inflow USD' => 'inflowConverted|simpleMoney',
            'Outflow USD' => 'outflowConverted|simpleMoney',
            'Closing balance USD' => 'balanceConverted|simpleMoney',
            'Description' => 'description',
            'User' => 'user.name',
            'Created at' => 'created_at|datetime',
        ];
    }

}
