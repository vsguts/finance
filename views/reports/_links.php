<?php

use yii\bootstrap\Tabs;

/**
 * @var \app\models\report\transactions\AbstractTransactionsReport $searchModel
 */

$items = [
    [
        'label' => __('Account turnovers'),
        'controllerId' => 'reports/account-turnovers',
    ],
    [
        'label' => __('Account turnovers ({currency})', ['currency' => Yii::$app->currency->getBaseCurrencyCode()]),
        'controllerId' => 'reports/account-turnovers-base',
    ],
    [
        'label' => __('Classification turnovers'),
        'controllerId' => 'reports/classification-turnovers',
    ],
    [
        'label' => __('Classification category turnovers (UAH)'),
        'controllerId' => 'reports/classification-category-turnovers',
    ],
    [
        'label' => __('Counterparty turnovers'),
        'controllerId' => 'reports/counterparty-turnovers',
    ],
    [
        'label' => __('Balance dynamics'),
        'controllerId' => 'reports/balance-dynamics',
    ],
];

foreach ($items as &$item) {
    if (Yii::$app->controller->id == $item['controllerId']) {
        $item['active'] = true;
    } else {
        $item['url'] = [
            $item['controllerId'] . '/view',
            'timestamp' => $searchModel->timestamp,
            'timestamp_to' => $searchModel->timestamp_to,
            'account_id' => $searchModel->account_id,
        ];
    }
}

echo Tabs::widget([
    'items' => $items,
]);

