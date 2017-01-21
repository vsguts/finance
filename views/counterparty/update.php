<?php

use yii\bootstrap\Tabs;
use app\widgets\form\ActiveForm;
use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;
use app\widgets\Checkboxes;
use app\models\CounterpartyCategory;

if ($model->isNewRecord) {
    $obj_id = 'counterparty_create';
    $header = __('Create counterparty');
} else {
    $obj_id = 'counterparty_' . $model->id;
    $header = __('Counterparty: {counterparty}', [
        'counterparty' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('counterparty_manage'),
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
    'id' => $obj_id . '-name',
]);

echo $form->field($model, 'category_id')->dropDownList(CounterpartyCategory::find()->scroll(['empty' => true]), [
    'id' => $obj_id . '-category_id',
    'class' => ['form-control', 'app-select2'],
]);

echo $form->field($model, 'notes')->textarea([
    'rows' => 6,
    'id' => $obj_id . '-notes',
]);

ActiveForm::end();

Modal::end();
