<?php

namespace app\models\search;


use app\behaviors\SearchBehavior;
use app\behaviors\TimestampConvertBehavior;
use yii\base\Model;

class TimestampSearch extends Model
{
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
            SearchBehavior::className(),
            [
                'class' => TimestampConvertBehavior::className(),
                'fields' => ['timestamp', 'timestamp_to']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $year = date('Y');
        $month = date('n');
        $first_month_day = mktime(0, 0, 0, $month, 1, $year);
        $month_days = date('t', $first_month_day);
        $last_month_day = mktime(0, 0, 0, $month, $month_days, $year);

        return [
            // [['timestamp'], 'default', 'value' => $today - 30 * SECONDS_IN_DAY],
            // [['timestamp_to'], 'default', 'value' => $today],
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
}