<?php

use app\widgets\form\SearchForm;

?>

<div class="user-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'name') ?>

            <?= $form->field($model, 'email') ?>

            <?= $form->field($model, 'status')->dropDownList($model->getLookupItems('status', ['empty' => true])) ?>

        </div>
        <div class="col-md-6">

            <?= $form->field($model, 'has_role')->dropDownList($model->getBoolItems()) ?>

            <?= $form->field($model, 'permission')->dropDownList($permissions, ['prompt' => '--']) ?>

        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
