<?php

/* @var \app\models\report\transactions\AbstractTransactionsReport $model */

use app\widgets\form\DatePickerRange;
use app\widgets\form\SearchForm;
use app\widgets\PeriodLinks;

?>

<div class="report-search">

    <?php $form = SearchForm::begin([
        'action' => ['view'],
        'targetClass' => 'report-search-form'
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'timestamp')->widget(DatePickerRange::class) ?>
        </div>
        <div class="col-md-6">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= PeriodLinks::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
