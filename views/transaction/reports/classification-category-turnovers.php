<?php

$this->render('components/functions');

$formatter = Yii::$app->formatter;
$base_currency = Yii::$app->currency->getBaseCurrency();

?>

<table class="table table-condensed table-bordered table-hover table-highlighted app-float-thead">
    <thead>
        <tr class="va-middle">
            <th>
                <span class="app-toggle-comb pointer" data-target-prefix="report-category-" data-display-status="0">
                    <span class="glyphicon glyphicon-chevron-down report-category--on h"></span>
                    <span class="glyphicon glyphicon-chevron-right report-category--off"></span>
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
            <td align="right" class="nowrap <?= getTextClass($data['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($data['inflow'], $data['currency_id']) ?></td>
            <td align="right" class="nowrap <?= getTextClass($data['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($data['outflow'], $data['currency_id']) ?></td>
            <td align="right" class="nowrap <?= getTextClass($data['difference']) ?>"><?= $formatter->asMoneyWithSymbol($data['difference'], $data['currency_id']) ?></td>
        </tr>
    </tbody>

    <?php foreach ($data['categories'] as $category) : ?>

        <?php
            $category_id = $category['category'] ? $category['category']->id : 0;
            $transactions_url = Url::to(['index',
                'classification_category_id' => $category_id,
                'timestamp' => $formatter->asDate($searchModel->timestamp),
                'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
            ]);
        ?>

        <tbody>
            <tr class="table-section">
                <td colspan="2">
                    <span class="app-toggle app-toggle-save app-toggle-save-inverse pointer" data-target-class="report-category-<?= $category_id ?>">
                        <span class="glyphicon glyphicon-chevron-down report-category-<?= $category_id ?>-on h"></span>
                        <span class="glyphicon glyphicon-chevron-right report-category-<?= $category_id ?>-off"></span>
                        <?= $category['category'] ? $category['category']->name : '' ?>
                    </span>
                </td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $category['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= getTextClass($category['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($category['inflow'], $data['currency_id']) ?></td>
                <td align="right" class="nowrap <?= getTextClass($category['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($category['outflow'], $data['currency_id']) ?></td>
                <td align="right" class="nowrap <?= getTextClass($category['difference']) ?>"><?= $formatter->asMoneyWithSymbol($category['difference'], $data['currency_id']) ?></td>
            </tr>
        </tbody>

        <tbody class="report-category-<?= $category_id ?> h">
        <?php foreach ($category['classifications'] as $classification) : ?>

            <?php
                $transactions_url = Url::to(['index',
                    'classification_id' => $classification['classification']->id,
                    'timestamp' => $formatter->asDate($searchModel->timestamp),
                    'timestamp_to' => $formatter->asDate($searchModel->timestamp_to),
                ]);
            ?>

            <tr class="">
                <td>&nbsp;</td>
                <td><?= $classification['classification']->name ?></td>
                <td align="center">
                    <a href="<?= $transactions_url ?>" target="_blank"><span class="badge"><?= $classification['transactions'] ?></span></a>
                </td>
                <td align="right" class="nowrap <?= getTextClass($classification['inflow']) ?>"><?= $formatter->asMoneyWithSymbol($classification['inflow'], $data['currency_id']) ?></td>
                <td align="right" class="nowrap <?= getTextClass($classification['outflow'], true) ?>"><?= $formatter->asMoneyWithSymbol($classification['outflow'], $data['currency_id']) ?></td>
                <td align="right" class="nowrap <?= getTextClass($classification['difference']) ?>"><?= $formatter->asMoneyWithSymbol($classification['difference'], $data['currency_id']) ?></td>
            </tr>

        <?php endforeach ?>
        </tbody>

    <?php endforeach ?>

</table>
