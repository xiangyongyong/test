<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/10
 * Time: 上午11:32
 */

/* @var $model \system\modules\gateway\models\Gateway*/

$this->title = '查看网关';

// 网关是否扫描
$is_study = Yii::$app->systemConfig->getValue('GATEWAY_IS_STUDY', []);
// 端口状态
$port_state = Yii::$app->systemConfig->getValue('PORT_STATE_LIST', []);
// 设备类型
//$device_type = Yii::$app->systemConfig->getValue('DEVICE_TYPE_LIST', []);

$id = Yii::$app->request->get('id');

// 将gps坐标转换为百度地图坐标
if (isset($gateway['location'])) {
    $gps = new \system\core\utils\Gps();
    $locArray = $gps->gps_bd09($gateway['location']);
    //$locArray = $gps->gps_gcj02($gateway['location']);
    //print_r($locArray);exit;
    $lat = $locArray['lat'];
    $lon = $locArray['lon'];
}
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">网关列表</a></li>
        <li class="layui-this">查看网关</li>
    </ul>
</div>

<div class="row layui-clear">

    <div class="col-lg-4 pull-left">

        <div class="ibox">
            <div class="ibox-title">
                <h5>网关信息</h5>
                <div class="ibox-tools pull-right">
                    <button class="layui-btn layui-btn-mini addWorkOrder"><i class="fa fa-plus" aria-hidden="true"></i> 创建工单</button>
                </div>
            </div>
            <div class="ibox-content no-padding">

                <table class="layui-table margin0" lay-even lay-skin="nob">
                    <colgroup>
                        <col width="100">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <td class="text-r bold">名称</td>
                        <td class="c666"><?= $model->gateway_id . ',' . $model->gateway_name?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">网关描述</td>
                        <td class="c666 word-break"><?= $model->gateway_desc?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">网关MAC</td>
                        <td class="c666"><?= $model->mac?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">网关IP</td>
                        <td class="c666"><?= $model->ip?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">所在组</td>
                        <td class="c666"><?= \system\modules\group\models\Group::getNamePath($model->group_id)?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">创建时间</td>
                        <td class="c666"><?= date('Y-m-d H:i:s', $model->add_time)?></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-lg-3 pull-left">
        <div class="ibox">
            <div class="ibox-title">
                <h5>实时环境数据</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="layui-table margin0" lay-even lay-skin="nob">
                    <colgroup>
                        <col width="100">
                        <col>
                    </colgroup>
                    <tbody id="gateway">
                    <tr>
                        <td class="text-r bold">温度</td>
                        <td class="c666"><?= isset($gateway['temperature']) ? $gateway['temperature'] : '--'?>℃</td>
                    </tr>
                    <tr>
                        <td class="text-r bold">湿度</td>
                        <td class="c666"><?= isset($gateway['humidity']) ? $gateway['humidity'] . '%' : '--'?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">震动</td>
                        <td class="c666"><?= isset($gateway['vibration']) ? $gateway['vibration'] .'次' : '--'?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">位置</td>
                        <td class="c666"><?= isset($gateway['location']) ? $gateway['location'] : '--'?></td>
                    </tr>
                    <tr>
                        <td class="text-r bold">扫描</td>
                        <td class="c666">
                            <?= isset($gateway['is_study_desc']) ? $gateway['is_study_desc'] : '--'?>
                            <?php if (isset($gateway['is_study'])): ?>
                                <?php if ($gateway['is_study'] == 2):?>
                                    <button class="layui-btn layui-btn-mini study_action" data-state="2">关闭</button>
                                <?php else:?>
                                    <button class="layui-btn layui-btn-warm layui-btn-mini study_action" data-state="0">开启</button>
                                <?php endif;?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-r bold">刷新</td>
                        <td class="c666"><?= isset($gateway['data_time_desc']) ? $gateway['data_time_desc'] : '--'?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5 pull-left">
        <div class="ibox">
            <div class="ibox-title">
                <h5>位置信息</h5>
            </div>
            <div class="ibox-content no-padding">
                <div id="mapLocation" style="width: 100%;height:230px;"><span style="padding: 20px;">暂无数据</span></div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-lg-12">
        设备列表
        <form class="layui-form" action="">
        <table class="layui-table">
            <thead>
            <tr>
                <th>网口</th>
                <th>类型</th>
                <th>MAC</th>
                <th>IP</th>
                <th>状态</th>
                <td>PPS</td>
                <td>带宽</td>
                <th>刷新时间</th>
            </tr>
            </thead>

            <tbody id="devices">
            <?php foreach ($devices as $item): ?>
                <tr>
                    <td><?= \yii\helpers\Html::encode($item['if_port'])?></td>
                    <td><div class="system-tip" data-tip="<?= $item['factory_name'];?>"><?= $item['dev_type_desc'];?></div></td>
                    <td><?= \yii\helpers\Html::encode($item['mac'])?></td>
                    <td><?= \yii\helpers\Html::encode($item['ip'])?></td>
                    <td><?= $item['action_desc']?>
                        <div class="layui-input-inline" style="margin-left:10px; width: 100px;">
                            <select name="port_state<?= $item['if_port']?>">
                                <option value="1">绑定设备</option>
                                <option value="2">开放端口</option>
                                <option value="3">关闭端口</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <button class="layui-btn layui-btn-small" data-port="<?= $item['if_port']?>" name="action" value="resolve" lay-submit lay-filter="port_change">更改</button>
                        </div>
                    </td>
                    <td><?= \yii\helpers\Html::encode($item['pps_desc'])?></td>
                    <td><?= \yii\helpers\Html::encode($item['bandwidth_desc'])?> </td>
                    <td><?= $item['time_desc']?></td>
                </tr>
            <?php endforeach;?>
            </tbody>

        </table>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>实时环境图表</h5>
            </div>
            <div class="ibox-content no-padding">
                <div id="main" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>网口速率</h5>
            </div>
            <div class="ibox-content no-padding">
                <div id="port" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>操作日志</h5>
    </div>
    <div class="ibox-content">
        <div class="feed-activity-list">
            <?php foreach ($logs as $log):?>
                <div class="feed-element">
                    <div class="avatar">
                        <div class="avatar-box">
                            <?php
                            if ($log['user']) {
                                echo "<img class='avatar-mini img-circle' src='{$log['user']['avatar']}' /> ";
                            } else {
                                echo "<img class='avatar-mini img-circle' src='/upload/avatar/default/system.png' /> ";
                            }
                            ?>
                        </div>
                        <p class="name"><?= $log['user']['realname'] ?: '系统'?></p>
                        <small class="text-muted"><?= date('Y-m-d H:i:s', $log['add_time'])?></small>
                        <small class="text-muted"><?= $log['ip']?></small>
                    </div>
                    <div class="comment-box">
                        <div class="comment"><?= \yii\helpers\Html::encode($log['content'])?></div>
                    </div>

                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>

<?= \system\modules\workorder\widgets\WorkorderWidget::widget([
    'targetName' =>  $model->gateway_id . ',' . $model->gateway_name,
    'targetId' => $model->gateway_id,
]);
?>

<!--百度地图-->
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=a7AewYUACz80AizMPryttKiUo4GpP75G"></script>

<script type="text/javascript">
    var form;
    layui.use(['layer', 'form'], function() {
        var $ = layui.jquery,
            layer = layui.layer;
        form = layui.form();

        // 扫描状态更改
        $("#gateway").on('click', '.study_action', function () {
            var state = $(this).attr('data-state');
            //console.log('state', state);return ;
            state = state == 2 ? 0 : 2;
            $.get('<?= \yii\helpers\Url::toRoute(['', 'id' => $id, 'ajax' => 'study', 'state' => ''])?>'+state, function (res) {
                var res = JSON.parse(res);
                layer.msg(res.message);
            });
        });
        // 端口状态更改
        form.on('submit(port_change)', function(data){
            //console.log(data);return false;
            var port = $(this).attr('data-port'); // 网口号
            var state = data.field['port_state'+port]; // 更改状态
            $.get('<?= \yii\helpers\Url::toRoute(['', 'id' => $id, 'ajax' => 'changePortState'])?>&port=' + port + '&state=' + state, function (res) {
                var res = JSON.parse(res);
                layer.msg(res.message);
            });
            return false;
        });
    });
</script>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));

    var envData = JSON.parse('<?= \yii\helpers\Json::encode($envData)?>');
    //console.log(envData);

    // 指定图表的配置项和数据
    var option = {
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['湿度','温度','震动次数']
        },
        xAxis: {
            //boundaryGap: false, // 左右是否留白
            axisTick: {
                show: false  // 显示刻度
            },
            axisLabel:{
                //X轴刻度配置
                interval:2 //0：表示全部显示不间隔；auto:表示自动根据刻度个数和宽度自动设置间隔个数
            },
            data: ['00:00','01:00','02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00']
        },
        yAxis: {
            name: '温度(℃) 湿度(%) 震动(次)',
            splitLine: {
                show: false
            }
        },
        series: [{
                name: '湿度',
                type: 'line',
                smooth: true,
                showAllSymbol: true,
                data: envData.humidity
            },{
                name: '温度',
                type: 'line',
                smooth: true,
                showAllSymbol: true,
                data: envData.temperature
            },{
                name: '震动次数',
                type: 'line',
                smooth: true,
                showAllSymbol: true,
                data: envData.vibration
        }]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var portChart = echarts.init(document.getElementById('port'));

    var portData = JSON.parse('<?= \yii\helpers\Json::encode($portData)?>');
    //console.log(portData);

    // 指定图表的配置项和数据
    var portOption = {
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            //data: portData.port
        },
        xAxis: {
            //boundaryGap: false, // 左右是否留白
            axisTick: {
                show: false  // 显示刻度
            },
            axisLabel:{
                //X轴刻度配置
                interval:2 //0：表示全部显示不间隔；auto:表示自动根据刻度个数和宽度自动设置间隔个数
            },
            data: ['00:00','01:00','02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00']
        },
        yAxis: {
            name: '速率pps',
            splitLine: {
                show: false
            }
        },
        series: []
    };

    var legend = [], series = [];
    for (var i in portData) {
        legend.push('网关'+i);
        series.push({
            name: '网口'+i,
            type: 'line',
            smooth: true,
            showAllSymbol: true,
            data: portData[i]
        });
    }

    portOption.legend.data = legend;
    portOption.series = series;

    // 使用刚指定的配置项和数据显示图表。
    portChart.setOption(portOption);
</script>

<?php if (isset($gateway['location'])): ?>
<script type="text/javascript">
    var map = new BMap.Map("mapLocation");          // 创建地图实例
    map.setCurrentCity("武汉");          // 设置地图显示的城市 此项是必须设置的
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
    var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
    map.addControl(top_left_navigation);
    var point = new BMap.Point(<?= $lon ?>,<?= $lat?>);  // 创建点坐标
    map.centerAndZoom(point, 17);                 // 初始化地图，设置中心点坐标和地图级别
    var marker = new BMap.Marker(point); // 创建点
    map.addOverlay(marker);
</script>
<?php endif;?>

<script type="text/javascript">
    // 自动刷新动态数据,60s刷新一次
    setInterval(function () {
       $.get('<?= \yii\helpers\Url::toRoute(['', 'id' => $id, 'ajax' => 'all'])?>', function (res) {
            var res = JSON.parse(res);
            //console.log('data', res);
            if (res.code == 1) {
                layer.msg(data.message);
                return false;
            }

            var data = res.data;

            // 处理网关数据
            var gateway = data.gateway;
            var gatewayHtml = '';
            gatewayHtml += '<tr><td class="text-r bold">温度</td><td class="c666">'+gateway.temperature+'℃</td></tr>';
            gatewayHtml += '<tr><td class="text-r bold">湿度</td><td class="c666">'+gateway.humidity+'%</td></tr>';
            gatewayHtml += '<tr><td class="text-r bold">震动</td><td class="c666">'+gateway.vibration+'次</td></tr>';
            gatewayHtml += '<tr><td class="text-r bold">位置</td><td class="c666">'+gateway.location+'</td></tr>';
            gatewayHtml += '<tr><td class="text-r bold">扫描</td><td class="c666">'+gateway.is_study_desc;
            if (gateway.is_study == 2) {
                gatewayHtml += ' <button class="layui-btn layui-btn-mini study_action" data-state="2">关闭</button>';
            } else {
                gatewayHtml += ' <button class="layui-btn layui-btn-warm layui-btn-mini study_action" data-state="0">开启</button>';
            }
            gatewayHtml += '</td></tr>';
            gatewayHtml += '<tr><td class="text-r bold">刷新</td><td class="c666">'+gateway.data_time_desc+'</td></tr>';
            $("#gateway").html(gatewayHtml);

            // @TODO 处理地图数据

            // 处理设备数据
            var deviceHtml = '';
            $.each(data.device, function (index, item) {
                //console.log(item);
                var tr = '<tr>';
                tr += '<td>'+item.if_port+'</td>';
                tr += '<td><div class="system-tip" data-tip="'+item.factory_name+'">'+item.dev_type_desc+'</div></td>';
                tr += '<td>'+item.mac+'</td>';
                tr += '<td>'+item.ip+'</td>';
                tr += '<td>'+item.action_desc;
                tr += '<div class="layui-input-inline" style="margin-left:10px; width: 100px;">';
                tr += '    <select name="port_state'+item.if_port+'">';
                tr += '         <option value="1">绑定设备</option>';
                tr += '         <option value="2">开放端口</option>';
                tr += '         <option value="3">关闭端口</option>';
                tr += '    </select>';
                tr += '</div>';
                tr += '<div class="layui-input-inline"> ';
                tr += '     <button class="layui-btn layui-btn-small" data-port="'+item.if_port+'" name="action" value="resolve" lay-submit lay-filter="port_change">更改</button>';
                tr += '</div>';
                tr += '</td>';
                tr += '<td>'+item.pps_desc+'</td>';
                tr += '<td>'+item.bandwidth_desc+'</td>';
                tr += '<td>'+item.time_desc+'</td>';
                tr += '</tr>';
                deviceHtml += tr;
            });
            $("#devices").html(deviceHtml);

           form.render('select');

           // 处理图表数据
           var realEnvData = data.envData;
           myChart.setOption({
               series: [{
                   data: realEnvData.humidity
               },{
                   data: realEnvData.temperature
               },{
                   data: realEnvData.vibration
               }]
           });

           // 处理网口数据
           var portData = data.portData;
           var legend = [], series = [];
           for (var i in portData) {
               legend.push('网关'+i);
               series.push({
                   name: '网口'+i,
                   type: 'line',
                   smooth: true,
                   showAllSymbol: true,
                   data: portData[i]
               });
           }

           portOption.legend.data = legend;
           portOption.series = series;

           // 使用刚指定的配置项和数据显示图表。
           portChart.setOption(portOption);

       });
    }, 60*1000);
</script>
