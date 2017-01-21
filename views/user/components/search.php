<?php

use app\widgets\form\SearchForm;

?>

<div class="user-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'name') ?>

            <?= $form->field($model, 'email') ?>
        
        </div>
        <div class="col-md-6">

            <?= $form->field($model, 'status')->dropDownList($model->getLookupItems('status', ['empty' => true])) ?>

        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
