<?php

use app\models\Account;
use app\models\Currency;
use app\models\Classification;
use app\models\Counterparty;
use app\widgets\SearchForm;

?>

<div class="transaction-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'account_id')->dropDownList(Account::find()->active($model->account_id)->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
            <?= $form->field($model, 'currency_id')->dropDownList(Currency::find()->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
            <?= $form->field($model, 'counterparty_id')->dropDownList(Counterparty::find()->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
            <?= $form->field($model, 'classification_id')->dropDownList(Classification::find()->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'timestamp')->widget('app\widgets\DatePickerRange') ?>
            <?= $form->field($model, 'description') ?>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
