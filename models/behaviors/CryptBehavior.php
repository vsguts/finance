<?php

namespace app\models\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\db\ActiveRecord;

class CryptBehavior extends Behavior
{
    public $fields = [];

    public function events()
    {
        return [
            Model::EVENT_AFTER_VALIDATE  => 'encrypt',
            ActiveRecord::EVENT_AFTER_FIND  => 'decrypt',
            ActiveRecord::EVENT_AFTER_INSERT  => 'decrypt',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'decrypt',
        ];
    }

    public function encrypt($event)
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = base64_encode(Yii::$app->security->encryptByPassword($model->$field, Yii::$app->params['cryptKey']));
            }
        }
    }

    public function decrypt($event)
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = Yii::$app->security->decryptByPassword(base64_decode($model->$field), Yii::$app->params['cryptKey']);
            }
        }
    }

}
