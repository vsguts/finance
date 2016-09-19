<?php

use app\widgets\ActiveForm;
use app\widgets\Tooltip;

$this->render('components/functions');

$formatter = Yii::$app->formatter;

?>

<table class="table table-condensed table-striped table-bordered table-hover table-highlighted app-float-thead">
    <thead>
        <tr>
            <th><?= __('Account') ?></th>
            <th><?= __('Currency') ?></th>
            <th align="center"><?= __('Transactions') ?></th>
            <th align="right"><?= __('Opening balance') ?></th>
            <th align="right"><?= __('Inflow') ?></th>
            <th align="right"><?= __('Outflow') ?></th>
            <th align="right"><?= __('Closing balance') ?></th>
            <th align="right"><?= __('Difference') ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($data['accounts'] as $row) : ?>

            <?php
                $transactions_url = Url::to(['index',
                    'account_id' => $row['account']->id,
                    'timestamp' => $formatter->asDate($searchModel->timestamp),
                    'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
                ]);
            ?>

            <tr>
                <td><?= $row['account']->name ?></td>
                <td><?= $row['account']->currency->code ?></td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $row['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= getTextClass($row['opening_balance']) ?>"><?= $formatter->asMoney($row['opening_balance']) ?> <?= $row['account']->currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['inflow']) ?>"><?= $formatter->asMoney($row['inflow']) ?> <?= $row['account']->currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['outflow']) ?>"><?= $formatter->asMoney($row['outflow']) ?> <?= $row['account']->currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['closing_balance']) ?>"><?= $formatter->asMoney($row['closing_balance']) ?> <?= $row['account']->currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['difference']) ?>"><?= $formatter->asMoney($row['difference']) ?> <?= $row['account']->currency->symbol ?></td>
            </tr>

        <?php endforeach; ?>
    </tbody>

</table>