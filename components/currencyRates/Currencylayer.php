<?php

namespace app\components\currencyRates;

use Yii;
use yii\base\Component;

class Currencylayer extends AbstractCurrencyRate
{
    public $link = 'http://apilayer.net/api/historical';

    public $access_key = 'c546d942aa165db78a72061290a4b97c';

    public function get($dates)
    {
        $codes_map = $this->currency->getCurrencyIdsMap();
        unset($codes_map[$this->currency->getBaseCurrencyCode()]);
        $codes = implode(',', array_keys($codes_map));

        $result = [];

        foreach ($dates as $date) {
            $_date = date('Y-m-d', mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));
            $link = $this->link . '?' . http_build_query([
                'access_key' => $this->access_key,
                'date' => $_date,
                'currencies' => $codes,
            ]);
            $response = file_get_contents($link);
            $data = json_decode($response, true);
            if (!empty($data['quotes'])) {
                foreach ($data['quotes'] as $code => $rate) {
                    $code = substr($code, 3);
                    if ($currency_id = $this->currency->getCurrencyId($code)) {
                        $result[] = [
                            'year' => $date['year'],
                            'month' => $date['month'],
                            'day' => $date['day'],
                            'currency_id' => $currency_id,
                            'rate' => $rate,
                        ];
                    }
                }
            }
            
        }
        
        return $result;
    }

}
