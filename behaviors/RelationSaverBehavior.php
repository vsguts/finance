<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

class RelationSaverBehavior extends Behavior
{
    public $relation;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'update',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'update',
        ];
    }

    public function init()
    {
        parent::init();
        if (!$this->relation) {
            throw new Exception('RelationSaverBehavior: Relation not found');
        }
    }

    public function update()
    {
        $model = $this->owner;
        $form_name = $model->formName();
        $post = Yii::$app->request->post();
        if (isset($post[$form_name][$this->relation])) {
            $model->unlinkAll($this->relation, true);
            $relation_class = $model->{'get' . $this->relation}()->modelClass;
            foreach ((array)$post[$form_name][$this->relation] as $id) {
                if ($id) {
                    $model2 = $relation_class::findOne($id);
                    $model->link($this->relation, $model2);
                }
            }
        }

    }

}
