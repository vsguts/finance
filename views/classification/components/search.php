<?php

use app\widgets\form\SearchForm;

?>

<div class="classification-search">

    <?php $form = SearchForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList($model->getLookupItems('type', ['empty' => true])) ?>
        </div>
    </div>

    <?php SearchForm::end(); ?>

</div>
