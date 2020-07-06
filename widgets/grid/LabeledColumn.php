<?php

namespace app\widgets\grid;

use app\models\contracts\LabeledModel;
use app\widgets\LabelModel;
use Closure;

class LabeledColumn extends DataColumn
{
    public $format = 'raw';

    /**
     * @var null|Closure
     */
    public $labeledModel = null;

    /**
     * @param LabeledModel $model
     * @param mixed        $key
     * @param int          $index
     *
     * @return string
     * @throws \Exception
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);

        $labeledModel = $this->labeledModel
            ? call_user_func($this->labeledModel, $model, $key, $index, $this)
            : $model;

        return LabelModel::widget([
            'model' => $labeledModel,
            'value' => $value,
        ]);
    }
}
