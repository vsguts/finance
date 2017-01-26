<?php

use app\models\Account;
use yii\bootstrap\Tabs;

// Prepare bank accounts
$accounts = ['all' => [
    'label' => '- ' . __('All') . ' -',
]];
foreach (Account::find()->sorted()->all() as $account) {
    $accounts[$account->id] = [
        'label' => $account->getFullName(),
        'status' => $account->status,
    ];
}


echo Tabs::widget([
    'options' => [
        'id' => $form_id . '_app_objects_tabs',
    ],
    'navType' => 'nav-pills',
    'items' => [
        [
            'label' => __('Accounts'),
            'content' => $form
                ->field($model, 'data[accounts]')
                ->label(__('Accounts'))
                ->checkboxList($accounts, [
                    'item' => function($index, $label, $name, $checked, $value) use($model) {
                        $status_class = '';
                        if (!empty($label['status'])) {
                            $status_class = 'status-' . $label['status'];
                        }
                        return Html::tag(
                            'div',
                            Html::checkbox($name, $checked, ['label' => $label['label'], 'value' => $value]),
                            ['class' => ['checkbox', $status_class]]
                        );
                    },
                ]),
        ],
    ],
]);

