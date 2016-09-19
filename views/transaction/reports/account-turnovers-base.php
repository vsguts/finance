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
            $transactions_url = Url::to(['index',
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
        ?>

        <tr class="app-table-totals">
            <td colspan="3"><?= __('Totals') ?>:</td>
            <td align="center">
                <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $data['totals']['transactions'] ?></span></a>
            </td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['opening_balance']) ?> <?= $data['currency']->symbol ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['inflow']) ?> <?= $data['currency']->symbol ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['outflow']) ?> <?= $data['currency']->symbol ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['forex']) ?> <?= $data['currency']->symbol ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['closing_balance']) ?> <?= $data['currency']->symbol ?></td>
            <td align="right" class="nowrap"><?= $formatter->asMoney($data['totals']['difference']) ?> <?= $data['currency']->symbol ?></td>
        </tr>

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
                    <?= $data['currency']->code ?>
                </td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $row['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= getTextClass($row['opening_balance']) ?>"><?= $formatter->asMoney($row['opening_balance']) ?> <?= $data['currency']->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['inflow']) ?>"><?= $formatter->asMoney($row['inflow']) ?> <?= $data['currency']->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['outflow']) ?>"><?= $formatter->asMoney($row['outflow']) ?> <?= $data['currency']->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['forex']) ?>"><?= $formatter->asMoney($row['forex']) ?> <?= $data['currency']->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['closing_balance']) ?>"><?= $formatter->asMoney($row['closing_balance']) ?> <?= $data['currency']->symbol ?></td>
                <td align="right" class="nowrap <?= getTextClass($row['difference']) ?>"><?= $formatter->asMoney($row['difference']) ?> <?= $data['currency']->symbol ?></td>
            </tr>

        <?php endforeach; ?>
    </tbody>

</table>