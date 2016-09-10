<?php

namespace app\components\app;

use Yii;
use yii\base\Component;
use app\models\Weekend;

class Calendar extends Component
{

    protected $work_days = [];

    /**
     * Gets all days in period
     *
     * @param  mixed $from Timestamp or Date
     * @param  mixed $to   Timestamp or Date
     * @return array
     */
    public function getPeriodDays($from, $to)
    {
        if ($from > $to) {
            list($from, $to) = [$to, $from];
        }

        $formatter = Yii::$app->formatter;
        $from = $this->dayTimestamp($from);
        $to = $this->dayTimestamp($to);

        $dates = [];
        $current = $from;
        while ($current <= $to) {
            $dates[date('Y-n-j', $current)] = [
                'year' => date('Y', $current),
                'month' => date('n', $current),
                'day' => date('j', $current),
            ];
            $current += SECONDS_IN_DAY;
        }

        return $dates;
    }

    /**
     * Gets all months in period
     *
     * @param  mixed $from Timestamp or Date
     * @param  mixed $to   Timestamp or Date
     * @return array
     */
    public function getPeriodMonths($from, $to)
    {
        if ($from > $to) {
            list($from, $to) = [$to, $from];
        }

        $formatter = Yii::$app->formatter;
        $to = $this->dayTimestamp($to);

        $from = (new \DateTime)->setTimestamp($this->dayTimestamp($from));
        $to = (new \DateTime)->setTimestamp($this->dayTimestamp($to));
        $interval = new \DateInterval('P1M');
        $period = new \DatePeriod($from, $interval, $to);

        $months = [];
        foreach ($period as $period_step) {
            $months[] = [
                'year' => $period_step->format('Y'),
                'month' => $period_step->format('n'),
            ];
        }

        return $months;
    }

    /**
     * Returns array of month first and last timestamp
     * @param  string $year  Number of year
     * @param  string $month number of month
     * @return array
     */
    public function getMonthTimestamps($year, $month)
    {
        $from = mktime(0, 0, 0, $month, 1, $year);
        $month_days = date('t', $from);
        $to = mktime(0, 0, 0, $month, $month_days, $year);

        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Gets clear timestamp of beginning of the day
     *
     * @param  int $time Timestamp
     * @return int       Timestamp
     */
    public function dayTimestamp($time)
    {
        $formatter = Yii::$app->formatter;
        return $formatter->asTimestamp($formatter->asDate($time));
    }

    public function getTimestampDateData($timestamp)
    {
        return [
            'year' => date('Y', $timestamp),
            'month' => date('n', $timestamp),
            'day' => date('j', $timestamp),
        ];
    }

}
