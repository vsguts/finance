<?php

use app\widgets\ActionsDropdown;
use app\widgets\grid\GridView;

$this->title = __('Accounts');
$this->params['breadcrumbs'][] = $this->title;

$detailsLink = function($model) {
    return [
        'label' => __('Edit'),
        'class' => 'app-modal',
        'href' => Url::to(['/account/update', 'id' => $model->id, '_return_url' => Url::to()]),
        'data-target-id' => 'account_' . $model->id,
    ];
};

?>
<div class="account-index">

    <?php if (Yii::$app->user->can('account_manage')) : ?>

    <div class="pull-right buttons-container">
        <div class="btn-group">
            <?= Html::a(__('Create bank account'), ['update', '_return_url' => Url::to()], [
                'class' => 'btn btn-success app-modal',
                'data-target-id' => 'account_create',
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
                'attribute' => 'currency',
                'value' => 'currency.code',
                'label' => __('Currency'),
            ],
            'bank',
            'account_number',

            [
                'attribute' => 'import_processor',
                'value' => function($model, $key, $index, $column){
                    if ($model->import_processor) {
                        return $model->getLookupItem('import_processor', $model->import_processor);
                    }
                },
            ],

            [
                'class' => 'app\widgets\grid\CounterColumn',
                'label' => __('Transactions'),
                'modelClass' => 'app\models\Transaction',
                'modelField' => 'account_id'
            ],

            [
                'class' => 'app\widgets\grid\ActionColumn',
                'size' => 'xs',
                'items' => [
                    $detailsLink,
                    function($model) {
                        if (Yii::$app->user->can('transaction_manage')) {
                            return [
                                'label' => __('Recalculate transaction balances'),
                                'href' => Url::to(['transaction/recalculate-balance', 'account_id' => $model->id, '_return_url' => Url::to()]),
                                'data-method' => 'post',
                                'data-confirm' => __('Are you sure you want to recalculate balances of bank transactions?'),
                            ];
                        }
                    },
                    function($model) {
                        if (Yii::$app->user->can('account_manage')) {
                            return [
                                'label' => __('Delete'),
                                'href' => Url::to(['account/delete', 'id' => $model->id, '_return_url' => Url::to()]),
                                'data-method' => 'post',
                                'data-confirm' => __('Are you sure you want to delete this item?'),
                            ];
                        }
                    },
                ],
            ],

        ],
    ]) ?>
    
</div>
