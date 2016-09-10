<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class EncoderBehavior extends Behavior
{
    public $fields = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'decode',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'encode',
            ActiveRecord::EVENT_AFTER_INSERT => 'decode',
            ActiveRecord::EVENT_AFTER_UPDATE => 'decode',
        ];
    }

    public function encode($event)
    {
        $model = $this->owner;
        foreach ((array)$this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = $model->$field ? Json::encode($model->$field) : '';
            }
        }
    }

    public function decode($event)
    {
        $model = $this->owner;
        foreach ((array)$this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = $model->$field ? Json::decode($model->$field) : '';
            }
        }
    }

}
