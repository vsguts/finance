<?php

/**
 * @var \app\models\Account $model
 */

echo $form->field($model, 'label_enabled')->checkbox([
    'id' => $form_id . '-label_enabled',
    'class' => 'checkboxfix',
], false);

echo $form->field($model, 'label_text_color')->textInput([
    'type' => 'color',
    'id' => $form_id . '-label_text_color',
]);

echo $form->field($model, 'label_bg_color')->textInput([
    'type' => 'color',
    'id' => $form_id . '-label_bg_color',
]);
