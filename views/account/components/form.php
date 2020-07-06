<?php

use yii\bootstrap\Tabs;
use app\widgets\form\ActiveForm;

$form = ActiveForm::begin(['id' => $form_id]);

$tabItems = [
    [
        'label' => __('General'),
        'content' => $this->render('form_general', [
            'form' => $form,
            'model' => $model,
            'form_id' => $form_id,
        ]),
        'active' => true,
    ],
    [
        'label' => __('Bank'),
        'content' => $this->render('form_bank', [
            'form' => $form,
            'model' => $model,
            'form_id' => $form_id,
        ]),
    ],
    [
        'label' => __('Label'),
        'content' => $this->render('form_label', [
            'form' => $form,
            'model' => $model,
            'form_id' => $form_id,
        ]),
    ],
];

echo Tabs::widget([
    'options' => [
        'id' => $form_id . '_tabs',
        // 'class' => 'app-tabs-save',
    ],
    'items' => $tabItems,
]);

ActiveForm::end();
