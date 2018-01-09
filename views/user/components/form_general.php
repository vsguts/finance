<?php

echo $form->field($model, 'name')->textInput([
    'id' => $form_id . '-name',
    'maxlength' => true,
]);

echo $form->field($model, 'email')->textInput([
    'id' => $form_id . '-email',
    'maxlength' => true,
]);

echo $form->field($model, 'password')->passwordInput([
    'id' => $form_id . '-password',
]);

echo $form->field($model, 'status')->dropDownList($model->getLookupItems('status'), [
    'id' => $form_id . '-status',
]);

if (!$model->isNewRecord) {
    echo $form->field($model, 'created_at')->text(['format' => 'date']);
    echo $form->field($model, 'updated_at')->text(['format' => 'date']);
}
