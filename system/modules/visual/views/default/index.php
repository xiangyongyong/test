<?php

// 引入静态包
$bundle = \system\modules\visual\assets\VisualAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
    <title><?= Yii::$app->systemConfig->getValue('SYSTEM_NAME', '运维系统')?></title>
    <?php $this->head() ?>
    <script type="text/javascript">
        var gatewayData = JSON.parse('<?= \yii\helpers\Json::encode($data)?>');
        var getAllUrl = '<?= \yii\helpers\Url::toRoute(['index'])?>';
        var getUrl = '<?= \yii\helpers\Url::toRoute(['get'])?>';
        var postUrl = '<?= \yii\helpers\Url::toRoute(['post'])?>';
        var staticBasePath = '<?= $bundle->baseUrl;?>'; // 静态资源路径
        function log() {
            <?php if (Yii::$app->systemConfig->getValue('SYSTEM_JS_LOG')):?>
            for (var i in arguments){
                console.log(arguments[i]);
            }
            <?php endif; ?>
        }
    </script>
</head>
<body>
<?php $this->beginBody() ?>

<div id="container"></div>

<div class="header">
    <div class="header-l">
        <div class="weather">
            <div class="temperature">
                <i class="icon_tq"></i><span>22°</span>
            </div>
            <div class="date">
                4.12  星期三   <span>10:30</span>
            </div>
        </div>
    </div>
    <div class="title"></div>
    <div class="header-r">
        <ul>
            <li><a href="##">网关状态</a></li>
            <li><a href="##">工单状态</a></li>
            <li><a href="##">网关状态</a></li>
        </ul>
    </div>
</div>

<div class="chart-box-l" id="chart_box_l" style="display: none;">
    <div class="chart-content">
        <p class="title">网关分布图@TODO 返回功能</p>
        <div class="chart-box" id="gatewayGroup" style="height: 350px;">
        </div>
        <p class="title">工单状态@TODO 日月年切换</p>
        <div class="chart-box" id="workOrderState" style="height: 350px;">
        </div>
    </div>
    <div class="switch"></div>
</div>
<div class="chart-box-r" id="chart_box_r" style="display: none;">
    <p class="title">网关状态</p>
    <div class="chart" id="gatewayState" style="height: 350px;">
    </div>
</div>

<div class="chart-box-l gateway" id="chart_details_l" style="display: none;">
    <div class="chart-content chart-pd">
        <div class="search-box">
            <input type="text" placeholder="输入路名/设备号"/>
            <button></button>
        </div>

        <div class="status-detail" id="gatewayInfo">
            <!--<h3>武珞路288号</h3>
            <div class="position">
                <span>武汉市武昌区武珞路288号</span>
                <button>位置校正</button>
            </div>
            <p class="num">电杆: 1180</p>
            <button class="status-btn">正在检修</button>-->
        </div>
        <div class="device-list">
            <p>设备列表</p>
            <ul class="device-item" id="devices">
                <!--<li>网关</li>
                <li>类型</li>
                <li>状态</li>
                <li>2</li>
                <li><i class="camera"></i>摄像头</li>
                <li>已绑定<button class="btn1">更改</button></li>
                <li>5</li>
                <li><i class="camera"></i>摄像头</li>
                <li>已绑定<button class="btn2">更改</button></li>-->
            </ul>
        </div>
        <div class="chart4-box" id="gatewayPortChart">
        </div>
        <!--<div class="chart5-box"></div>-->
    </div>
    <div class="switch"></div>
    <div class="close">×</div>
</div>
<div class="chart-box-r" id="chart_details_r" style="display: none;">
    <p class="title">环境数据</p>
    <div class="chart" id="gatewayEnvChart" style="height: 300px;">
    </div>
</div>

<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/echarts/echarts.common.min.js"></script>
<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/echarts/dark.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>