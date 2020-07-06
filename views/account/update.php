<?php

use app\models\Currency;
use app\widgets\form\ActiveForm;
use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;

/**
 * @var \app\models\Account $model
 */

if ($model->isNewRecord) {
    $obj_id = 'account_create';
    $header = __('Create account');
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

echo $this->render('components/form', [
    'model' => $model,
    'form_id' => $form_id,
]);

Modal::end();
