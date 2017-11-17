<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class PeriodLinks extends AbstractLinkWidget
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
        $queryParams = $this->prepareQuery($this->timestampFromField, $this->timestampToField);

        $currentTimestampFrom = $queryParams[$this->timestampFromField] ?? null;
        $currentTimestampTo = $queryParams[$this->timestampToField] ?? null;

        if ($this->formName && isset($queryParams[$this->formName])) {
            if (isset($queryParams[$this->formName][$this->timestampFromField])) {
                $currentTimestampFrom = $queryParams[$this->formName][$this->timestampFromField];
            }
            if (isset($queryParams[$this->formName][$this->timestampToField])) {
                $currentTimestampTo = $queryParams[$this->formName][$this->timestampToField];
            }
            unset($queryParams[$this->formName]);
        }

        if (!is_numeric($currentTimestampFrom)) {
            $currentTimestampFrom = Yii::$app->formatter->asTimestamp($currentTimestampFrom);
        }

        if (!is_numeric($currentTimestampTo)) {
            $currentTimestampTo = Yii::$app->formatter->asTimestamp($currentTimestampTo);
        }

        $calendar = Yii::$app->calendar;
        $months = $calendar->getPeriodMonths($this->timestampFrom, $this->timestampTo);
        $monthsList = Yii::$app->calendar->getMonthsList();
        foreach ($months as $month) {
            $timestamps = $calendar->getMonthTimestamps($month['year'], $month['month']);

            $url = Url::to(array_merge([$this->path], $queryParams, [
                $this->timestampFromField => $timestamps['from'],
                $this->timestampToField => $timestamps['to'],
            ]));

            $text = $month['year'] . ' ' . $monthsList[$month['month']];

            $options = $this->linkOptions;
            if ($timestamps['from'] == $currentTimestampFrom && $timestamps['to'] == $currentTimestampTo) {
                Html::addCssClass($options, $this->linkActiveClass);
            }

            echo Html::a($text, $url, $options);
        }
    }
}
