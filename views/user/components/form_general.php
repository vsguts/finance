<?php

echo $form->field($model, 'name')->textInput(['maxlength' => true]);

echo $form->field($model, 'email')->textInput(['maxlength' => true]);

echo $form->field($model, 'password')->passwordInput();

echo $form->field($model, 'status')->dropDownList($model->getLookupItems('status'));

if (!$model->isNewRecord) {
    echo $form->field($model, 'created_at')->text(['formatter' => 'date']);
    echo $form->field($model, 'updated_at')->text(['formatter' => 'date']);
}
