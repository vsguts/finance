<?php

use yii\bootstrap\Tabs;
use app\widgets\form\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'settings_form',
    'labelCols' => 3,
]);

echo Tabs::widget([
    'options' => [
        'id' => 'settings_tabs',
        'class' => 'app-tabs-save'
    ],
    'items' => [
        [
            'label' => __('General'),
            'content' => $this->render('form_general', ['form' => $form, 'model' => $model]),
            'active' => true,
        ],
        [
            'label' => __('Description'),
            'content' => $this->render('form_description', ['form' => $form, 'model' => $model]),
        ],
        [
            'label' => __('Mailer'),
            'content' => $this->render('form_mailer', ['form' => $form, 'model' => $model]),
        ],
        [
            'label' => __('Transactions'),
            'content' => $this->render('form_transactions', ['form' => $form, 'model' => $model]),
        ],

    ],
]);

ActiveForm::end();
