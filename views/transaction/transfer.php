<?php

use app\models\Account;
use app\models\Classification;
use app\models\Counterparty;
use app\widgets\ActiveForm;
use app\widgets\ButtonsContatiner;
use app\widgets\Modal;

$obj_id = 'transaction_transfer';
if ($model->transaction) {
    $obj_id = 'transaction_' . $model->transaction->id;
}
$header = __('Transfer');

$form_id = $obj_id . '_form';


Modal::begin([
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('transaction_edit') && !($model->transaction && !$model->transaction->canUpdate),
        'form' => $form_id,
        'create' => !$model->transaction,
    ]),
]);

$form = ActiveForm::begin([
    'labelCols' => 3,
    'options' => [
        'id' => $form_id,
    ]
]);

echo $form->field($model, 'timestamp')->widget('app\widgets\DatePicker', ['options' => [
    'id' => $form_id . '-timestamp',
]]);

echo $form->field($model, 'account_id_from')->dropDownList(Account::find()->active($model->account_id_from)->scroll(), [
    'id' => $form_id . '-account_id_from',
    'class' => 'form-control app-account app-select2',
]);

echo $form->field($model, 'account_id_to')->dropDownList(Account::find()->active($model->account_id_to)->scroll(), [
    'id' => $form_id . '-account_id_to',
    'class' => 'form-control app-account app-select2',
]);

// echo $form->field($model, 'classification_id')->dropDownList(Classification::find()->scroll(['empty' => true]), [
//     'id' => $form_id . '-classification_id',
// ]);

echo $form->field($model, 'counterparty_id')->dropDownList(Counterparty::find()->scroll(['empty' => true]), [
    'id' => $form_id . '-counterparty_id',
    'class' => 'form-control app-select2',
]);


$field = $form->field($model, 'value_from')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-value_from',
]);
echo Html::tag('div', $field, [
    'class' => 'h app-accounts-currency',
    'data-account-from' => $form_id . '-account_id_from',
    'data-account-to' => $form_id . '-account_id_to',
]);

echo $form->field($model, 'value')->textInput([
    'maxlength' => true,
    'id' => $form_id . '-value',
]);


echo $form->field($model, 'description')->textarea([
    'rows' => 4,
    'id' => $form_id . '-description',
]);


// User
if ($model->transaction) {
    if ($model->transaction->user) {
        echo $form->field($model->transaction, 'user')->widget('app\widgets\Text', [
            'value' => Html::a(
                $model->transaction->user->name,
                Url::to(['user/update', 'id' => $model->transaction->user->id]),
                ['target' => '_blank']
            ),
        ]);
    }
    if ($model->transaction->created_at) {
        echo $form->field($model->transaction, 'created_at')->widget('app\widgets\Text', ['formatter' => 'datetime']);
    }
}



ActiveForm::end();

Modal::end();

