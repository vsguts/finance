<?php

use app\widgets\form\ActiveForm;
use app\widgets\Tooltip;

$this->render('components/functions');

$formatter = Yii::$app->formatter;
$base_currency = Yii::$app->currency->getBaseCurrency();

?>

<table class="table table-condensed table-bordered table-hover table-highlighted app-float-thead">
    <thead>
        <tr class="va-middle">
            <th>
                <span class="app-toggle-comb pointer" data-target-prefix="report-counterparty-" data-display-status="0">
                    <span class="glyphicon glyphicon-chevron-down report-counterparty--on h"></span>
                    <span class="glyphicon glyphicon-chevron-right report-counterparty--off"></span>
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
            $transactions_url = Url::to(['index',
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
        ?>
        <tr class="app-table-totals">
            <td colspan="2"><?= __('Totals') ?>:</td>
            <td align="center">
                <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $data['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap <?= getTextClass($data['inflow']) ?>"><?= $formatter->asMoney($data['inflow']) ?> <?= $base_currency->symbol ?></td>
            <td align="right" class="nowrap <?= getTextClass($data['outflow'], true) ?>"><?= $formatter->asMoney($data['outflow']) ?> <?= $base_currency->symbol ?></td>
            <td align="right" class="nowrap <?= getTextClass($data['difference']) ?>"><?= $formatter->asMoney($data['difference']) ?> <?= $base_currency->symbol ?></td>
        </tr>
    </tbody>

    <?php foreach ($data['counterparties'] as $counterparty) : ?>

        <?php
            $transactions_url = Url::to(['index',
                'counterparty_id' => $counterparty['counterparty']->id,
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
        ?>

        <tbody>
            <tr class="table-section">
                <td colspan="2">
                    <span class="app-toggle pointer" data-target-class="report-counterparty-<?= $counterparty['counterparty']->id ?>">
                        <span class="glyphicon glyphicon-chevron-down report-counterparty-<?= $counterparty['counterparty']->id ?>-on h"></span>
                        <span class="glyphicon glyphicon-chevron-right report-counterparty-<?= $counterparty['counterparty']->id ?>-off"></span>
                        <?= $counterparty['counterparty']->name ?>
                    </span>
                </td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $counterparty['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= getTextClass($counterparty['inflow']) ?>"><?= $formatter->asMoney($counterparty['inflow']) ?> <?= $base_currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($counterparty['outflow'], true) ?>"><?= $formatter->asMoney($counterparty['outflow']) ?> <?= $base_currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($counterparty['difference']) ?>"><?= $formatter->asMoney($counterparty['difference']) ?> <?= $base_currency->symbol ?></td>
            </tr>
        </tbody>

        <tbody class="report-counterparty-<?= $counterparty['counterparty']->id ?> h">
        <?php foreach ($counterparty['accounts'] as $account) : ?>

            <?php
                $transactions_url = Url::to(['index',
                    'counterparty_id' => $counterparty['counterparty']->id,
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
                <td align="right" class="nowrap <?= getTextClass($account['inflow']) ?>"><?= $formatter->asMoney($account['inflow']) ?> <?= $base_currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($account['outflow'], true) ?>"><?= $formatter->asMoney($account['outflow']) ?> <?= $base_currency->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($account['difference']) ?>"><?= $formatter->asMoney($account['difference']) ?> <?= $base_currency->symbol ?></td>
            </tr>

        <?php endforeach ?>
        </tbody>

    <?php endforeach ?>

</table>
