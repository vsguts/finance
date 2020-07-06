<?php

use app\models\Currency;

/**
 * @var \app\models\Account $model
 */

echo $form->field($model, 'bank')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-bank',
]);

echo $form->field($model, 'account_number')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-account_number',
]);

echo $form->field($model, 'import_processor')->dropDownList($model->getLookupItems('import_processor', ['empty' => true]), [
    'id' => $form_id . '-import_processor',
]);
