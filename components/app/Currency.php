<?php

namespace app\components\app;

use Yii;
use yii\base\Component;
use yii\base\UserException;
use app\models\Currency as MCurrency;
use app\models\CurrencyRate;

class Currency extends Component
{
    const BASE_CURRENCY = 'USD';

    /**
     * Rates client class name
     * @var string
     */
    public $ratesClass = 'app\components\currencyRates\Currencylayer';

    /**
     * Currencies map
     * [currency_code => currency_id]
     * @var array
     */
    protected $currencyMap = [];
    
    /**
     * Currency models
     * [currency_id => Currency]
     * @var array
     */
    protected $currencies = [];

    /**
     * Rates cache array
     * [period => [from_currency_id => [to_currency_id => rate]]]
     * @var array
     */
    protected $rates = [];

    /**
     * Rates cache
     * @var array
     */
    protected $rates_cache = [];

    protected $rates_object;

    /**
     * Convert value to another currency
     *
     * @param  float $rate           Rate value
     * @param  int   $currency_id    Current currency ID
     * @param  int   $to_currency_id Need currency ID
     * @param  int   $from           Period begin timestamp
     * @param  int   $to             Period end timestamp, if not set $from will be used
     * @return float
     */
    public function convert($value, $currency_id, $to_currency_id, $from, $to = null, $precision = null)
    {
        if ($currency_id == $to_currency_id) {
            return $value;
        }

        if (is_null($to)) {
            $to = $from;
        }

        $rate = $this->getPeriodCurrencyRate($from, $to, $currency_id, $to_currency_id);

        $result = $value * $rate;

        if (!is_null($precision)) {
            $result = round($result, $precision);
        }

        return $result;
    }

    /**
     * Getting period currency rates
     *
     * @param  mixed $from        Timestamp: int or string
     * @param  mixed $to          Timestamp: int or string
     * @param  int   $currency_id Currency ID
     * @return array
     */
    public function getPeriodCurrencyRates($from, $to, $currency_id)
    {
        $rates = $this->getPeriodRates($from, $to);
        return isset($rates[$currency_id]) ? $rates[$currency_id] : [];
    }


    /**
     * Getting period currency rate to other currencya
     *
     * @param  mixed $from           Timestamp: int or string
     * @param  mixed $to             Timestamp: int or string
     * @param  int   $currency_id    Currency ID
     * @param  int   $to_currency_id Currency ID
     * @return float
     */
    public function getPeriodCurrencyRate($from, $to, $currency_id, $to_currency_id)
    {
        if (empty($this->rates_cache[$from][$to][$currency_id][$to_currency_id])) {
            $rates = $this->getPeriodCurrencyRates($from, $to, $currency_id);
            $rate = isset($rates[$to_currency_id]) ? $rates[$to_currency_id] : false;
            $this->rates_cache[$from][$to][$currency_id][$to_currency_id] = $rate;
        }

        return $this->rates_cache[$from][$to][$currency_id][$to_currency_id];
    }

    /**
     * Getting period rates for all available currencies
     *
     * @param  mixed $from Timestamp: int or string
     * @param  mixed $to   Timestamp: int or string
     * @return array
     */
    public function getPeriodRates($from, $to)
    {
        $from = Yii::$app->calendar->dayTimestamp($from);
        $to = Yii::$app->calendar->dayTimestamp($to);
        
        if ($from > time()) {
            $from = Yii::$app->calendar->dayTimestamp(time());
        }
        
        if ($to > time()) {
            $to = Yii::$app->calendar->dayTimestamp(time());
        }

        $key = date('Y-m-d', $from) . '_' . date('Y-m-d', $to);

        if (empty($this->rates[$key])) {
            $currency_rates = [];
            $models = $this->prepareRates($from, $to);
            foreach ($models as $model) {
                $currency_rates[$model->currency_id][] = $model->rate;
            }
            
            // Base currency rates
            $base_currency_id = $this->getCurrencyId(self::BASE_CURRENCY);
            $this->rates[$key][$base_currency_id][$base_currency_id] = 1;
            foreach ($currency_rates as $currency_id => $_rates) {
                $this->rates[$key][$base_currency_id][$currency_id] = array_sum($_rates) / count($_rates);
            }

            $currency_ids = $this->getCurrencyIds(true);
            foreach ($currency_ids as $from_currency_id) {
                $from_rate = $this->rates[$key][$base_currency_id][$from_currency_id];
                $this->rates[$key][$from_currency_id][$base_currency_id] = 1 / $from_rate;
                foreach ($currency_ids as $to_currency_id) {
                    $to_rate = $this->rates[$key][$base_currency_id][$to_currency_id];
                    $this->rates[$key][$from_currency_id][$to_currency_id] = $to_rate / $from_rate;
                }
            }
            
        }

        return $this->rates[$key];
    }

    /**
     * Gets currency ID by currency code
     *
     * @param  str $currency_code Currency code
     * @return int
     */
    public function getCurrencyId($currency_code)
    {
        $map = $this->getCurrencyIdsMap();
        return isset($map[$currency_code]) ? $map[$currency_code] : null;
    }

    public function getBaseCurrencyCode()
    {
        return self::BASE_CURRENCY;
    }

    public function getBaseCurrencyId()
    {
        return $this->getCurrencyId(self::BASE_CURRENCY);
    }

    public function getBaseCurrency()
    {
        return $this->getCurrencies()[$this->getBaseCurrencyId()];
    }

    /**
     * Gets available currencies (just IDs)
     *
     * @param  bool  $exclude_base Flag to exclude base currency. Can be helpful )
     * @return array
     */
    public function getCurrencyIds($exclude_base = false)
    {
        $map = $this->getCurrencyIdsMap();
        
        if ($exclude_base) {
            unset($map[self::BASE_CURRENCY]);
        }
        
        return array_values($map);
    }

    /**
     * Gets map of currencies: Currency code to currency ID
     *
     * @return array
     */
    public function getCurrencyIdsMap()
    {
        if (empty($this->currencyMap)) {
            $currencies = $this->getCurrencies();
            foreach ($currencies as $currency) {
                $this->currencyMap[$currency->code] = $currency->id;
            }
        }
        return $this->currencyMap;
    }

    public function getCurrencies()
    {
        if (empty($this->currencies)) {
            $this->currencies = MCurrency::find()->orderBy(['name' => SORT_ASC])->indexBy('id')->all();
        }
        return $this->currencies;
    }

    protected function prepareRates($from, $to)
    {
        $dates = Yii::$app->calendar->getPeriodDays($from, $to);

        $currencies = $this->getCurrencyIds(true);

        $models = $this->getRateModels($dates, $currencies);

        $need_dates = $dates;
        foreach ($models as $model) {
            $key = $model->year . '-' . $model->month . '-' . $model->day;
            $need_dates[$key]['currency_ids'][] = $model->currency_id;
        }
        foreach ($need_dates as $key => $date_data) {
            if (!empty($date_data['currency_ids']) && count($date_data['currency_ids']) == count($currencies)) {
                unset($need_dates[$key]);
            }
        }

        if ($need_dates) {
            $this->downloadExternalRates($need_dates);
            $models = $this->getRateModels($dates, $currencies);
        }

        return $models;
    }

    protected function getRateModels($dates, $currency_ids = null)
    {
        $query = CurrencyRate::find();
        
        $query->where(array_merge(['or'], $dates));
        
        if ($currency_ids) {
            $query->andWhere(['currency_id' => $currency_ids]);
        }

        return $query->all();
    }

    protected function downloadExternalRates($dates)
    {
        if (!$this->rates_object) {
            $this->rates_object = Yii::createObject([
                'class' => $this->ratesClass,
                'currency' => $this,
            ]);
        }

        $rates = $this->rates_object->get($dates);

        $exists = [];

        foreach ($rates as $rate) {
            $exists[$rate['year'] . '-' . $rate['month'] . '-' . $rate['day']] = 1;
            $model = CurrencyRate::find()->where([
                'currency_id' => $rate['currency_id'],
                'year' => $rate['year'],
                'month' => $rate['month'],
                'day' => $rate['day'],
            ])->one();
            if (!$model) {
                $model = new CurrencyRate;
                $model->currency_id = $rate['currency_id'];
                $model->year = $rate['year'];
                $model->month = $rate['month'];
                $model->day = $rate['day'];
            }
            $model->rate = $rate['rate'];
            $model->save();
        }
        if ($not_found = array_diff_key($dates, $exists)) {
            throw new UserException('Currency rates not found: ' . key($not_found));
        }
    }

}
