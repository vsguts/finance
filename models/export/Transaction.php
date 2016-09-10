<?php

namespace app\models\export;

use Yii;
use app\models\Transaction as MTransaction;
use app\models\search\TransactionSearch;

class Transaction extends AbstractExport
{

    public function find()
    {
        if ($this->ids) {
            return MTransaction::find()
                ->where(['id' => $this->ids])
                ->orderBy([
                    'timestamp' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
            ;
        } else {
            $search = new TransactionSearch();
            $dataProvider = $search->search($this->queryParams);
            $dataProvider->pagination = false;
            return $dataProvider->query;
        }
    }

    protected function getColumnsDirect()
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
