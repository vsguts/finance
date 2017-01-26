<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Widget;

class PeriodLinks extends Widget
{
    /**
     * Default: One year ago
     * @var null
     */
    public $timestampFrom = null;

    /**
     * Default: today
     * @var null
     */
    public $timestampTo = null;

    public $timestampFromField = 'timestamp';

    public $timestampToField = 'timestamp_to';

    public $linkOptions = [
        'class' => 'btn btn-link',
    ];

    public $linkActiveClass = 'strong';

    public function init()
    {
        parent::init();
        if (!$this->timestampTo) {
            $this->timestampTo = (new \DateTime)->modify('+1 month')->getTimestamp();
        }
        if (!$this->timestampFrom) {
            $this->timestampFrom = $this->timestampTo - SECONDS_IN_YEAR;
        }
    }

    public function run()
    {
        $path = Yii::$app->request->pathInfo;
        $queryParams = Yii::$app->request->queryParams;

        $currentTimestampFrom = isset($queryParams[$this->timestampFromField]) ? $queryParams[$this->timestampFromField] : null;
        $currentTimestampTo = isset($queryParams[$this->timestampToField]) ? $queryParams[$this->timestampToField] : null;

        $calendar = Yii::$app->calendar;
        $months = $calendar->getPeriodMonths($this->timestampFrom, $this->timestampTo);
        foreach ($months as $month) {
            $timestamps = $calendar->getMonthTimestamps($month['year'], $month['month']);

            $url = Url::to(array_merge([$path], $queryParams, [
                $this->timestampFromField => $timestamps['from'],
                $this->timestampToField => $timestamps['to'],
            ]));

            $text = $month['year'] . ' ' . __(Yii::$app->params['months'][$month['month']]);

            $options = $this->linkOptions;
            if ($timestamps['from'] == $currentTimestampFrom && $timestamps['to'] == $currentTimestampTo) {
                Html::addCssClass($options, $this->linkActiveClass);
            }

            echo Html::a($text, $url, $options);
        }
    }

}
