<?php

namespace app\components\currencyRates;

use Yii;
use yii\base\Component;

abstract class AbstractCurrencyRate extends Component
{
    public $currency;

    abstract protected function get($dates);
}
