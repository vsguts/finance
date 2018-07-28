<?php

use app\models\Account;
use app\models\Classification;
use app\models\Counterparty;
use app\models\Currency;
use app\models\User;
use app\widgets\form\DatePickerRange;
use app\widgets\form\SearchForm;

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
            <?= $form->field($model, 'classification_id')->dropDownList(Classification::find()->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
            <?= $form->field($model, 'counterparty_id')->dropDownList(Counterparty::find()->scroll(), [
                'class' => 'form-control app-select2',
                'multiple' => true,
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'timestamp')->widget(DatePickerRange::class) ?>
            <?= $form->field($model, 'description') ?>
            <?= $form->field($model, 'user_id')->dropDownList(User::find()->scroll(['empty' => true]), [
                'class' => 'form-control app-select2',
            ]) ?>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
