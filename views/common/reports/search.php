<?php

use app\widgets\PeriodLinks;
use app\widgets\form\SearchForm;

?>

<div class="state-search">

    <?php $form = SearchForm::begin(['action' => ['report']]); ?>

    <?= Html::activeHiddenInput($model, 'report') ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'timestamp')->widget('app\widgets\form\DatePickerRange') ?>
        </div>
        <div class="col-md-6">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= PeriodLinks::widget() ?>
            </div>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
