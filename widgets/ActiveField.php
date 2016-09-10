<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\ActiveField as YActiveField;

class ActiveField extends YActiveField
{
    protected $tooltip = false;

    public function tooltip($tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    public function label($label = null, $options = [])
    {
        if ($label !== false && $this->tooltip) {
            $options = array_merge($this->labelOptions, $options);
            if (empty($options['label'])) {
                $options['label'] = Html::encode($this->model->getAttributeLabel($this->attribute));
            }
            $options['label'] .= ' ' . Tooltip::widget(['tooltip' => $this->tooltip]);
        }

        return parent::label($label, $options);;
    }
}
