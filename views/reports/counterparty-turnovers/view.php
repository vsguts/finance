<?php

/* @var \app\models\report\transactions\AbstractTransactionsReport $searchModel */
/* @var array $data */

use yii\bootstrap\Html;

$this->title = __('Reports');
$this->params['breadcrumbs'][] = ['label' => __('Transactions'), 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="directSales-report">

    <?= $this->render('../_actions') ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('../_search', ['model' => $searchModel]) ?>

    <?= $this->render('../_links', ['searchModel' => $searchModel]) ?>

    <?= $this->render('_report', [
        'data' => $data,
        'searchModel' => $searchModel,
    ]) ?>

</div>
