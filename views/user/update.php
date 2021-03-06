<?php

use app\widgets\form\ButtonsContatiner;
use app\widgets\Modal;

/**
 * @var \app\models\User $model
 */

if ($model->isNewRecord) {
    $obj_id = 'user_create';
    $header = __('Create user');
} else {
    $obj_id = 'user_' . $model->id;
    $header = __('User: {user}', [
        'user' => $model->name,
    ]);
}

$form_id = $obj_id . '_form';

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => $header,
    'id' => $obj_id,
    'footer' => ButtonsContatiner::widget([
        'model' => $model,
        'saveLink' => $model->canUpdate(),
        'form' => $form_id,
    ]),
]);

echo $this->render('components/form', [
    'model' => $model,
    'form_id' => $form_id,
    'roles' => isset($roles) ? $roles : null,
]);

Modal::end();
