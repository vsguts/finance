<?php

echo $form->field($model, 'description')->textInput([
    'id' => $form_id . '-description',
]);

if (!$model->isNewRecord) {
    echo $form->field($model, 'name')->widget('app\widgets\Text');
}
