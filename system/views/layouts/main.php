<?php
use yii\helpers\Html;

//echo Yii::$app->request->getScriptUrl();exit;
//$static = dirname(Yii::$app->request->getScriptUrl());
//\yii\bootstrap\BootstrapAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!--<link rel="stylesheet" href="<?/*= WEB*/?>/theme/default/css/bootstrap-grid.css?<?/*= VERSION*/?>" media="all">-->
    <link rel="stylesheet" href="<?= WEB?>/theme/default/lib/layui/css/layui.css?<?= VERSION?>" media="all" />
    <link rel="stylesheet" href="<?= WEB?>/theme/default/css/global.css?<?= VERSION?>" media="all">
    <link rel="stylesheet" href="<?= WEB?>/theme/default/css/style.css?<?= VERSION?>" media="all">
    <link rel="stylesheet" href="<?= WEB?>/theme/default/css/custom.css?<?= VERSION?>" media="all">
    <link rel="stylesheet" href="<?= WEB?>/theme/default/lib/font-awesome/css/font-awesome.min.css?<?= VERSION?>">

    <script type="text/javascript">
        // 闪屏消息
        var flashMsgs = <?= \system\widgets\FlashMsg::widget()?>;
        // 全局默认的删除url
        var deleteUrl = '<?= \yii\helpers\Url::toRoute(['delete', 'id'=>''])?>';
    </script>
    <script type="text/javascript" src="<?= WEB?>/theme/default/lib/layui/layui.js?<?= VERSION?>"></script>
    <script type="text/javascript" src="<?= WEB?>/theme/default/lib/echarts/echarts.common.min.js?<?= VERSION?>"></script>
    <script type="text/javascript" src="<?= WEB?>/theme/default/js/global.js?<?= VERSION?>"></script>
</head>

<body class="gray-bg">
    <?php $this->beginBody() ?>
    <div class="admin-main ">
        <?= $content?>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>