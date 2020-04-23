<?php

use app\helpers\ViewHelper;

/* @var \app\models\report\transactions\CounterpartyTurnoversReport $searchModel */
/* @var array $data */

$formatter = Yii::$app->formatter;

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
    $transactions_url = Url::to(['transaction/index',
        'timestamp' => $formatter->asDate($searchModel->timestamp),
        'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
        'account_id' => $searchModel->account_id,
    ]);
    ?>
    <tr class="app-table-totals">
        <td colspan="2"><?= __('Totals') ?>:</td>
        <td align="center">
            <a href="<?= $transactions_url ?>"><span class="badge"><?= $data['transactions'] ?></span></a>
        </td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($data['inflow']) ?></td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($data['outflow']) ?></td>
        <td align="right" class="nowrap <?= ViewHelper::getTextClass($data['difference']) ?>"><?= $formatter->asMoneyWithSymbol($data['difference']) ?></td>
    </tr>
    </tbody>

    <?php foreach ($data['counterparties'] as $counterparty) : ?>

        <?php
        $transactions_url = Url::to(['transaction/index',
            'counterparty_id' => $counterparty['counterparty']->id,
            'timestamp' => $formatter->asDate($searchModel->timestamp),
            'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            'account_id' => $searchModel->account_id,
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
                <a href="<?= $transactions_url ?>"><span class="badge"><?= $counterparty['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($counterparty['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($counterparty['inflow']) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($counterparty['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($counterparty['outflow']) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($counterparty['difference']) ?>"><?= $formatter->asMoneyWithSymbol($counterparty['difference']) ?></td>
        </tr>
        </tbody>

        <tbody class="report-counterparty-<?= $counterparty['counterparty']->id ?> h">
        <?php foreach ($counterparty['accounts'] as $account) : ?>

            <?php
            $transactions_url = Url::to(['transaction/index',
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
                    <a href="<?= $transactions_url ?>"><span class="badge"><?= $account['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($account['inflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($account['outflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($account['difference']) ?>"><?= $formatter->asMoneyWithSymbol($account['difference']) ?></td>
            </tr>

        <?php endforeach ?>
        </tbody>

    <?php endforeach ?>

</table>
