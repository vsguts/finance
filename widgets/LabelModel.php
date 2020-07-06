<?php

namespace app\widgets;

use app\models\contracts\LabeledModel;
use yii\base\Widget;
use yii\helpers\Html;

class LabelModel extends Widget
{
    /**
     * @var LabeledModel
     */
    public $model;

    /**
     * @var string
     */
    public $value;

    public function run()
    {
        $model = $this->model;
        $value = $this->value ?: $model->name;
        if ($model->getLabelEnabled()) {
            return Html::tag('span', $value, [
                'class' => 'badge',
                'style' => [
                    'color' => $model->label_text_color,
                    'background-color' => $model->label_bg_color
                ]
            ]);
        } else {
            return $value;
        }
    }
}
