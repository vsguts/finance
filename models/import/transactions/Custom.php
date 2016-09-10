<?php

namespace app\models\import\transactions;

use Yii;
use app\models\BankTransaction;

/**
 * Custom import schema
 *
 * Columns: Date, Value, Balance, Reference, Description
 */
class Custom extends AbstractProvider
{
    
    public function getDelimiter()
    {
        return ',';
    }

    public function getHasCols()
    {
        return true;
    }

    public function processData($data)
    {
        $uniqid = $this->getUniqid($data, ['Date', 'Value', 'Balance', 'Reference', 'Description']);

        $transaction = BankTransaction::find()->where([
            'account_id' => $this->account->id,
            'uniqid' => $uniqid,
        ])->one();
        
        if (!$transaction) {
            $transaction = new BankTransaction;
            $transaction->account_id = $this->account->id;
            $transaction->uniqid = $uniqid;
        }

        if (!empty($data['Reference'])) {
            $transaction->reference = $data['Reference'];
        }

        $transaction->timestamp = $this->parseDate($data['Date']);
        $transaction->description = $data['Description'];

        if (!empty($data['Balance'])) {
            $transaction->balance = $this->parsePrice($data['Balance']);
        }

        $gross_value = $this->parsePrice($data['Value']);

        if ($gross_value > 0) {
            $transaction->inflow = $gross_value;
        } else {
            $transaction->outflow = abs($gross_value);
        }

        return $transaction->save();
    }

}
