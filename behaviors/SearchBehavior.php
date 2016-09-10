<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;

class SearchBehavior extends Behavior
{
    public function processParams($params)
    {
        $form_name = $this->owner->formName();
        $_params = $params;
        unset($_params[$form_name]);
        unset($_params['r']);
        $params[$form_name] = array_merge(
            $_params,
            isset($params[$form_name]) ? $params[$form_name] : []
        );
        
        return $params;
    }

    public function addRangeCondition($query, $field = 'timestamp', $to_suffix = '_to', $format = 'timestamp')
    {
        $model = $this->owner;

        if ($model->$field) {
            $query->andWhere(['>=', $field, $this->formatField($model->$field, $format)]);
        }

        $field_to = $field . $to_suffix;
        if ($model->$field_to) {
            $to = $this->formatField($model->$field_to, $format);
            if ($format == 'timestamp') {
                $to += SECONDS_IN_DAY - 1;
            }
            $query->andWhere(['<=', $field, $to]);
        }
    }

    public function getPaginationDefaults()
    {
        return [
            'pageSizeLimit' => [50, 500],
            'defaultPageSize' => 100,
        ];
    }

    protected function formatField($value, $format)
    {
        if (!$format) {
            return $value;
        }

        $method = 'as' . $format;
        return Yii::$app->formatter->$method($value);
    }
    
}
