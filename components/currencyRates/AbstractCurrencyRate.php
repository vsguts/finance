<?php

namespace app\components\currencyRates;

use Yii;
use yii\base\Component;

abstract class AbstractCurrencyRate extends Component
{
    public $currency;

    abstract protected function get($dates);

    protected function getCodesMap()
    {
        $codes_map = $this->currency->getCurrencyIdsMap();
        unset($codes_map[$this->currency->getBaseCurrencyCode()]);

        return $codes_map;
    }
}
