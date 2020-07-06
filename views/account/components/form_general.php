<?php

use app\models\Currency;

/**
 * @var \app\models\Account $model
 */

echo $form->field($model, 'name')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-name',
]);

echo $form->field($model, 'status')->dropDownList($model->getLookupItems('status'), [
    'id' => $form_id . '-status',
]);

echo $form->field($model, 'currency_id')->dropDownList(Currency::find()->scroll(), [
    'id' => $form_id . '-currency_id',
]);

echo $form->field($model, 'init_balance')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-_balance',
]);

echo $form->field($model, 'notes')->textarea([
    'rows' => 4,
    'id' => $form_id . '-notes',
]);
