<?php

use app\widgets\ActiveForm;
use app\widgets\Modal;

$_form_id = 'form-template_create_form';

Modal::begin([
    'header' => __('Create template'),
    'id' => 'form-template_create',
    'footer' => Html::submitButton(__('Create'), [
        'form' => $_form_id,
        'class' => 'btn btn-success app-serialize-form',
        'data-target-id' => $_form_id . '-data',
        'data-form-id' => $form_id,
    ]),
]);

$form = ActiveForm::begin([
    'options' => [
        'id' => $_form_id
    ],
]);

echo Html::activeHiddenInput($model, 'data', [
    'id' => $_form_id . '-data',
]);

echo $form->field($model, 'template')->textInput([
    'maxlength' => true,
    'id' => $_form_id . '-template',
]);


ActiveForm::end();

Modal::end();
