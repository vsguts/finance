<?php

use app\helpers\ViewHelper;
use app\widgets\Tooltip;

/** @var \app\models\report\transactions\AccountTurnoversBaseReport $searchModel */

$formatter = Yii::$app->formatter;

?>

<table class="table table-condensed table-striped table-bordered table-hover table-highlighted app-float-thead">
    <thead>
    <tr>
        <th><?= __('Account') ?></th>
        <th><?= __('Account currency') ?></th>
        <th><?= __('Presentation currency') ?></th>
        <th align="center"><?= __('Transactions') ?></th>
        <th align="right"><?= __('Opening balance') ?></th>
        <th align="right"><?= __('Inflow') ?></th>
        <th align="right"><?= __('Outflow') ?></th>
        <th align="right"><?= __('Realized Forex') ?></th>
        <th align="right"><?= __('Closing balance') ?></th>
        <th align="right"><?= __('Difference') ?></th>
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
        <td colspan="3"><?= __('Totals') ?>:</td>
        <td align="center">
            <a href="<?= $transactions_url ?>"><span class="badge"><?= $data['totals']['transactions'] ?></span></a>
        </td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['opening_balance'], $data['currency']->symbol) ?></td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['inflow'], $data['currency']->symbol) ?></td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['outflow'], $data['currency']->symbol) ?></td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['forex'], $data['currency']->symbol) ?></td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['closing_balance'], $data['currency']->symbol) ?></td>
        <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['difference'], $data['currency']->symbol) ?></td>
    </tr>

    <?php foreach ($data['accounts'] as $row) : ?>

        <?php
        $transactions_url = Url::to(['transaction/index',
            'account_id' => $row['account']->id,
            'timestamp' => $formatter->asDate($searchModel->timestamp),
            'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
        ]);
        ?>

        <tr>
            <td>
                <a href="<?= Url::to(['account/update', 'id' => $row['account']->id]) ?>" class="app-modal" data-target-id="account_<?= $row['account']->id ?>">
                    <?= $row['account']->fullName ?>
                </a>
            </td>
            <td>
                <?= $row['account']->currency->code ?>
                <?php if ($row['account']->currency->id != $data['currency']->id) : ?>
                    <?= Tooltip::widget(['tooltip' => implode("\n", [
                        __('Opening exchange rate') . ': ' . round($data['rates']['opening'][$row['account']->currency->id], 8),
                        __('Closing exchange rate') . ': ' . round($data['rates']['closing'][$row['account']->currency->id], 8),
                    ])]) ?>
                <?php endif; ?>
            </td>
            <td>
                <?= $data['currency']->code ?>
            </td>
            <td align="center">
                <a href="<?= $transactions_url ?>"><span class="badge"><?= $row['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['opening_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['opening_balance'], $data['currency']->symbol) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['inflow'], $data['currency']->symbol) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['outflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['outflow'], $data['currency']->symbol) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['forex']) ?>"><?= $formatter->asMoneyWithSymbol($row['forex'], $data['currency']->symbol) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['closing_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['closing_balance'], $data['currency']->symbol) ?></td>
            <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['difference']) ?>"><?= $formatter->asMoneyWithSymbol($row['difference'], $data['currency']->symbol) ?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>

</table>