<?php

namespace app\models\report;

abstract class ReportAbstract extends TimestampSearch implements ReportInterface
{
    public $account_id;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'account_id' => __('Account'),
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['account_id'], 'safe'],
        ]);
    }

}
