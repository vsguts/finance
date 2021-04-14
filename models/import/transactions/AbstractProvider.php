<?php

namespace app\models\import\transactions;

use Yii;
use yii\base\BaseObject;

abstract class AbstractProvider extends BaseObject
{
    public $account;

    abstract public function processData($data);

    public function getFormat()
    {
        return 'csv';
    }

    public function getEncoding()
    {
        return 'UTF-8';
    }

    public function getDelimiter()
    {
        return ',';
    }

    public function getHasCols()
    {
        return true;
    }

    public function prepareData($data)
    {
        return $data;
    }

    protected $timestamps = [];

    /**
     * Common methods: Parse date to timestamp
     * @param  str $date Date and time
     * @return int Timestamp
     */
    protected function parseDate($date)
    {
        $timestamp = Yii::$app->formatter->asTimestamp($date);

        // Generate unique timestamp
        if (isset($this->timestamps[$timestamp])) {
            $this->timestamps[$timestamp] ++;
            $timestamp += $this->timestamps[$timestamp];
        } else {
            $this->timestamps[$timestamp] = 0;
        }

        return $timestamp;
    }

    /**
     * Common methods: Parse price
     * @param  str $price Dirty price value
     * @return str Price value
     */
    protected function parsePrice($price)
    {
        $price = strtr($price, [
            ',' => '',
            ' ' => '',
            chr(194) . chr(160) => '', // Non-breaking space workaround
            '$' => '',
            '(' => '', // norvic
            ')' => '', // norvic
        ]);

        return floatval(trim($price));
    }

    /**
     * Generate uniqid string for data using particular fields.
     *
     * @param  array $data  Data
     * @param  array $fields Fields array
     * @return string
     */
    protected function getUniqid($data, $fields)
    {
        $uniq_data = array_intersect_key($data, array_flip($fields));
        ksort($uniq_data);
        return md5(json_encode($uniq_data));
    }

}
