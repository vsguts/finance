<?php

use app\widgets\form\ActiveForm;
use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;

if ($model->isNewRecord) {
    $obj_id = 'currency_create';
    $header = __('Create currency');
} else {
    $obj_id = 'currency_' . $model->id;
    $header = __('Currency: {currency}', [
        'currency' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('currency_manage'),
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

echo $form->field($model, 'code')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-code',
]);

echo $form->field($model, 'symbol')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-symbol',
]);

ActiveForm::end();

Modal::end();
