<?php

use app\models\Currency;
use app\widgets\SearchForm;

?>

<div class="account-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'currency_id')->dropDownList(Currency::find()->scroll(['empty' => true])) ?>
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'bank') ?>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
