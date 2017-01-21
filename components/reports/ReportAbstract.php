<?php

namespace app\components\reports;

use Yii;
use yii\base\Object;

abstract class ReportAbstract extends Object implements ReportInterface
{
    public $position = 10;

    public $timestamp;

    public $timestamp_to;

    abstract public function execute();

    public function exportGetColumns()
    {
    }

    public function exportPrepareData($data)
    {
    }

    protected function getQueryTimestampFrom()
    {
        return $this->timestamp;
    }

    protected function getQueryTimestampTo()
    {
        return $this->timestamp_to + SECONDS_IN_DAY - 1;
    }

    protected function getChartDateMask()
    {
        $diff = $this->timestamp_to - $this->timestamp;
        if ($diff > 10 * 365 * SECONDS_IN_DAY) {
            return 'Y'; // Year
        } elseif ($diff > 10 * 30 * SECONDS_IN_DAY) {
            return 'M Y'; // month
        } else {
            return 'd M'; // day
        }
    }

    protected function prepareDatesByMask($mask)
    {
        $dates = [];

        $timestamp = $this->timestamp;
        while ($timestamp <= $this->timestamp_to) {
            $date = date($mask, $timestamp);
            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }
            $timestamp += SECONDS_IN_DAY;
        }
        return $dates;
    }

    protected function getBaseCurrencyRates()
    {
        $component = Yii::$app->currency;
        
        $base_currency_id = $component->getBaseCurrencyId();

        $rates = [
            'opening' => $component->getPeriodRates($this->timestamp, $this->timestamp),
            'closing' => $component->getPeriodRates($this->timestamp_to, $this->timestamp_to),
        ];
        
        foreach ($rates as $schema => $_rates) {
            foreach ($_rates as $currency_id => $currency_rates) {
                $rates[$schema][$currency_id] = $currency_rates[$base_currency_id];
            }
        }

        return $rates;
    }

}
