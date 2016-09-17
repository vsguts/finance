<?php

namespace app\components\reports\transactions;

use Yii;
use yii\base\Object;

class BalanceDynamics extends AbstractTransactionReport
{

    public function getReportName()
    {
        return __('Balance dynamics');
    }

    public function execute()
    {
        $open = [];

        // Prepare opening balance data
        foreach ($this->getAccounts() as $account) {
            $open[$account->id] = 0;
            if ($last_transaction = $this->getAccountPreviousTransaction($account->id)) {
                $open[$account->id] = $last_transaction->balanceConverted;
            }
        }

        $mask = $this->getChartDateMask();

        // Preparing date by transactions
        $data = [];
        $previous_date = date($mask, $this->timestamp);
        $data[$previous_date] = $open;
        foreach ($this->getTransactions() as $transaction) {
            $date = date($mask, $transaction->timestamp);
            if ($date != $previous_date) {
                $data[$date] = $data[$previous_date];
                $previous_date = $date;
            }
            $data[$date][$transaction->account_id] = $transaction->balanceConverted;
        }

        // Prepare totals
        $totals = [];
        $previous_date = date($mask, $this->timestamp);
        foreach ($this->prepareDatesByMask($mask) as $date) {
            if (isset($data[$date])) {
                $totals[$date] = round(array_sum($data[$date]));
            } else {
                $totals[$date] = $totals[$previous_date];
            }
            $previous_date = $date;
        }
        
        // Prepare chart
        $chart = [
            [__('Date'), __('Balance')]
        ];
        foreach ($totals as $key => $balance) {
            $chart[] = [$key, $balance];
        }

        return [
            'data' => $data,
            'chart' => $chart,
            'currency' => Yii::$app->currency->getBaseCurrency(),
        ];
    }

}
