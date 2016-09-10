<?php

use app\widgets\SearchForm;

?>

<div class="counterparty-category-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'name') ?>

        </div>
        <div class="col-md-6">

        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
