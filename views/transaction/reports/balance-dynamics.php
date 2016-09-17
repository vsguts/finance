<?= $this->render('/common/reports/chart', [
    'chart_data' => $data['chart'],
    'title' => $data['currency']->name,
]) ?>
