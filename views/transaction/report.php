<?php

use yii\bootstrap\Tabs;
use app\widgets\ActionsDropdown;

$this->title = __('Reports');
$this->params['breadcrumbs'][] = ['label' => __('Bank transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="transaction-index">

    <div class="pull-right buttons-container">
    <?php
        if ($report->exportGetColumns()) {
            echo ActionsDropdown::widget([
                'items' => [
                    [
                        'label' => __('Export'),
                        'url' => Url::to([
                            '/export/export/',
                            'object' => 'transaction-report',
                            'attributes' => [
                                'report' => $report_id,
                                'timestamp' => $searchModel->timestamp,
                                'timestamp_to' => $searchModel->timestamp_to,
                            ],
                        ]),
                        'linkOptions' => [
                            'class' => 'app-modal',
                            'data-target-id' => 'export',
                        ],
                    ],
                ],
            ]);
        }
    ?>
    </div>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/common/reports/search', ['model' => $searchModel]) ?>

    <?php

        $items = [];

        foreach ($reports as $id => $report_label) {
            $item = [
                'label' => $report_label,
            ];

            if ($id == $report_id) {
                $item['content'] = $this->render('reports/' . $report_id, [
                    'data' => $data,
                    'searchModel' => $searchModel,
                ]);
                $item['active'] = true;
            } else {
                $item['url'] = Url::to(['transaction/report',
                    'report' => $id,
                    'timestamp' => $searchModel->timestamp,
                    'timestamp_to' => $searchModel->timestamp_to,
                ]);
            }

            $items[] = $item;
        }

        echo Tabs::widget([
            'items' => $items,
        ]);

    ?>

</div>
