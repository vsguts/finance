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
        $today = mktime(0, 0, 0);

        return [
            [['timestamp'], 'default', 'value' => $today - 30 * SECONDS_IN_DAY],
            [['timestamp_to'], 'default', 'value' => $today],
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