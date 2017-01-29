<?php

namespace app\models\import\transactions;

use app\models\Classification;
use Yii;
use app\models\Transaction;
use yii\base\Exception;

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
        $transaction = new Transaction;
        $transaction->account_id = $this->account->id;

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

        $transaction->classification_id = $this->getClassification($transaction, $data);

        return $transaction->save();
    }

    protected function getClassification($transaction, $data)
    {
        $classification = null;
        $type = floatval($transaction->inflow) ? 'inflow' : 'outflow';

        if (!empty($data['Classification'])) {
            $classification = Classification::find()->where([
                'name' => $data['Classification'],
                'type' => $type,
            ])->one();
        }
        if (!$classification) {
            $classification = Classification::find()->sorted()->where(['type' => $type])->limit(1)->one();
        }
        if (!$classification) {
            throw new Exception('Classification not found: ' . $type);
        }
        return $classification->id;
    }

}
