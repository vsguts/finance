<?php

namespace app\widgets\form;

use app\widgets\Tooltip;
use Yii;
use yii\bootstrap\ActiveField as YActiveField;
use yii\helpers\Html;

class ActiveField extends YActiveField
{
    protected $tooltip = false;

    public function text($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        Html::removeCssClass($options, 'form-control');
        Html::addCssClass($options, 'form-control-static');
        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
        $method = 'asRaw';
        if (isset($options['formatter'])) {
            $method = 'as' . ucfirst($options['formatter']);
        }
        $value = Yii::$app->formatter->$method($value);
        $this->parts['{input}'] = Html::tag('p', $value, $options);

        return $this;
    }

    public function tooltip($tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    public function label($label = null, $options = [])
    {
        if ($this->tooltip) {
            $options = array_merge($this->labelOptions, $options);
            $options['label'] = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));
            $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);
            $options['label'] .= ' ' . Tooltip::widget(['tooltip' => $this->tooltip]);
            return $this;
        }

        return parent::label($label, $options);
    }
}
