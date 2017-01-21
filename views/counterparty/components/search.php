<?php

use app\models\CounterpartyCategory;
use app\widgets\form\SearchForm;

?>

<div class="counterparty-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'category_id')->dropDownList(CounterpartyCategory::find()->scroll(['empty' => true, 'without' => true])) ?>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
