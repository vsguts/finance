<?php

use app\widgets\ActiveForm;
use app\widgets\ButtonsContatiner;
use app\widgets\Modal;
use app\models\Account;
use app\models\Classification;
use app\models\Counterparty;

if ($model->isNewRecord) {
    $obj_id = 'transaction_create';
    $header = __('Create cash transaction');
} else {
    $obj_id = 'transaction_' . $model->id;
    $header = __('Transaction: {transaction}', [
        'transaction' => Yii::$app->formatter->asDate($model->timestamp),
    ]);
}

$form_id = $obj_id . '_form';

$create_template_url = Url::to(['/form-template/create', 'model' => $model->formName(), 'form_id' => $form_id, '_return_url' => Yii::$app->request->getQueryParam('_return_url')]);
$create_template_btn = Html::a(__('Save as template'), $create_template_url, [
    'class' => 'btn btn-info app-modal app-modal-force',
    'data-target-id' => 'form-template_create',
]);

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => Yii::$app->user->can('transaction_edit') && $model->getCanUpdate(),
        'form' => $form_id,
        'beforeBtn' => Html::tag('div', $create_template_btn, ['class' => 'pull-left']),
    ]),
]);

$form = ActiveForm::begin([
    // 'labelCols' => 3,
    'options' => [
        'id' => $form_id,
    ]
]);

echo $form->field($model, 'timestamp')->widget('app\widgets\DatePicker', ['options' => [
    'id' => $form_id . '-timestamp',
]]);

echo $form->field($model, 'account_id')->dropDownList(Account::find()->active($model->account_id)->scroll(), [
    'id' => $form_id . '-account_id',
    'class' => 'form-control app-select2',
]);

echo $form->field($model, 'classification_id')->dropDownList(Classification::find()->inout()->scroll(), [
    'id' => $form_id . '-classification_id',
    'class' => 'form-control app-classification app-select2',
]);

echo $form->field($model, 'counterparty_id')->dropDownList(Counterparty::find()->scroll(['empty' => true]), [
    'id' => $form_id . '-counterparty_id',
    'class' => 'form-control app-select2',
]);


// Preparing: Opening balance, inflow, outflow, closing balance
$converted = [
    'opening_balance' => null,
    'inflow' => null,
    'outflow' => null,
    'balance' => null,
];
if (
    !$model->isNewRecord
    && $model->account
    && $model->account->currency_id != Yii::$app->currency->getBaseCurrencyId()
) {
    $converted = [
        'opening_balance' => $model->openingBalanceConverted,
        'inflow' => $model->inflowConverted,
        'outflow' => $model->outflowConverted,
        'balance' => $model->balanceConverted,
    ];
    $currency = Yii::$app->currency->getBaseCurrency();
    foreach ($converted as &$value) {
        if ($value) {
            $value = Yii::$app->formatter->asMoney($value) . ' ' . $currency->symbol;
        } else {
            $value = null;
        }
    }
}


// Opening balance
if (!$model->isNewRecord) {
    echo $form->field($model, 'opening_balance')->widget('app\widgets\Text', ['value' => $model->openingBalanceValue])->hint($converted['opening_balance']);
}


// Inflow, outflow
$calc_template = '{input}<div class="help-block app-input-calc-view h"></div>';

echo Html::activeHiddenInput($model, 'inflow', ['value' => 0, 'id' => $form_id . '-inflow-hidden']);
$field = $form
    ->field($model, 'inflow', ['inputTemplate' => $calc_template])
    ->hint($converted['inflow'])
    ->textInput([
        'class' => 'form-control app-input-calc',
        'id' => $form_id . '-inflow',
        'maxlength' => true,
    ]);
echo Html::tag('div', $field, ['class' => 'app-classification-inflow']);

echo Html::activeHiddenInput($model, 'outflow', ['value' => 0, 'id' => $form_id . '-outflow-hidden']);
$field = $form
    ->field($model, 'outflow', ['inputTemplate' => $calc_template])
    ->hint($converted['outflow'])
    ->textInput([
        'class' => 'form-control app-input-calc',
        'id' => $form_id . '-outflow',
        'maxlength' => true,
    ]);
echo Html::tag('div', $field, ['class' => 'app-classification-outflow']);


// Closing balance
if (!$model->isNewRecord) {
    echo $form->field($model, 'balance')->widget('app\widgets\Text', ['value' => $model->balanceValue])->hint($converted['balance']);
}


// Description
echo $form->field($model, 'description')->textarea([
    'rows' => 4,
    'id' => $form_id . '-description',
]);

// Attachments
$widget = $form->field($model, 'attachments')->widget('app\widgets\Attachments');
if ($widget->parts['{input}']) {
    echo $widget;
}
echo $form->field($model, 'attachmentsUpload[main][]')->fileInput(['multiple' => true]);


// User
if (!$model->isNewRecord && $model->user) {
    echo $form->field($model, 'user_id')->widget('app\widgets\Text', [
        'value' => Html::a(
            $model->user->name,
            Url::to(['user/update', 'id' => $model->user->id]),
            ['target' => '_blank']
        ),
    ]);
}

if (!$model->isNewRecord) {
    echo $form->field($model, 'created_at')->widget('app\widgets\Text', ['formatter' => 'datetime']);
}

ActiveForm::end();

Modal::end();
