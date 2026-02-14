<?php

namespace app\components\currencyRates;

class FawazCurrencyApi extends AbstractCurrencyRate
{
    /**
     * jsDelivr base URL (versioned by date, e.g. @2026.2.14)
     * Docs/source: @fawazahmed0/currency-api
     */
    public $link = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@';

    /**
     * Base currency in the remote JSON.
     * Your old provider returned quotes like USDEUR, USDRUB, etc., so we keep USD as base.
     */
    public $base = 'usd';

    public function get($dates)
    {
        $codesMap = $this->getCodesMap();
        $codes = array_keys($codesMap);

        $result = [];

        foreach ($dates as $date) {
            // Version format used by the package: YYYY.M.D (no leading zeros)
            $version = (int)$date['year'] . '.' . (int)$date['month'] . '.' . (int)$date['day'];

            // Endpoint: ...@YYYY.M.D/v1/currencies/usd.json
            $url = $this->link . $version . '/v1/currencies/' . $this->base . '.json';

            // Basic HTTP fetch (you can replace with Yii HTTP client if you prefer)
            $response = file_get_contents($url);

            $data = json_decode($response, true);

            // Expected JSON shape:
            // { "date": "YYYY-MM-DD", "usd": { "eur": ..., "uah": ..., "rub": ... } }
            $rates = $data[$this->base];

            foreach ($codes as $code) {
                $key = strtolower($code);

                if (!isset($rates[$key])) {
                    continue;
                }

                $result[] = [
                    'year' => $date['year'],
                    'month' => $date['month'],
                    'day' => $date['day'],
                    'currency_id' => $codesMap[$code],
                    'rate' => (float)$rates[$key], // 1 USD -> {currency}
                ];
            }
        }

        return $result;
    }
}
