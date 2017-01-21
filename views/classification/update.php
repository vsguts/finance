<?php

use app\widgets\form\ActiveForm;
use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;

if ($model->isNewRecord) {
    $obj_id = 'classification_create';
    $header = __('Create classification');
} else {
    $obj_id = 'classification_' . $model->id;
    $header = __('Classification: {classification}', [
        'classification' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('classification_manage'),
        'form' => $form_id,
    ]),
]);

$form = ActiveForm::begin([
    'labelCols' => 3,
    'options' => [
        'id' => $form_id,
    ]
]);


echo $form->field($model, 'name')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-name',
]);

echo $form->field($model, 'type')->dropDownList($model->getLookupItems('type'), [
    'id' => $form_id . '-type',
]);

echo $form->field($model, 'notes')->textarea([
    'rows' => 6,
    'id' => $form_id . '-notes',
]);


ActiveForm::end();

Modal::end();
