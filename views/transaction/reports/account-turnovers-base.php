<?php

use app\helpers\ViewHelper;
use app\models\search\TransactionReportSearch;
use app\widgets\Tooltip;

/* @var TransactionReportSearch $searchModel */
/* @var array $data */

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
        <?php foreach ($data['accounts'] as $row) : ?>
            <?php
                $transactions_url = Url::to(['index',
                    'account_id' => $row['account']->id,
                    'timestamp' => $formatter->asDate($searchModel->timestamp),
                    'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
                ]);
            ?>
            <tr>
                <td><?= $row['account']->fullName ?></td>
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
                    <?= Yii::$app->currency->getBaseCurrencyCode() ?>
                </td>
                <td align="center">
                    <a href="<?= $transactions_url ?>"><span class="badge"><?= $row['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['opening_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['opening_balance']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['inflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['outflow']) ?>"><?= $formatter->asMoneyWithSymbol($row['outflow']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['forex']) ?>"><?= $formatter->asMoneyWithSymbol($row['forex']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['closing_balance']) ?>"><?= $formatter->asMoneyWithSymbol($row['closing_balance']) ?></td>
                <td align="right" class="nowrap <?= ViewHelper::getTextClass($row['difference']) ?>"><?= $formatter->asMoneyWithSymbol($row['difference']) ?></td>
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
            <td>&nbsp;</td>
            <td align="center">
                <a href="<?= $transactions_url ?>"><span class="badge"><?= $data['totals']['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['opening_balance']) ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['inflow']) ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['outflow']) ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['forex']) ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['closing_balance']) ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoneyWithSymbol($data['totals']['difference']) ?></td>
        </tr>
    </tfoot>

</table>