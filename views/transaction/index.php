<?php

use app\assets\AppAsset;
use app\widgets\ActionsDropdown;
use app\widgets\grid\GridView;

$this->registerJs(AppAsset::appAccounts());
$this->registerJs(AppAsset::appClassifications());

$this->title = __('Transactions');
$this->params['breadcrumbs'][] = $this->title;

$detailsLink = function($model) {
    $action = $model->related_id ? 'transfer' : 'update';
    return [
        'label' => __('Edit'),
        'class' => 'app-modal',
        'href' => Url::to(['/transaction/' . $action, 'id' => $model->id, '_return_url' => Url::to()]),
        'data-target-id' => 'transaction_' . $model->id,
    ];
};

// Prepare template items
$template_items = [];
foreach ($templates as $template) {
    $template_items[] = [
        'label' => $template->template,
        'url' => Url::to(['update', 'form_template_id' => $template->id, '_return_url' => Url::to()]),
        'linkOptions' => [
            'class' => 'app-modal app-modal-force',
            'data-target-id' => 'transaction_create',
        ],
    ];
}

?>

<div class="transaction-index">

    <div class="pull-right buttons-container">

        <?php if (Yii::$app->user->can('transaction_edit')) : ?>
            <div class="btn-group">
                <?php
                    echo Html::a(__('Create transaction'), ['update', '_return_url' => Url::to()], [
                        'class' => 'btn btn-success app-modal app-modal-force',
                        'data-target-id' => 'transaction_create',
                    ]);
                    if ($template_items) {
                        echo ActionsDropdown::widget([
                            'layout' => 'success',
                            'label' => '',
                            'items' => $template_items,
                        ]);
                    }
                ?>
            </div>
            <div class="btn-group">
                <?= Html::a(__('Create transfer'), ['transfer', '_return_url' => Url::to()], [
                    'class' => 'btn btn-success app-modal app-modal-force',
                    'data-target-id' => 'transaction_transfer',
                ]) ?>
            </div>
        <?php endif; ?>

        <?php

        $items = [
            [
                'label' => __('Export selected'),
                'url' => Url::to(['/export/export/', 'object' => 'transaction']),
                'linkOptions' => [
                    'class' => 'app-modal app-modal-force',
                    'data-target-id' => 'export',
                    'data-app-process-items' => 'ids',
                ],
            ],
            [
                'label' => __('Export all'),
                'url' => Url::to(['/export/export/', 'object' => 'transaction', 'attributes' => ['queryParams' => Yii::$app->request->queryParams]]),
                'linkOptions' => [
                    'class' => 'app-modal app-modal-force',
                    'data-target-id' => 'export',
                ],
            ],
        ];

        if (Yii::$app->user->can('transaction_delete')) {
            $items[] = '<li role="presentation" class="divider"></li>';
            $items[] = [
                'label' => __('Delete selected'),
                'url' => Url::to(['delete']),
                'linkOptions' => [
                    'data-app-process-items' => 'id',
                    'data-confirm' => __('Are you sure you want to delete these items?'),
                    'data-method' => 'post',
                ]
            ];
        }
        if (Yii::$app->user->can('transaction_edit')) {
            $items[] = '<li role="presentation" class="divider"></li>';
            $items[] = [
                'label' => __('Import'),
                'url' => Url::to(['/import/import', 'object' => 'transactions', '_return_url' => Url::to()]),
                'linkOptions' => [
                    'class' => 'app-modal',
                    'data-target-id' => 'import',
                ],
            ];
        }

        echo ActionsDropdown::widget([
            'layout' => 'info',
            'items' => $items,
        ]);

        ?>

    </div>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('components/search', ['model' => $searchModel]) ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-condensed table-striped table-bordered table-hover table-highlighted app-float-thead'],
        'showFooter' => !empty($totals),
        'footerRowOptions' => [
            'class' => 'info',
        ],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'attribute' => 'timestamp',
                'format' => 'date',
                'link' => $detailsLink,
            ],
            [
                'attribute' => 'account',
                'label' => __('Account'),
                'value' => function($model, $key, $index, $column){
                    return $model->account->fullName;
                },
                'link' => function($model) {
                    if (Yii::$app->user->can('account_view')) {
                        return [
                            'class' => 'app-modal',
                            'href' => Url::to(['/account/update', 'id' => $model->account_id, '_return_url' => Url::to()]),
                            'data-target-id' => 'account_' . $model->account_id,
                        ];
                    }
                },
            ],
            [
                'attribute' => 'classification',
                'value' => function($model, $key, $index, $column){
                    return $model->classification ? $model->classification->name : null;
                },
                'link' => function($model) {
                    if ($model->classification && Yii::$app->user->can('classification_view')) {
                        return [
                            'class' => 'app-modal',
                            'href' => Url::to(['/classification/update', 'id' => $model->classification_id, '_return_url' => Url::to()]),
                            'data-target-id' => 'classification_' . $model->classification_id,
                        ];
                    }
                },
            ],
            [
                'attribute' => 'counterparty',
                'value' => function($model, $key, $index, $column){
                    return $model->counterparty ? $model->counterparty->name : null;
                },
                'link' => function($model) {
                    if ($model->counterparty && Yii::$app->user->can('counterparty_view')) {
                        return [
                            'class' => 'app-modal',
                            'href' => Url::to(['/counterparty/update', 'id' => $model->counterparty_id, '_return_url' => Url::to()]),
                            'data-target-id' => 'counterparty_' . $model->counterparty_id,
                        ];
                    }
                },
            ],
            [
                'attribute' => 'opening_balance',
                'value' => 'openingBalanceValue',
                'label' => __('Op. bal'),
                'headerOptions' => ['title' => __('Opening balance'), 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'data-container' => 'body'],
                'contentOptions' => ['class' => 'nowrap', 'align' => 'right'],
            ],
            [
                'attribute' => 'inflow',
                'value' => 'inflowValue',
                'contentOptions' => ['class' => 'nowrap text-success', 'align' => 'right'],
                'footer' => !empty($totals) ? $totals['inflowValue'] : '',
                'footerOptions' => ['class' => 'nowrap text-success', 'align' => 'right'],
            ],
            [
                'attribute' => 'outflow',
                'value' => 'outflowValue',
                'contentOptions' => ['class' => 'nowrap text-danger', 'align' => 'right'],
                'footer' => !empty($totals) ? $totals['outflowValue'] : '',
                'footerOptions' => ['class' => 'nowrap text-danger', 'align' => 'right'],
            ],
            [
                'attribute' => 'balance',
                'value' => 'balanceValue',
                'label' => __('Cl. bal'),
                'headerOptions' => ['title' => __('Closing balance'), 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'data-container' => 'body'],
                'contentOptions' => ['class' => 'nowrap', 'align' => 'right'],
            ],

            [
                'attribute' => 'has_attachments',
                'label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-paperclip']),
                'encodeLabel' => false,
                'format' => 'raw',
                'contentOptions' => ['align' => 'center'],
                'value' => function($model) {
                    return $model->has_attachments ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-paperclip']) : '';
                },
            ],

            [
                'class' => 'app\widgets\grid\ActionColumn',
                'size' => 'xs',
                'items' => [
                    $detailsLink,
                    function($model) {
                        if (Yii::$app->user->can('transaction_edit')) {
                            if ($model->related_id) {
                                $action = 'transfer';
                                $id = 'transaction_transfer';
                            } else {
                                $action = 'update';
                                $id = 'transaction_create';
                            }
                            return [
                                'label' => __('Copy'),
                                'href' => Url::to(['transaction/' . $action, 'copy_id' => $model->id, '_return_url' => Url::to()]),
                                'class' => 'app-modal app-modal-force',
                                'data-target-id' => $id,
                            ];
                        }
                    },
                    function($model) {
                        if (Yii::$app->user->can('transaction_delete')) {
                            return [
                                'label' => __('Delete'),
                                'href' => Url::to(['transaction/delete', 'id' => $model->id, '_return_url' => Url::to()]),
                                'data-method' => 'post',
                                'data-confirm' => __('Are you sure you want to delete this item?'),
                            ];
                        }
                    },
                ],
            ],

        ],
    ]); ?>
    
</div>
