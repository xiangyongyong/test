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
    <link rel="stylesheet" href="<?= WEB?>/theme/default/lib/layui-new/css/layui.css?<?= VERSION?>" media="all" />
    <link rel="stylesheet" href="<?= WEB?>/theme/default/css/iconfont.css?<?= VERSION?>">
    <style>
        .workOrder{ min-width: 1040px; height: 90%; background: #26292E; z-index: 160; position: absolute; border: #383E45 1px solid; padding: 36px 19px; overflow: auto; }
        .workOrder .order-statistics{ overflow: hidden; margin-bottom: 20px; width: 1040px; }
        .workOrder .order-statistics .orderInfo{ width: 240px; height: 120px; background: #36404A!important; float: left; margin: 0 10px; }
        .workOrder .order-statistics .orderInfo h5{ font-size: 14px; color: #80868C; margin: 17px; }
        .workOrder .order-statistics .orderInfo p{ font-size: 24px; margin: 9px 18px;  }
        .workOrder .order-statistics a .orderInfo:nth-of-type(1) p{ color: #FB6D7E; }
        .workOrder .order-statistics a .orderInfo:nth-of-type(2) p{ color: #FFBD4A; }
        .workOrder .order-statistics a .orderInfo:nth-of-type(3) p{ color: #5D9CEC; }
        .workOrder .orderList{ width: 1020px; height: 750px; background: #36404A; margin-left: 10px; position: relative; }
        .workOrder .orderList h5{ padding: 20px; color: #92979D; }
        .workOrder .orderList .orderReload{ display: block; width: 20px; height: 20px; position: absolute; right: 20px; top: 20px; }
        .workOrder .orderList .icon-shuaxin:before{ color:#99A7AE; }
        .workOrder .orderList .layui-form{ padding: 0 20px; }
        .workOrder .orderList .layui-table{ margin: 0; background: #36404A; border-left: none; border-right: none; border-color: #414D59; }
        .workOrder .orderList .layui-table th{ border-bottom:1px solid #414d59; color: #C0C3C6; background: #36404A; }
        .workOrder .orderList .layui-table td{ border-bottom:1px solid #414d59; color: #C0C3C6; }
        .workOrder .orderList .layui-table tr:hover td{ color: #58859E; }
        .workOrder .orderList .layui-table .operation .layui-icon{ color: #98A6AD; }

        ul.pagination li a, ul.pagination li span {width: 31px; height: 34px; display: block; background: #414D59; border-radius: 5px; text-align: center; line-height: 34px; color: #98A9AF;  margin: 0 2px;  }
        ul.pagination li { float: left;
        }
        ul.pagination li.active a {
            width: 31px;
            height: 34px;
            display: block;
            background: #61A5F8;
            border-radius: 5px;
            text-align: center;
            line-height: 34px;
            color: #fff;
            margin: 0 2px;
        }
        .pagination{    margin-top: 20px;}
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<!--工单start-->
<div class="workOrder">
    <div class="order-statistics">
        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 0])?>">
        <div class="orderInfo">
            <h5>待办工单</h5>
            <p style="color: #FB6D7E;"><?=$suspendingCounts?></p>
        </div>
        </a>
        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 1])?>">
        <div class="orderInfo">
            <h5>本周处理中工单</h5>
            <p style="color: #FFBD4A;"><?=$handlingCounts?></p>
        </div>
        </a>
        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 2])?>">
        <div class="orderInfo">
            <h5>本周处理工单</h5>
            <p style="color: #5D9CEC;"><?=$finishCounts?></p>
        </div>
        </a>
        <div class="orderInfo" id="main"></div>
    </div>
    <div class="orderList">
        <div class="project-list">
            <h5>我的工单</h5>
            <a class="orderReload iconfont icon-shuaxin" href="javascript:;"></a>
            <form class="layui-form"  lay-data="{}" lay-filter="workerList" action="">
                <table class="layui-table" lay-skin="line" lay-filter="test">
                    <colgroup>
                        <col width="50">
                        <col width="100">
                        <col width="100">
                        <col width="100">
                        <col width="100">
                        <col width="50">
                    </colgroup>
                    <thead>
                    <tr>
                        <th lay-data="{checkbox:true, LAY_CHECKED: true}"><input type="checkbox" name="" lay-skin="primary"></th>
                        <th lay-data="{field:'experience', width:80, edit:'text'}">工单编号</th>
                        <th lay-data="{field:'sign'}">处理状态</th>
                        <th lay-data="{field:'sign'}">负责人</th>
                        <th lay-data="{field:'sign'}">更新时间</th>
                        <th lay-data="{fixed: 'right', align:'center', toolbar: '#barDemo'}">处理</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $item): ?>
                    <tr>
                        <td><input type="checkbox" name="" lay-skin="primary"></td>
                        <td><?=$item['order_id']?></td>
                        <td>
                            <?php if($item['state'] == 0): ?>
                                <span style="color:#FB6D7E">未处理</span>
                            <?php elseif ($item['state'] == 1): ?>
                                <span style="color:#F9C851">处理中</span>
                            <?php elseif ($item['state'] == 2): ?>
                                <span style="color:#5ACA64">已解决</span>
                            <?php elseif ($item['state'] == 3): ?>
                                <span>已关闭</span>
                            <?php endif ?>
                        </td>
                        <td><?=$item['worker']['realname']?></td>
                        <td><?=date('Y-m-d H:i:s', $item['update_at'])?></td>
                        <td class="operation">
                            <a class="layui-icon" href="javascript:;">&#xe642;</a>
                            <a class="layui-icon" href="javascript:;">&#x1006;</a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class="pagination pull-right">
                    <?php echo \yii\widgets\LinkPager::widget([
                        'pagination' => $pagination,
                        'prevPageLabel' => '&#8249;',
                        'nextPageLabel' => '&#8250;',
                        'firstPageLabel' => '<<',
                        'lastPageLabel' => '>>'
                    ]) ?>
                </div>
            </form>
        </div>
    </div>
</div>
<!--工单end-->
<script type="text/javascript" src="<?= WEB?>/theme/default/lib/layui-new/layui.js?<?= VERSION?>"></script>
<script src="<?= WEB?>/theme/default/lib/echarts/echarts.min.js"></script>
<script>
    layui.use('element', function(){
        var element = layui.element;

    });
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(formDemo)', function(data){
            layer.msg(JSON.stringify(data.field));
            return false;
        });
    });
    layui.use('table', function(){
        var table = layui.table;
    });
    var myChart = echarts.init(document.getElementById('main'));

    var option = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',      // 布局方式，默认为水平布局，可选为：
            // 'horizontal' ¦ 'vertical'
            x: 130,               // 水平安放位置，默认为全图居中，可选为：
            // 'center' ¦ 'left' ¦ 'right'
            // ¦ {number}（x坐标，单位px）
            y: 30,                  // 垂直安放位置，默认为全图顶端，可选为：
            // 'top' ¦ 'bottom' ¦ 'center'
            // ¦ {number}（y坐标，单位px）
            backgroundColor: 'rgba(0,0,0,0)',
            borderColor: '#ccc',       // 图例边框颜色
            borderWidth: 0,            // 图例边框线宽，单位px，默认为0（无边框）
            padding: 5,                // 图例内边距，单位px，默认各方向内边距为5，
                                       // 接受数组分别设定上右下左边距，同css
            itemGap: 10,               // 各个item之间的间隔，单位px，默认为10，
                                       // 横向布局时为水平间隔，纵向布局时为纵向间隔
            itemWidth: 10,             // 图例图形宽度
            itemHeight: 10,            // 图例图形高度
            textStyle: {
                color: '#717D85',          // 图例文字颜色
                fontSize: 14
            },
            data:['未解决','解决中','正常']
        },
        series: [
            {
                name:'全部网关',
                type:'pie',
                radius: ['30%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '14',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[
                    {value:"<?=$suspendingCounts?>", name:'未解决', itemStyle:{normal:{color:'#FF3F4E'}}},
                    {value:"<?=$handlingCounts?>", name:'解决中', itemStyle:{normal:{color:'#F9C851'}}},
                    {value:"<?=$finishCounts?>", name:'正常', itemStyle:{normal:{color:'#5ACA64'}}},
                ],
                center:[60,60]
            }
        ]
    };

    myChart.setOption(option);
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>