<?php

use app\widgets\grid\GridView;
use app\widgets\ActionsDropdown;

$this->title = __('Classification');
$this->params['breadcrumbs'][] = $this->title;

$detailsLink = function($model) {
    return [
        'label' => __('Edit'),
        'class' => 'app-modal',
        'href' => Url::to(['/classification/update', 'id' => $model->id, '_return_url' => Url::to()]),
        'data-target-id' => 'classification_' . $model->id,
    ];
};

?>
<div class="classification-index">

    <?php if (Yii::$app->user->can('classification_manage')) : ?>

    <div class="pull-right buttons-container">
        <div class="btn-group">
            <?= Html::a(__('Create classification'), ['update', '_return_url' => Url::to()], [
                'class' => 'btn btn-success app-modal',
                'data-target-id' => 'classification_create',
            ]) ?>
        </div>
        <?= ActionsDropdown::widget([
            'layout' => 'info',
            'items' => [
                ['label' => __('Delete'), 'url' => Url::to(['delete']), 'linkOptions' => [
                    'data-app-process-items' => 'id',
                    'data-confirm' => __('Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                ]],
            ],
        ]) ?>
    </div>

    <?php endif; ?>

    <ul class="nav nav-pills app-tool-links">
        <li role="presentation"><a href="<?= Url::to(['classification-category/index']) ?>"><?= __('Classification categories') ?></a></li>
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
                'attribute' => 'category',
                'label' => __('Category'),
                'value' => 'category.name',
                'link' => function($model) {
                    if ($model->category) {
                        return [
                            'class' => 'app-modal',
                            'href' => Url::to(['/classification-category/update', 'id' => $model->category->id, '_return_url' => Url::to()]),
                            'data-target-id' => 'classification-category_' . $model->category->id,
                        ];
                    }
                },
            ],

            [
                'attribute' => 'type',
                'value' => function($model, $key, $index, $column){
                    if ($model->type) {
                        return $model->getLookupItem('type', $model->type);
                    }
                },
            ],

            [
                'class' => 'app\widgets\grid\CounterColumn',
                'label' => __('Transactions'),
                'modelClass' => 'app\models\Transaction',
                'modelField' => 'classification_id',
            ],

            [
                'class' => 'app\widgets\grid\ActionColumn',
                'size' => 'xs',
                'items' => [
                    $detailsLink,
                    function($model) {
                        if (Yii::$app->user->can('classification_manage')) {
                            return [
                                'label' => __('Delete'),
                                'href' => Url::to(['classification/delete', 'id' => $model->id, '_return_url' => Url::to()]),
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
