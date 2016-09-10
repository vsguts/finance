<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class CurrencyRates extends Widget
{
    public $rates;

    public $rate_models;
    
    public $currencies;
    
    public function init()
    {
        parent::init();
        if ($this->rate_models) {
            $this->convertModelsToRates();
        }
        $this->currencies = Yii::$app->currency->getCurrencies();
    }

    public function run()
    {
        echo Html::beginTag('table', ['class' => 'table currency-rates']);
        
        // Header
        echo Html::beginTag('thead');
        $cols = [
            Html::tag('th', __('Currencies'))
        ];
        foreach ($this->currencies as $currency) {
            if (isset($this->rates[$currency->id])) {
                $cols[] = Html::tag('th', $currency->code);
            }
        }
        echo Html::tag('tr', implode(' ', $cols));
        echo Html::endTag('thead');

        echo Html::beginTag('tbody');
        foreach ($this->currencies as $currency_from) {
            if (isset($this->rates[$currency_from->id])) {
                $cols = [
                    Html::tag('td', $currency_from->code, ['class' => 'strong']),
                ];
                foreach ($this->currencies as $currency_to) {
                    if (isset($this->rates[$currency_to->id])) {
                        $rate = $this->rates[$currency_from->id][$currency_to->id];
                        $cols[] = Html::tag('td', floatval($rate));
                    }
                }
                echo Html::tag('tr', implode(' ', $cols));
            }
        }
        echo Html::endTag('tbody');

        echo Html::endTag('table');
    }

    protected function convertModelsToRates()
    {
        $rates = [];
        foreach ($this->rate_models as $model) {
            $rates[$model->from_currency_id][$model->to_currency_id] = $model->rate;
        }
        $this->rates = $rates;
    }
}
