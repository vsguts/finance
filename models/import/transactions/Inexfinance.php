<?php

namespace app\models\import\transactions;

use Yii;
use yii\base\Exception;
use app\models\Account;
use app\models\Classification;
use app\models\Counterparty;
use app\models\Transaction;

class Inexfinance extends AbstractProvider
{
    protected $currencies = [
        1 => 1, // USD
        3 => 4, // RUB
        6 => 3, // UAH
    ];

    protected $accounts = [];

    protected $classifications = [];

    protected $transactionItems = [];

    public function getFormat()
    {
        return 'json';
    }

    public function prepareData($data)
    {
        // Truncate
        Yii::$app->db->createCommand()->truncateTable('transaction')->execute();
        Yii::$app->db->createCommand()->delete('account', "import_processor IS NULL OR import_processor = ''")->execute();
        Yii::$app->db->createCommand()->delete('classification')->execute();
        Yii::$app->db->createCommand()->delete('counterparty')->execute();

        $this->prepareAccounts($data['resources']);
        $this->prepareClassifications($data['categories']);
        $this->prepareItems($data['items']);
        $this->importTransactions($this->hashItems($data['transactions']));
        // $this->importBudgets($data['budgets']); // ??

        return [];
    }

    public function processData($data)
    {
    }

    protected function prepareAccounts($accounts)
    {
        foreach ($accounts as $account) {
            $this->accounts[$account['id']]['inex'] = $account;
        }
    }

    protected function prepareClassifications($classifications)
    {
        foreach ($classifications as $classification) {
            $this->classifications[$classification['id']]['inex'] = $classification;
        }
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
        foreach ($items as $kid => $item) {
            if (in_array($item['mode'], ['standard', 'correction', 'debt'])) {
                $model = new Transaction;
                $model->user_id = 1;
                $model->account_id = $this->getItemAccount($item);
                $model->classification_id = $this->getItemClassification($item);
                $model->counterparty_id = $this->getItemCounterparty($item);
                $model->timestamp = $item['made_on'];
                $model->inflow = $item['income'];
                $model->outflow = $item['expense'];
                $model->description = $this->prepareItemComment($item);
                if (!$model->save()) {
                    pd('Error saving model', $model->errors);
                }
            } elseif ($item['mode'] == 'exchange_to') {
                // skipping
            } elseif ($item['mode'] == 'exchange_from') {
                $item_to = $items[$item['parent_id']];
                
                $from = new Transaction;
                $to = new Transaction;
                $from->user_id = $to->user_id = 1;
                $from->account_id = $this->getItemAccount($item);
                $to->account_id = $this->getItemAccount($item_to);
                $from->classification_id = $to->classification_id = $this->getItemClassification($item, $item_to);
                $from->timestamp = $to->timestamp = $item['made_on'];
                $from->outflow = $item['expense'];
                $to->inflow = $item_to['income'];
                $from->description = $to->description = $this->prepareItemComment($item);
                if (!$from->save()) {
                    pd('Error saving from', $from->errors);
                }
                if (!$to->save()) {
                    pd('Error saving to', $to->errors);
                }
                $from->related_id = $to->id;
                $to->related_id = $from->id;
                if (!$from->save()) {
                    pd('Error saving from', $from->errors);
                }
                if (!$to->save()) {
                    pd('Error saving to', $to->errors);
                }
            } else {
                pd('undefined mode', $item);
            }
        }
        // pd(array_slice($items, 0, 100));
    }

    protected function getItemAccount($item)
    {
        $currency_id = $this->getItemCurrency($item);

        $account = &$this->accounts[$item['resource_id']];
        if (empty($account[$currency_id])) {
            $model = new Account;
            $model->name = $account['inex']['name'];
            $model->status = $account['inex']['enabled'] ? 'active' : 'disabled';
            $model->notes = $this->prepareItemComment($account['inex']);
            $model->currency_id = $currency_id;
            $model->save();
            $account[$currency_id] = $model->id;
        }
        return $account[$currency_id];
    }

    protected function getItemClassification($item, $item_to = null)
    {
        $direction = $this->getItemDirection($item, $item_to);
        $classification = & $this->classifications[$item['category_id']];
        if (empty($classification[$direction])) {
            $data = [
                'type' => $direction,
                'name' => $classification['inex']['name'],
            ];
            $model = Classification::find()->where($data)->one();
            if (!$model) {
                $model = new Classification;
                $model->attributes = $data;
                $model->notes = $this->prepareItemComment($classification['inex']);
                $model->save();
            }
            $classification[$direction] = $model->id;
        }
        return $classification[$direction];
    }

    protected $counterparties = [];

    protected function getItemCounterparty(&$item)
    {
        if (!empty($item['cached_tag_list'])) {
            $tags = explode(',', $item['cached_tag_list']);
            $first_tag = array_shift($tags);
            if (!$this->counterparties) {
                $this->counterparties = Counterparty::find()->indexBy('name')->all();
            }
            if (!empty($this->counterparties[$first_tag])) {
                $counterparty = $this->counterparties[$first_tag];
            } else {
                $counterparty = new Counterparty;
                $counterparty->name = $first_tag;
                $counterparty->save();
                $this->counterparties[$counterparty->name] = $counterparty;
            }
            $item['cached_tag_list'] = implode(',', $tags); // Remove counterparty tag from tags list
            return $counterparty->id;
        }
        return null;
    }

    protected function getItemCurrency($item)
    {
        return $this->currencies[$item['currency_id']];
    }

    protected function getItemDirection($item, $item_to = null)
    {
        // Transfer and conversion
        if ($item_to) {
            return $item['currency_id'] == $item_to['currency_id'] ? 'transfer' : 'conversion';
        }

        // Usual inflow or outflow
        $inflow = floatval($item['income']);
        $outflow = floatval($item['expense']);
        if ($inflow && !$outflow) {
            return 'inflow';
        } elseif ($outflow && !$inflow) {
            return 'outflow';
        } else {
            pd('Double flows', $item);
        }
    }

    protected function prepareItemComment(&$item)
    {
        $parts = [];
        if (!empty($item['comment'])) {
            $parts[] = $item['comment'];
        }
        if (!empty($item['color'])) {
            $parts[] = sprintf("[color] => %s\n[backcolor] => %s", $item['color'], $item['backcolor']);
        }
        if (!empty($item['cached_tag_list'])) {
            $parts[] = sprintf("[tags] => %s", $item['cached_tag_list']);
        }
        return implode("\n", $parts);
    }

    protected function hashItems($items, $field = 'id')
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item[$field]] = $item;
        }
        return $result;
    }

}
