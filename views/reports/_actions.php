<?php

use app\widgets\ActionsDropdown;
use app\helpers\Url;

?>

<div class="pull-right buttons-container">
    <?= ActionsDropdown::widget([
        'layout' => 'info',
        'items' => [
            [
                'label' => __('Export'),
                'url' => Url::to(array_merge(['export'], Yii::$app->request->queryParams)),
                'linkOptions' => [
                    'class' => 'app-modal app-modal-force',
                    'data-target-id' => 'export',
                ],
            ],
        ],
    ]) ?>
</div>
