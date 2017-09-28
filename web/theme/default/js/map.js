/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    // 创建地图
    var map = new AMap.Map('container', {
        center: [114.367526,30.512752],
        zoom: 14,
        //mapStyle:'normal' // normal  blue_night  light   fresh  dark
    });

    AMap.plugin(['AMap.ToolBar','AMap.Scale','AMap.OverView'],
        function(){
            map.addControl(new AMap.ToolBar());
            map.addControl(new AMap.Scale());
            //map.addControl(new AMap.ToolBar({liteStyle:true}));
            //map.addControl(new AMap.Scale({position:'RB'}));
            //map.addControl(new AMap.OverView({isOpen:true}));
        });       
    
    map.plugin(["AMap.Geocoder"], function () {
        geocoder = new AMap.Geocoder({
            radius: 1000,
            extensions: "all"
        });
    });
    
    //实例化信息窗体
    var infoWindow = new AMap.InfoWindow({
        offset: new AMap.Pixel(0, -30)
    });
    
    getData();
    // 获取首页的图表数据
    function getData() {
        // 获取当前点选的网关组id， 当前是在网关组状态还是具体的网关状态
        //var group_id = group_ids[group_ids.length-1] ? group_ids[group_ids.length-1] : '';
        $.get(getAllUrl, {ajax: 'getAll'}, function (res) {
            var data = JSON.parse(res);
            if (data.code == 0) {
                var gatewayData = data.data;
                //console.log(gatewayData);return false;
                // 调用点聚合 网关状态图表 网关组表 工单表
                addCluster(gatewayData);
            }
        })
    }
    
    // 添加点聚合
    function addCluster(gatewayData) {
        markers = []; // 初始化
        map.clearMap(); // 清除地图上的覆盖物

        var myIcon = [staticBasePath+'/images/icon/marker_green.png', staticBasePath+'/images/icon/marker_yellow.png', staticBasePath+'/images/icon/marker_red.png'];
        // 遍历所有的网关，创建marker，并且给marker添加点击事件

        $.each(gatewayData, function (i, item) {
            // 根据不同的显示级别，显示不同大小的点；根据不同的状态，显示不同颜色的点
            marker = new AMap.Marker({
                icon: myIcon[item.state],
                position: [item.longitude, item.latitude],
                //opacity:0.5,
                extData: {
                    gateway_id: item.gateway_id
                }
            });
                       
            //给地图上的点绑定事件
            marker.on('mouseover', showBasic);
            marker.on('click', showGateway);
            //markers.push(marker);
            marker.setMap(map);
        });

        // @TODO 让地图自适应在视觉范围内显示所有的点
        map.setFitView();

    }
    
    function showBasic(e) {
        e.target.setTop(true); // marker置顶
        var extData = e.target.getExtData();
        
        $.get(getBasicUrl, {gateway_id: extData.gateway_id}, function (res) {
            var res = JSON.parse(res);
            var data = res.data;
            var content = '<div class="basic-title">'+data.gateway.gateway_id+'\
                            </div><div class="basic-content"><div>位置：'+data.gateway.address+'</div>\n\
                            <div>责任人：'+data.gateway.worker_name+'</div>\n\
                            <div>状态：'+data.gateway.state+'</div>\n\
                            <div>待处理：'+data.gateway.suspending+'</div></div>';
            infoWindow.setContent(content);
            infoWindow.open(map, e.target.getPosition());            
        });
    }
    
     //在指定位置打开信息窗体
    function showGateway(e) {
        e.target.setTop(true); // marker置顶

        $("#gatewayBox").css('display','block')
        $("#gatewayBox ul").find(".port-num").removeClass("port-on");
        $("#gatewayBox ul").find(".layui-badge-dot").removeClass("port-yellow port-green port-red").addClass("port-gray");
        var extData = e.target.getExtData();
        $.get(getGatewayUrl, {gateway_id: extData.gateway_id}, function (res) {
            var res = JSON.parse(res);
            var data = res.data;
            //填充网关基本信息
            $('.gateway_counts').text(data.gateway.device_counts);
            $('.gateway_worker').text(data.gateway.worker_name);
            $('.gateway_ip').text(data.gateway.ip);
            $('.gateway_mac').text(data.gateway.mac);
            $('.gateway_state').text(data.gateway.state);
            $('.gateway_address').text(data.gateway.address);
            $('.gateway_pole').text(data.gateway.pole);
            $('.gateway_addtime').text(data.gateway.add_time);
            $('.gateway_temperature').text(data.statsEnv.temperature+"℃");
            $('.gateway_humidity').text(data.statsEnv.humidity+"%");
            $('.gateway_vibration').text(data.statsEnv.vibration+"次");
            //遍历设备状态
            $.each(data.device, function (i, item) {
                if(item.action == 1){
                    $("#gatewayBox ul").find(".layui-badge-dot").eq(i-1).removeClass("port-gray port-green port-red").addClass("port-yellow");
                }
                if(item.action == 2){
                    $("#gatewayBox ul").find(".layui-badge-dot").eq(i-1).removeClass("port-gray port-yellow port-red").addClass("port-green");
                }
                if(item.action == 3){
                    $("#gatewayBox ul").find(".layui-badge-dot").eq(i-1).removeClass("port-gray port-green port-yellow").addClass("port-red");
                }
                if(item.action == 4){
                    $("#gatewayBox ul").find(".layui-badge-dot").eq(i-1).removeClass("port-gray port-green port-yellow").addClass("port-red");
                }
            });
            //填充发送工单弹窗信息
            $("#target_id").val(data.gateway.gateway_id);
            $("#worker_id").val(data.gateway.worker_id);
            $("#worker_name").text(data.gateway.worker_name);
        });    
    }
    
    //端口信息弹窗       
    $('.port').each(function(index) {
        $(this).click(function() {
            if($(this).find('.layui-badge-dot').hasClass("port-gray")){
                return false;
            }
            $("#gatewayBox").find('.port-num').removeClass('port-on');
            $(this).find('.port-num').addClass('port-on');

            $('.port-panel').show();
            $('.port-panel').removeClass().addClass('port-panel');
            $('.port-panel').addClass('port'+index);
            
            var gateway_id = $("#target_id").val();
            $.get(getPortUrl, {gateway_id: gateway_id,port_id: index+1}, function (res) {
                var res = JSON.parse(res);
                var data = res.data;
                
                //填充网口基本信息
                $('.port_id').text(data.device.port);
                $('.port_state').html(data.device.action_desc);
                $('.port_pkg').text(data.device.pkg_num);
                $('.port_pps').text(data.device.pps_desc);
                $('.port_bytes').text(data.device.bytes_desc);
                $('.port_bandwidth').text(data.device.bandwidth_desc);
                $('.port_mac').text(data.device.mac);
                $('.port_ip').text(data.device.ip);
                $('.port_time').html(data.device.time_desc);
                $('.set_port_state').val(data.device.action);
                $('#set-port-state').attr("data-port", data.device.port);
            })
        });
    });
    
    $("#close-gateway").on('click', function(){
        $("#gatewayBox").css('display','none');
    })
    
    $("#close-port").on('click', function(){
        $("#portBox").css('display','none');
    })
    
    layui.use(['layer', 'form'], function() {
        var $ = layui.jquery,
            layer = layui.layer,form = layui.form;                
       
        // 扫描状态更改
//        $("body").delegate('.port_action', 'click', function () {
//           var state = $(this).attr('data-state');
//           var id = $("input[name=target_id]").val();
//           var port = $(this).parent().parent().find("input[name=port_id]").val();
//           $.get("<?= \yii\helpers\Url::toRoute(['state'])?>", {state: state, id: id, port: port}, function (res) {
//               var res = JSON.parse(res);
//               layer.msg(res.message);
//           });
//        });
        
        // 端口状态更改
        form.on('submit(port_change)', function(data){
            //console.log(data);return false;
            var gateway_id = $("#target_id").val();
            var port = $(this).attr('data-port'); // 网口号
            var state = data.field.state; // 更改状态

            $.get(setPortStateUrl, {gateway_id: gateway_id, port: port, state: state}, function (res) {
                var res = JSON.parse(res);
                layer.msg(res.message);
            });
            return false;
        });

        // 创建新工单
        var addBoxIndex = -1;
        $("#addWorkOrder").on('click', function() {
            if(addBoxIndex !== -1)
                return;
            addBoxIndex = layer.open({
                type: 1,
                title: '发起工单',
                content: $("#workOrderBox"),
                //btn: ['保存', '取消'],
                shade: false,
                area: ['450px'],
                yes: function(index) {
                    //触发表单的提交事件
                    $('form.layui-form').find('button[lay-filter=add]').click();
                },
                success: function(layero, index) {
                    //弹出窗口成功后渲染表单
                    form.render();
                    form.on('submit(add)', function(data) {
                        //console.log(data.field);return false; //当前容器的全部表单字段，名值对形式：{name: value}
                        $.get(addUrl, data.field, function (res) {
                            //console.log(res);
                            var res = JSON.parse(res);
                            if (res.code == 0) {
                                layer.closeAll();
                                $("#content").val('');
                                addBoxIndex = -1;
                            }
                            layer.msg(res.message);
                        });

                        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                    });
                    //console.log(layero, index);
                },
                end: function() {
                    addBoxIndex = -1;
                }
            });
        });

    });

