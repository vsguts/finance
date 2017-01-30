<?php

use app\widgets\form\ActiveForm;
use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;
use app\widgets\Checkboxes;

if ($model->isNewRecord) {
    $obj_id = 'classification-category_create';
    $header = __('Create category');
} else {
    $obj_id = 'classification-category_' . $model->id;
    $header = __('Classification: {classification-category}', [
        'classification-category' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('classification_manage'),
        'form' => $form_id,
    ]),
]);

$form = ActiveForm::begin([
    'options' => [
        'id' => $form_id,
    ]
]);

echo $form->field($model, 'name')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-name',
]);

echo $form->field($model, 'notes')->textarea([
    'rows' => 6,
    'id' => $form_id . '-notes',
]);

ActiveForm::end();

Modal::end();
