<?php

use yii\bootstrap\Tabs;
use app\widgets\ActiveForm;
use app\widgets\ButtonsContatiner;
use app\widgets\Modal;
use app\models\Currency;

if ($model->isNewRecord) {
    $obj_id = 'account_create';
    $header = __('Create bank account');
} else {
    $obj_id = 'account_' . $model->id;
    $header = __('Account: {account}', [
        'account' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('account_manage'),
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

echo $form->field($model, 'status')->dropDownList($model->getLookupItems('status'), [
    'id' => $form_id . '-status',
]);

echo $form->field($model, 'currency_id')->dropDownList(Currency::find()->scroll(), [
    'id' => $form_id . '-currency_id',
]);

echo $form->field($model, 'bank')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-bank',
]);

echo $form->field($model, 'account_number')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-account_number',
]);

echo $form->field($model, 'init_balance')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-_balance',
]);

echo $form->field($model, 'import_processor')->dropDownList($model->getLookupItems('import_processor', ['empty' => true]), [
    'id' => $form_id . '-import_processor',
]);

echo $form->field($model, 'notes')->textarea([
    'rows' => 4,
    'id' => $form_id . '-notes',
]);


ActiveForm::end();

Modal::end();
