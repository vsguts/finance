<?php

use app\widgets\grid\GridView;
use app\widgets\ActionsDropdown;

$this->title = __('Counterparties');
$this->params['breadcrumbs'][] = $this->title;

$detailsLink = function($model) {
    return [
        'label' => __('Edit'),
        'class' => 'app-modal',
        'href' => Url::to(['/counterparty/update', 'id' => $model->id, '_return_url' => Url::to()]),
        'data-target-id' => 'counterparty_' . $model->id,
    ];
};

?>
<div class="counterparty-index">

    <?php if (Yii::$app->user->can('counterparty_manage')) : ?>

    <div class="pull-right buttons-container">
        <div class="btn-group">
            <?= Html::a(__('Create counterparty'), ['update', '_return_url' => Url::to()], [
                'class' => 'btn btn-success app-modal',
                'data-target-id' => 'counterparty_create',
            ]) ?>
        </div>
        <?= ActionsDropdown::widget([
            'layout' => 'info',
            'items' => [
                [
                    'label' => __('Fill transactions'),
                    'url' => Url::to(['/transaction/fill', '_return_url' => Url::to()]),
                    'linkOptions' => [
                        'data-confirm' => __('Are you sure you want to fill these items?'),
                        'data-method' => 'post',
                    ],
                    'visible' => Yii::$app->user->can('transaction_update'),
                ],
                '<li role="presentation" class="divider"></li>',
                [
                    'label' => __('Delete selected'),
                    'url' => Url::to(['delete']),
                    'linkOptions' => [
                        'data-app-process-items' => 'id',
                        'data-confirm' => __('Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                    ],
                ],
            ],
        ]) ?>
    </div>

    <?php endif; ?>

    <ul class="nav nav-pills app-tool-links">
        <li role="presentation"><a href="<?= Url::to(['counterparty-category/index']) ?>"><?= __('Counterparty categories') ?></a></li>
    </ul>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('components/search', ['model' => $searchModel]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'attribute' => 'name',
                'link' => $detailsLink,
            ],
            [
                'attribute' => 'category',
                'label' => __('Category'),
                'value' => 'category.name',
                'link' => function($model) {
                    if ($model->category) {
                        return [
                            'class' => 'app-modal',
                            'href' => Url::to(['/counterparty-category/update', 'id' => $model->category->id, '_return_url' => Url::to()]),
                            'data-target-id' => 'counterparty-category_' . $model->category->id,
                        ];
                    }
                },
            ],

            [
                'class' => 'app\widgets\grid\CounterColumn',
                'label' => __('Transactions'),
                'modelClass' => 'app\models\Transaction',
                'modelField' => 'counterparty_id'
            ],

            [
                'class' => 'app\widgets\grid\ActionColumn',
                'size' => 'xs',
                'items' => [
                    $detailsLink,
                    function($model) {
                        if (Yii::$app->user->can('counterparty_manage')) {
                            return [
                                'label' => __('Delete'),
                                'href' => Url::to(['counterparty/delete', 'id' => $model->id, '_return_url' => Url::to()]),
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
