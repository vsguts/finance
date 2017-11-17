<?php

namespace app\models\report;


use app\models\behaviors\TimestampConvertBehavior;
use app\models\components\SearchTrait;
use yii\base\Model;

abstract class TimestampSearch extends Model
{
    use SearchTrait;

    public $timestamp;

    public $timestamp_to;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampConvertBehavior::class,
                'fields' => ['timestamp', 'timestamp_to']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // $today = mktime(0, 0, 0);
        // return [
        //     [['timestamp'], 'default', 'value' => $today - 30 * SECONDS_IN_DAY],
        //     [['timestamp_to'], 'default', 'value' => $today],
        // ];

        $year = date('Y');
        $month = date('n');
        $first_month_day = mktime(0, 0, 0, $month, 1, $year);
        $month_days = date('t', $first_month_day);
        $last_month_day = mktime(0, 0, 0, $month, $month_days, $year);
        return [
            [['timestamp'], 'default', 'value' => $first_month_day],
            [['timestamp_to'], 'default', 'value' => $last_month_day],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'timestamp' => __('Time'),
            'timestamp_to' => __('Time to'),
        ];
    }


    protected function getQueryTimestampFrom()
    {
        return $this->timestamp;
    }

    protected function getQueryTimestampTo()
    {
        return $this->timestamp_to + SECONDS_IN_DAY - 1;
    }

}