<?php

namespace app\components\app;

use Yii;
use yii\base\Component;
use yii\base\Exception;

class Calendar extends Component
{

    protected $work_days = [];

    /**
     * Gets all days in period
     * @param  mixed $from Timestamp or Date
     * @param  mixed $to   Timestamp or Date
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getPeriodDays($from, $to, $format = 'params')
    {
        if ($from > $to) {
            list($from, $to) = [$to, $from];
        }

        $from = (new \DateTime)->setTimestamp($this->dayTimestamp($from));
        $to = (new \DateTime)->setTimestamp($this->dayTimestamp($to));
        $interval = new \DateInterval('P1D'); // one day
        $to->add($interval);
        $dates = [];
        foreach (new \DatePeriod($from, $interval, $to) as $step) {
            $stepTimestamp = $step->getTimestamp();
            $key = $step->format('Y-n-j');
            if ($format == 'params') {
                $day = $this->getTimeData($stepTimestamp);
            } elseif ($format == 'timestamp') {
                $day = $stepTimestamp;
            } else {
                throw new Exception('Unknown format');
            }
            $dates[$key] = $day;
        }

        return $dates;
    }

    /**
     * Gets all months in period
     *
     * @param  mixed $from Timestamp or Date
     * @param  mixed $to   Timestamp or Date
     * @param  string $format
     * @return array
     * @throws Exception
     */
    public function getPeriodMonths($from, $to, $format = 'params')
    {
        $from = strtotime('first day of this month', $from);
        $to = strtotime('last day of this month', $to);

        if ($from > $to) {
            list($from, $to) = [$to, $from];
        }

        $to = $this->dayTimestamp($to);

        $from = (new \DateTime)->setTimestamp($this->dayTimestamp($from));
        $to = (new \DateTime)->setTimestamp($this->dayTimestamp($to));
        $interval = new \DateInterval('P1M');
        $period = new \DatePeriod($from, $interval, $to);

        $months = [];
        if ($format === 'timestamp') {
            foreach ($period as $period_step) {
                $timestamp = mktime(0, 0, 0, $period_step->format('n'), 1, $period_step->format('Y'));
                $months[$timestamp] = $period_step->format('Y') . ' ' . $period_step->format('F');
            }
        } elseif ($format === 'params') {
            foreach ($period as $period_step) {
                $months[] = [
                    'year' => $period_step->format('Y'),
                    'month' => $period_step->format('n'),
                ];
            }
        } else {
            throw new Exception('Unknown format');
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
    public function dayTimestamp($time = null)
    {
        if (!$time) {
            $time = time();
        }
        $formatter = Yii::$app->formatter;
        return $formatter->asTimestamp($formatter->asDate($time));
    }

    public function getTimeData($timestamp)
    {
        $timestamp = Yii::$app->formatter->asTimestamp($timestamp);

        return [
            'year' => date('Y', $timestamp),
            'month' => date('n', $timestamp),
            'day' => date('j', $timestamp),
        ];
    }

}
