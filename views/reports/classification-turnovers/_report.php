<?php

use app\helpers\ViewHelper;

/* @var \app\models\report\transactions\ClassificationTurnoversReport $searchModel */
/* @var array $data */

$formatter = Yii::$app->formatter;

?>

<table class="table table-condensed table-bordered table-hover table-highlighted app-float-thead">
    <thead>
    <tr class="va-middle">
        <th>
                <span class="app-toggle-comb pointer" data-target-prefix="report-classification-" data-display-status="0">
                    <span class="glyphicon glyphicon-chevron-down report-classification--on h"></span>
                    <span class="glyphicon glyphicon-chevron-right report-classification--off"></span>
                    <?= __('Classification') ?>
                </span>
        </th>
        <th><?= __('Account') ?></th>
        <th align="center"><?= __('Transactions') ?></th>
        <th align="center"><?= __('Inflow') ?></th>
        <th align="center"><?= __('Outflow') ?></th>
        <th align="center"><?= __('Difference') ?></th>
    </tr>
    </thead>

    <tbody>
    <?php
    $transactions_url = Url::to(['transaction/index',
        'timestamp' => $formatter->asDate($searchModel->timestamp),
        'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
    ]);
    ?>
    <tr class="app-table-totals">
        <td colspan="2"><?= __('Totals') ?>:</td>
        <td align="center">
            <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $data['transactions'] ?></span></a>
        </td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($data['inflow']) ?></td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($data['outflow']) ?></td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['difference']) ?>"><?= $formatter->asMoneyWithSymbol($data['difference']) ?></td>
    </tr>
    </tbody>

    <?php foreach ($data['classifications'] as $classification) : ?>

        <?php
        $transactions_url = Url::to(['transaction/index',
            'classification_id' => $classification['classification']->id,
            'timestamp' => $formatter->asDate($searchModel->timestamp),
            'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
        ]);
        ?>

        <tbody>
        <tr class="table-section">
            <td colspan="2">
                    <span class="app-toggle pointer" data-target-class="report-classification-<?= $classification['classification']->id ?>">
                        <span class="glyphicon glyphicon-chevron-down report-classification-<?= $classification['classification']->id ?>-on h"></span>
                        <span class="glyphicon glyphicon-chevron-right report-classification-<?= $classification['classification']->id ?>-off"></span>
                        <?= $classification['classification']->name ?>
                    </span>
            </td>
            <td align="center">
                <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $classification['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($classification['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($classification['inflow']) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($classification['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($classification['outflow']) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($classification['difference']) ?>"><?= $formatter->asMoneyWithSymbol($classification['difference']) ?></td>
        </tr>
        </tbody>

        <tbody class="report-classification-<?= $classification['classification']->id ?> h">
        <?php foreach ($classification['accounts'] as $account) : ?>

            <?php
            $transactions_url = Url::to(['transaction/index',
                'classification_id' => $classification['classification']->id,
                'account_id' => $account['account']->id,
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
            $orig_currency_code = $account['account']->currency->code;
            $orig_currency_symbol = $account['account']->currency->symbol;
            ?>

            <tr class="">
                <td>&nbsp;</td>
                <td><?= $account['account']->name ?></td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $account['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($account['inflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($account['outflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['difference']) ?>"><?= $formatter->asMoneyWithSymbol($account['difference']) ?></td>
            </tr>

        <?php endforeach ?>
        </tbody>

    <?php endforeach ?>

</table>
