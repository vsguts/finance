<?php

namespace app\components\app;

use Yii;

class Formatter extends \yii\i18n\Formatter
{
    const MONEY_DECIMALS = 2;

    public function asRoundInteger($value)
    {
        return $this->asInteger(round($value));
    }

    public function asMoney($value, $decimals = self::MONEY_DECIMALS)
    {
        $value = round($value, $decimals);
        return $this->asDecimal($value, $decimals);
    }

    public function asSimpleMoney($value, $decimals = self::MONEY_DECIMALS)
    {
        return round($value, $decimals);
    }

    /**
     * Format number as decimal with currency symbol
     * @param            $value
     * @param int|string $currency_id Currency ID or direct Symbol
     * @param int        $decimals
     * @param string     $delimiter
     * @return string
     */
    public function asMoneyWithSymbol($value, $currency_id = null, $decimals = self::MONEY_DECIMALS, $delimiter = ' ')
    {
        $result = $this->asMoney($value, $decimals);

        $symbol = '';
        if ($currency_id) {
            $symbol = $currency_id;
            if (
                is_numeric($currency_id)
                && $currency = Yii::$app->currency->getCurrency($currency_id)
            ) {
                $symbol = $currency->symbol;
            }
        } else {
            if ($currency = Yii::$app->currency->getBaseCurrency($currency_id)) {
                $symbol = $currency->symbol;
            }
        }

        if ($symbol) {
            $result .= $delimiter . $symbol;
        }

        return $result;
    }

    public function asDateFiltered($value, $format = null)
    {
        return $value ? parent::asDate($value, $format) : null;
    }

    public function asDatetimeFiltered($value, $format = null)
    {
        return $value ? parent::asDatetime($value, $format) : null;
    }

    public function asBoolean($value)
    {
        return $value ? __('Yes') : __('No');
    }

    public function asTagStripped($value)
    {
        return strip_tags($value);
    }

}
