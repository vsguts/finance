<?php

namespace app\models\import\transactions;

use Yii;
use app\models\BankTransaction;

class Inexfinance extends AbstractProvider
{
    protected $currenciesMap = [
        1 => 1, // USD
        3 => 4, // RUB
        6 => 3, // UAH
    ];

    protected $transactionItems = [];

    public function getFormat()
    {
        return 'json';
    }

    public function prepareData($data)
    {
        $this->importAccounts($data['resources']);
        // $this->importClassifications($data['categories']);
        $this->prepareItems($data['items']);
        $this->importTransactions($data['transactions']);
        // $this->importBudgets($data['budgets']); // ??
    }

    public function processData($data)
    {
    }

    protected function importAccounts($items)
    {
        pd($items);
    }

    protected function importClassifications($items)
    {
        pd($items);
    }

    protected function prepareItems($items)
    {
        foreach ($items as $item) {
            $this->transactionItems[$item['itemable_id']]['items'][] = $item;
        }
        foreach ($this->transactionItems as $transaction_id => $itemable_data) {
            $value = 0;
            $description = [];
            foreach ($itemable_data['items'] as $item) {
                $_value = $item['income'] - $item['expense'];
                $value += $_value;
                $description[] = $item['name'].': '.$_value;
            }
            $this->transactionItems[$transaction_id]['value'] = $value;
            $this->transactionItems[$transaction_id]['description'] = 'Items:'.PHP_EOL.implode(PHP_EOL, $description);
            unset($this->transactionItems[$transaction_id]['items']);
        }
    }

    protected function importTransactions($items)
    {
        pd(array_slice($items, 0, 100), $this->transactionItems);
        // $transaction =
    }

}
