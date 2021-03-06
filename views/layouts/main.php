<?php
use app\assets\AppAsset;

AppAsset::register($this);
$this->registerJs(AppAsset::appLangs());

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - <?= Yii::$app->params['applicationName'] ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

    <div class="app-ajax-overlay"></div>
    <div class="app-ajax-spinner"></div>

    <div class="wrap">
        <?= $this->render('components/navbar') ?>
        <div class="container-fluid">
        <?php
            $params = [
                'breadcrumbs' => $this->render('components/breadcrumbs'),
                'alerts' => $this->render('components/alerts'),
                'content' => $content,
            ];
            if (!empty($this->blocks['sidebox'])) {
                echo $this->render('layouts/sidebox', $params);
            } else {
                echo $this->render('layouts/simple', $params);
            }
        ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <p class="pull-left">&copy; <?= Yii::$app->params['companyName'] ?> <?= date('Y') ?></p>
            <p class="pull-right"><?= __('Made by Vladimir Guts') ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
