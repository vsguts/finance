<?php

use app\helpers\ViewHelper;
use app\models\search\TransactionReportSearch;

/* @var TransactionReportSearch $searchModel */
/* @var array $data */

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
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['opening_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['opening_balance'], $row['account']->currency_id) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['inflow'], $row['account']->currency_id) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['outflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['outflow'], $row['account']->currency_id) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['closing_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['closing_balance'], $row['account']->currency_id) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['difference']) ?>"><?= $formatter->asMoneyWithSymbol($row['difference'], $row['account']->currency_id) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <?php
            $transactions_url = Url::to(['index',
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
        ?>
        <tr class="info">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="center">
                <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= array_sum($data['totals']['transactions']) ?></span></a>
            </td>
            <td align="right" class="nowrap"><?= ViewHelper::getCurrencyValues($data['totals']['opening_balance']) ?></td>
            <td align="right" class="nowrap"><?= ViewHelper::getCurrencyValues($data['totals']['inflow']) ?></td>
            <td align="right" class="nowrap"><?= ViewHelper::getCurrencyValues($data['totals']['outflow']) ?></td>
            <td align="right" class="nowrap"><?= ViewHelper::getCurrencyValues($data['totals']['closing_balance']) ?></td>
            <td align="right" class="nowrap"><?= ViewHelper::getCurrencyValues($data['totals']['difference']) ?></td>
        </tr>
    </tfoot>

</table>