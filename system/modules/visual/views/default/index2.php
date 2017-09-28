<?php
/**
 *
 * 目标：所有的操作和维护可以在整张地图上操作；
 *
 * 网关：
 * 正常时：动态小绿色
 * 非正常时：动态大红点儿
 * hover时变大，显示网关的实时状态，显示绑定的设备信息等，查看环境图表，在infowindow中显示菜单，比如修改状态，比如发起工单，比如备注，比如校正位置，比如其他操作；
 * moveout时消失；
 *
 * 所有的网关环境状态 比如温度、湿度等的散点图；
 */

//\system\modules\visual\assets\VisualAsset::register($this);

?>
<!doctype html>
<html lang="zh-CN">

<head>
    <!-- 原始地址：//webapi.amap.com/ui/1.0/ui/misc/MarkerList/examples/index.html -->
    <!--<base href="//webapi.amap.com/ui/1.0/ui/misc/MarkerList/examples/" />-->
    <meta charset="utf-8">
    <title><?= Yii::$app->systemConfig->getValue('SYSTEM_NAME', '运维系统')?></title>
    <!--<link rel="stylesheet" type="text/css" href="./index.css">-->
    <style>
        #container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0.7;
        }
        .clear { clear: both; }
        #gateway {
            display: none;
            position: absolute;
            top: 20px;
            right: 50px;
            width: 500px;
            height: 600px;
            background: #FFFFFF;
            overflow:auto;
            overflow-x:hidden;
        }

        #index {
            display: block;
            position: absolute;
            top: 20px;
            left: 100px;
            width: 500px;
            height: 700px;
            background: #FFFFFF;
            overflow:auto;
            overflow-x:hidden;
        }

        .tooltip-point{
            display:inline-block;
            margin-right:5px;
            border-radius:10px;
            width:9px;
            height:9px;
        }
    </style>
</head>

<body style="background: #0627ad;">
<div id="outer-box">
    <div id="container"></div>
    <div id="index">
        @TODO 网关的区域分布图
        <div id="gatewayGroup" style="height: 200px;"></div>
        @TODO 网关的状态
        <div id="gatewayState" style="height: 200px;"></div>
        @TODO 工单状态
        <div id="workOrderState" style="height: 200px;"></div>
    </div>
    <div id="gateway">
        <div>
            网关信息：
            <div id="gatewayInfo"></div>
            <div>状态：正常/维护中/报警/已报废</div>
        </div>
        <div>
            报警信息：
            <div id="gatewayWarning">
                <ul>
                    <li>网口2数据异常</li>
                    <li>网口3数据异常</li>
                    <li>网口3插入设备</li>
                </ul>
            </div>
        </div>
        <div>
            环境信息图表：
            <div id="gatewayEnvChart" style="height: 300px;width: 100%;"></div>
        </div>
        <div>
            设备表：
            <div id="gatewayDevice">
                <ul>
                    <li>设备1</li>
                    <li>设备2</li>
                    <li>设备3</li>
                </ul>
            </div>
        </div>
        <div>
            设备流量表：
            <div id="gatewayPortChart" style="height: 300px;width: 100%;"></div>
        </div>

    </div>
</div>
<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/jquery/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/echarts/echarts.common.min.js"></script>
<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/echarts/dark.js"></script>
<script type="text/javascript" src="<?= WEB ?>/theme/default/lib/jquery/js.cookie.js"></script>
<script type="text/javascript" src='//webapi.amap.com/maps?v=1.3&key=84b2f7930d0992706ffd65960e9ecf52'></script>
<!-- UI组件库 1.0 -->
<script src="//webapi.amap.com/ui/1.0/main.js"></script>
<script type="text/javascript">
    function log() {
        <?php if (Yii::$app->systemConfig->getValue('SYSTEM_JS_LOG')):?>
        for (var i in arguments){
            console.log(arguments[i]);
        }
        <?php endif; ?>
    }

    // 创建地图
    var map = new AMap.Map('container', {
        center: [114.367526,30.512752],
        zoom: 14,
        mapStyle:'blue_night' // normal  blue_night  light   fresh  dark
    });

    AMap.plugin(['AMap.ToolBar','AMap.Scale','AMap.OverView'],
        function(){
            map.addControl(new AMap.ToolBar());
            map.addControl(new AMap.Scale());
            //map.addControl(new AMap.OverView({isOpen:true}));
        });

    var cluster, marker, markers = [], infoWindow, geocoder;
    map.plugin(["AMap.Geocoder"], function () {
        geocoder = new AMap.Geocoder({
            radius: 1000,
            extensions: "all"
        });
    });

    var group_ids = [];

    /**
     *
     * 整体规划
     * 1，全局时显示一些基本统计信息，比如：问题网关的比例，错误类型比例，待处理的问题比例，平均环境数据，各个区的数据比例等
     * 2，当点击某个具体网关时显示此网关的具体信息以及设备等；
     * 3，维护一个轮询或者socket，后台有报警时马上通知到前台来；报警到网关马上进行闪烁提示；并弹出具体消息，可以立即派单；
     * 4，维护中到网关应该标记出来；
     *
     */

    // 一分钟同步一次
    setInterval(function () {
        getData();
    }, 1000*60);


    // 所有网关点
    var gatewayData = JSON.parse('<?= \yii\helpers\Json::encode($data)?>');
    log(gatewayData);

    init();

    function getData() {
        // 获取当前点选的网关组id， 当前是在网关组状态还是具体的网关状态
        var group_id = group_ids[group_ids.length-1] ? group_ids[group_ids.length-1] : '';
        $.get('<?= \yii\helpers\Url::toRoute(['index', 'action' => 'ajax', 'group_id' => ''])?>'+group_id, function (res) {
            log('返回的原始数据', res);
            var data = JSON.parse(res);
            log('格式化数据', data);
            if (data.code == 0) {
                // 如果没有子组，那么直接返回，不再做数据处理
                if (!data.data.hasChildGroup) {
                    group_ids.pop();
                    return false;
                }
                log('实时信息：', data);
                gatewayData = data.data;

                // 调用点聚合
                addCluster();
                // 网关状态图表
                showGatewayState();
                showGatewayGroup();
                showWorkOrderState();
            }
        })
    }

    function init() {
        // 第一次进来初始化
        // 调用点聚合
        addCluster();
        // 网关状态图表
        showGatewayState();
        showGatewayGroup();
        showWorkOrderState();
    }

    // 展示网关组柱状图表，可以下钻
    function showGatewayGroup() {
        var gatewayGroup = gatewayData.gatewayGroup;
        // 将数据缓存一下，然后等待点击以后获取，没有重复数据项
        var map = gatewayGroup.map;

        var gatewayGroupChart = echarts.init(document.getElementById('gatewayGroup'), 'dark');
        var option = {
            backgroundColor: '#5089b0',
            tooltip : {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                splitLine: {
                    show: true
                },
                data: gatewayGroup.name
            },
            yAxis: {
                name: '数量',
                splitLine: {
                    show: true
                }
            },
            series: [
                {
                    name: '网关数量',
                    type: 'bar',
                    data: gatewayGroup.count
                }
            ]
        };

        gatewayGroupChart.setOption(option);

        gatewayGroupChart.on('click',function(object){
            //console.log(map[object['name']]);
            var group_id = map[object['name']];
            log('group_id:', group_id);
            group_ids.push(group_id);
            getData();
        });
    }

    // 显示网关的状态图表
    function showGatewayState() {
        var gatewayState = gatewayData.gatewayState;
        // 整理数据
        var data = [], map = {
            0: '正常',
            1: '维护中',
            2: '异常'
        };
        for (var i in gatewayState) {
            data.push({value: gatewayState[i].count, name: map[gatewayState[i].state]});
        }

        var gatewayStateChart = echarts.init(document.getElementById('gatewayState'), 'dark');
        var option = {
            backgroundColor: '#5089b0',

            tooltip : {
                trigger: 'item',
                //formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                //data:['网口2','网口3','网口4','网口5','网口6']
            },
            series : [
                {
                    name: '网关状态',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:data,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]

        };
        gatewayStateChart.setOption(option);
    }

    // 显示工单的状态图表
    function showWorkOrderState() {
        // 组织数据
        var workOrderState = gatewayData.workOrderState;
        var workOrderData = {x:[], sus: [], handle: [], finish: []};
        for (var i in workOrderState) {
            var item = workOrderState[i];
            workOrderData.x.push(i);
            workOrderData.sus.push(item[0]);
            workOrderData.handle.push(item[1]);
            workOrderData.finish.push(parseInt(item[2])+parseInt(item[3]));
        }

        var gatewayStateChart = echarts.init(document.getElementById('workOrderState'), 'dark');
        var option = {
            backgroundColor: '#5089b0',
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data:['待处理','处理中','已处理']
            },
            xAxis: {
                type: 'category',
                splitLine: {
                    show: true
                },
                data: workOrderData.x
                //data: ['201701', '201702', '201703', '201704', '201605', '201606', '201607', '201608', '201609']
            },
            yAxis: {
                name: '工单数量',
                splitLine: {
                    show: true
                }
            },
            series: [
                {
                    name: '待处理',
                    type: 'line',
                    stack: '总量',
                    areaStyle: {normal: {}},
                    //data: [32, 30, 40, 64, 56, 34, 128, 48, 16]
                    data: workOrderData.sus
                },
                {
                    name: '处理中',
                    type: 'line',
                    stack: '总量',
                    areaStyle: {normal: {}},
                    //data: [128, 32, 50, 64, 20, 34, 48, 16, 56]
                    data: workOrderData.handle
                },
                {
                    name: '已处理',
                    type: 'line',
                    stack: '总量',
                    areaStyle: {normal: {}},
                    //data: [32, 40, 40, 64, 56, 34, 128, 48, 16]
                    data: workOrderData.finish
                }
            ]
        };
        gatewayStateChart.setOption(option);
    }

    // 显示网关的信息
    function showGateway(e) {
        e.target.setTop(true); // marker置顶

        var extData = e.target.getExtData();

        $.get('<?= \yii\helpers\Url::toRoute(['get', 'ajax' => 'gateway', 'gateway_id' => ''])?>'+extData.gateway_id, function (res) {
            var res = JSON.parse(res);
            var data = res.data;

            /*
             // 显示infoWindow
             var content = '';
             content += '<div>';
             content += "<div style=\"padding:0px 0px 0px 4px;\"><b>网关"+data.gateway_id+"</b></div>";
             content += "<div>位置: "+data.address+"</div>";
             content += "<div>电线杆标号："+data.pole+"</div>";
             content += '</div>';

             infoWindow = new AMap.InfoWindow({
             offset: new AMap.Pixel(0, -30),
             content: content
             });
             infoWindow.open(map, e.target.getPosition());*/

            showGatewayInfo(data, e.target);
        });
    }

    /**
     * 组合网关信息
     * @param gateway array 网关信息
     * @param marker Marker 点对象
     */
    function showGatewayInfo(data, marker) {
        $("#gateway").show();
        var gateway = data.gateway;
        var gatewayInfo = '<ul>';
        gatewayInfo += '<li>网关：'+gateway.gateway_id+'</li>';
        gatewayInfo += '<li id="gateway_address">位置：'+gateway.address+'</li>';
        gatewayInfo += '<li>电杆：'+gateway.pole+'</li>';
        gatewayInfo += '<li>备注：'+gateway.gateway_desc+'</li>';
        gatewayInfo += '<li><button id="correctLocation">位置校正</button></li>';

        // 监听位置校正按钮，实时保存
        $("#gateway").on('click', '#correctLocation', function (res) {
            // 设置为可以拖动，并且改变按钮的状态及样式用来提示用户，保存新的位置后恢复为默认样式
            marker.setDraggable(true);
            marker.setIcon('http://a.amap.com/lbs/static/img/2.png');
            marker.on('dragend', function(e) {
                //console.log('拖拽结束了');
                geocoder.getAddress([e.lnglat.getLng(), e.lnglat.getLat()], function(status, result) {
                    if (status === 'complete' && result.info === 'OK') {
                        //geocoder_CallBack(result);
                        $("#gateway_address").html(result.regeocode.formattedAddress);
                    }
                });

                //alert('拖动结束');
                // 拖动结束以后实时保存
                $.post('<?= \yii\helpers\Url::toRoute(['post'])?>', {ajax: 'correctLocation', gateway_id: gateway.gateway_id, longitude: e.lnglat.getLng(), latitude: e.lnglat.getLat()}, function (res) {
                    //console.log(res);
                    // 更新网关对象中的内容
                    gatewayData.gatewayList[gateway.gateway_id].longitude = e.lnglat.getLng();
                    gatewayData.gatewayList[gateway.gateway_id].latitude = e.lnglat.getLat();
                    //console.log(allGateway);
                });
            });
        });

        $("#gatewayInfo").html(gatewayInfo);

        // @TODO 显示实时环境数据
        showEnvChart(data.statsEnv);

        // @TODO 显示设备信息
        showPortChart(data.portData);
    }

    function showPortChart(data) {
        var portChart = echarts.init(document.getElementById('gatewayPortChart'), 'dark');
        var option = {
            backgroundColor: '#5089b0',
            tooltip : {
                trigger: 'axis',
                formatter: function (params) {
                    //console.log(params);
                    // 获取时间;
                    var date = new Date(params[0].value[0]-0);
                    //console.log(date);
                    var hour = date.getHours() < 10 ? '0'+date.getHours() : date.getHours();
                    var minute = date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes();
                    var str = hour + ':' + minute + '<br />';
                    $.each(params, function (i, item) {
                        //console.log(item);
                        // 最后一项是锚点，不打印
                        if (i < params.length - 1) {
                            str += '<span class="tooltip-point" style="background-color:'+item.color+';"></span>';
                            str += item.seriesName + '：' + item.value[1];
                            str += ' MB <br />';
                        }
                    });
                    return str;
                },
                /*axisPointer: {
                 type: 'cross',
                 label: {
                 backgroundColor: '#6a7985'
                 }
                 }*/
            },
            legend: {
                //data:['网口2','网口3','网口4','网口5','网口6']
            },
            /*toolbox: {
             feature: {
             saveAsImage: {}
             }
             },*/
            /*grid: {
             left: '3%',
             right: '4%',
             bottom: '3%',
             containLabel: true
             },*/
            xAxis: {
                type: 'time',
                boundaryGap: [20, 20],
                //splitNumber:6,
                splitLine: {
                    show: true
                }
            },
            /*yAxis : [
             {
             type : 'value'
             }
             ],*/
            yAxis: {
                name: '流量(MB)',
                splitLine: {
                    show: true
                }
            },
            series: []
            /*series : [
             {
             name:'网口2',
             type:'line',
             showSymbol: false,
             hoverAnimation: false,
             smooth: true,
             stack: '总量',
             areaStyle: {normal: {}},
             data:data[5]
             },
             {
             name:'ant',
             type:'line',
             //stack: '总量',
             areaStyle: {normal: {}},
             silent: true,
             showSymbol:false,
             hoverAnimation: false,
             smooth: true,
             itemStyle:{normal:{opacity:0}},
             lineStyle:{normal:{opacity:0}},
             data:[
             ['2017/04/18 00:00:00', 0],
             ['2017/04/18 23:59:59', 0]
             ]
             },
             ]*/
        };


        var legend = [], series = [];
        for (var i in data) {
            legend.push('网口'+i);
            series.push({
                name: '网口'+i,
                type: 'line',
                stack: '总量',
                smooth: true,
                showSymbol: false,
                hoverAnimation: false,
                areaStyle: {normal: {}},
                data: data[i]
            });
        }

        // 锚点，形成一条24小时线，今天的时间线
        var today = getToday();
        var anchor = [
            [today[0], 0],
            [today[1], 0]
        ];

        series.push({
            name:'ant',
            type:'line',
            //stack: '总量',
            areaStyle: {normal: {}},
            silent: true,
            showSymbol:false,
            hoverAnimation: false,
            smooth: true,
            itemStyle:{normal:{opacity:0}},
            lineStyle:{normal:{opacity:0}},
            data:anchor
        });

        option.legend.data = legend;
        option.series = series;
        //console.log(option);
        portChart.setOption(option);
    }

    // 获取今天的时间 开始和结束的时间戳
    function getToday() {
        var startTime = new Date(new Date().setHours(0, 0, 0, 0)).getTime();
        var endTime = new Date().setTime((startTime/1000+24*60*60-1)*1000);
        //console.log(startTime, endTime);
        return [startTime, endTime];
    }


    /*var date = new Date("1492452000000"-0);
     console.log('year', date.getFullYear());*/

    function showEnvChart(data) {
        // 初始化echarts图表
        // 基于准备好的dom，初始化echarts实例
        var envChart = echarts.init(document.getElementById('gatewayEnvChart'), 'dark');

        // 锚点，形成一条24小时线，今天的时间线
        var today = getToday();
        var anchor = [
            [today[0], 0],
            [today[1], 0]
        ];

        var option = {
            backgroundColor: '#005e61',
            legend: {
                data:['平均湿度', '平均温度', '平均震动'],
            },
            tooltip: {
                trigger: 'axis',
                formatter: function (params) {
                    // 获取时间;
                    var date = new Date(params[0].value[0]-0);
                    //console.log(date);
                    var hour = date.getHours() < 10 ? '0'+date.getHours() : date.getHours();
                    var minute = date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes();
                    var str = hour + ':' + minute + '<br />';
                    var unitMap = ['%', '℃', '次'];
                    $.each(params, function (i, item) {
                        //console.log(item);
                        if (i < params.length - 1) {
                            str += '<span class="tooltip-point" style="background-color:'+item.color+';"></span>';
                            str += item.seriesName + '：' + item.value[1];
                            str += unitMap[i];
                            str += '<br />';
                        }
                    });
                    return str;
                },
                axisPointer: {
                    animation: false
                }
            },
            xAxis: {
                type: 'time',
                //splitNumber:6,
                //splitNumber: '2016/12/18 06:00:00',
                // 显示分割线
                splitLine: {
                    show: true
                }
            },
            yAxis: {
                max: '80',
                type: 'value',
                boundaryGap: [0, '100%'],
                // 显示分割线
                splitLine: {
                    show: true
                }
            },
            series: [{
                name: '平均湿度',
                type: 'line',
                showSymbol: true,
                hoverAnimation: true,
                data: data.humidity
            },{
                name: '平均温度',
                type: 'line',
                showSymbol: true,
                hoverAnimation: true,
                data: data.temperature
            },{
                name: '平均震动',
                type: 'line',
                showSymbol: true,
                hoverAnimation: true,
                data: data.vibration
            },{
                name:'.anchor',
                type:'line',
                silent: true,
                showSymbol:false,
                data:anchor,
                itemStyle:{normal:{opacity:0}},
                lineStyle:{normal:{opacity:0}}
            }]
        };


        // 使用刚指定的配置项和数据显示图表。
        envChart.setOption(option);
    }

    // 添加点聚合
    function addCluster(tag) {

        // 遍历所有的网关，创建marker，并且给marker添加点击事件
        $.each(gatewayData.gatewayList, function (i, item) {
            marker = new AMap.Marker({
                //icon: "http://webapi.amap.com/theme/v1.3/markers/n/mark_b.png",
                position: [item.longitude, item.latitude],
                opacity:0.5,
                extData: {
                    gateway_id: item.gateway_id
                }
            });
            //
            marker.on('click', showGateway);
            markers.push(marker);
            //marker.setMap(map);
        });

        if (cluster) {
            cluster.setMap(null);
        }
        if (tag == 1) {
            var sts = [{
                url: "http://a.amap.com/lbs/static/img/1102-1.png",
                size: new AMap.Size(32, 32),
                offset: new AMap.Pixel(-16, -30),
                textSize:'0'
            }, {
                url: "http://a.amap.com/lbs/static/img/2.png",
                size: new AMap.Size(32, 32),
                offset: new AMap.Pixel(-16, -30),
                textSize:'0'
            }, {
                url: "http://lbs.amap.com/wp-content/uploads/2014/06/3.png",
                size: new AMap.Size(48, 48),
                offset: new AMap.Pixel(-24, -45),
                textColor: '#CC0066',
                textSize:'0'
            }, {
                url: "http://lbs.amap.com/wp-content/uploads/2014/06/3.png",
                size: new AMap.Size(48, 48),
                offset: new AMap.Pixel(-24, -45),
                textColor: '#CC0066',
                textSize:'0'
            }, {
                url: "http://lbs.amap.com/wp-content/uploads/2014/06/3.png",
                size: new AMap.Size(48, 48),
                offset: new AMap.Pixel(-24, -45),
                textColor: '#CC0066',
                textSize:'0'
            }];
            map.plugin(["AMap.MarkerClusterer"], function() {
                cluster = new AMap.MarkerClusterer(map, markers, {
                    styles: sts
                });
            });
        } else {
            map.plugin(["AMap.MarkerClusterer"], function() {
                cluster = new AMap.MarkerClusterer(map, markers, {
                    minClusterSize: 5
                });
            });
        }
    }

</script>
</body>

</html>