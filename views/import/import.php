<?php

use app\widgets\form\ActiveForm;
use app\widgets\Modal;

$form_id = 'import_form';

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => __('Import'),
    'id' => 'import',
    'footer' => Html::submitButton(__('Import'), ['class' => 'btn btn-success', 'form' => $form_id]),
]);

$form = ActiveForm::begin(['options' => ['id' => $form_id, 'enctype' => 'multipart/form-data']]);


echo $this->render('objects/' . $model->viewPath, [
    'form' => $form,
    'model' => $model,
]);


echo $form->field($model, 'upload')->fileInput();

ActiveForm::end();

Modal::end();
