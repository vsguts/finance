<?php

use app\widgets\grid\GridView;
use app\widgets\ActionsDropdown;

$this->title = __('Counterparty categories');
$this->params['breadcrumbs'][] = $this->title;

$detailsLink = function($model) {
    return [
        'label' => __('Edit'),
        'class' => 'app-modal',
        'href' => Url::to(['/counterparty-category/update', 'id' => $model->id, '_return_url' => Url::to()]),
        'data-target-id' => 'counterparty-category_' . $model->id,
    ];
};

?>
<div class="counterparty_category-index">

    <?php if (Yii::$app->user->can('counterparty_manage')) : ?>

    <div class="pull-right buttons-container">
        <div class="btn-group">
            <?= Html::a(__('Create category'), ['update', '_return_url' => Url::to()], [
                'class' => 'btn btn-success app-modal',
                'data-target-id' => 'counterparty-category_create',
            ]) ?>
        </div>
        <?= ActionsDropdown::widget([
            'layout' => 'info',
            'items' => [
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
        <li role="presentation"><a href="<?= Url::to(['counterparty/index']) ?>"><?= __('Counterparties') ?></a></li>
    </ul>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('components/search', ['model' => $searchModel]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            [
                'attribute' =>'name',
                'link' => $detailsLink,
            ],

            [
                'class' => 'app\widgets\grid\CounterColumn',
                'label' => __('Counterparties'),
                'modelClass' => 'app\models\Counterparty',
                'modelField' => 'category_id'
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
                                'href' => Url::to(['counterparty-category/delete', 'id' => $model->id, '_return_url' => Url::to()]),
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
