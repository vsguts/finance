<?php

namespace app\models\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class LogBehavior extends Behavior
{
    public $fields;

    public $log_model_class;
    
    public $id_field;

    public $skipLogging = false;

    public $apply_timestamp;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE  => 'logUpdate',
        ];
    }

    public function logUpdate($event)
    {
        if (!$this->skipLogging) {
            $model = $this->owner;
            $attributes = $model->attributes;
            $oldAttributes = $model->oldAttributes;
            foreach ($this->fields as $field) {
                if (
                    isset($attributes[$field])
                    && $attributes[$field] != $oldAttributes[$field]
                ) {
                    $record = new $this->log_model_class;
                    $record->{$this->id_field} = $model->id;
                    $record->user_id = Yii::$app->user->identity->id;
                    $record->field = $field;
                    $record->value = $attributes[$field];
                    $record->old_value = $oldAttributes[$field];
                    $record->timestamp = time();

                    if ($record->hasAttribute('apply_timestamp')) {
                        $formatter = Yii::$app->formatter;
                        $apply_timestamp = $this->apply_timestamp ?: time();
                        $record->apply_timestamp = $formatter->asTimestamp($formatter->asDate($apply_timestamp));
                    }

                    $record->save();
                }
            }
        }
    }

}
