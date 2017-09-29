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
    .body-bg{ background: url(<?= WEB?>/theme/default/images/bg.jpg); }
    .layui-layout-admin .layui-header{ height: 90px; border-bottom: #3F4349 1px solid; background: rgba(0,0,0,0); }
    .layui-layout-admin .layui-side, .layui-layout-admin .layui-body{ top: 90px; }
    .layui-layout-admin .layui-logo{ line-height: 90px; }
    .title-color{ color: #EEEEEE; font-size: 36px; font-weight: 500; font-family: "微软雅黑"; letter-spacing:4px; line-height: 90px;}
    .search{ position: relative; line-height: 90px; }
    .search-input{ width: 250px; height: 34px; background: rgba(0,0,0,0); border-radius: 50px; border: #515356 1px solid; color: #818183; padding-left: 20px; }
    .icon-sousuo{ text-decoration: none; color: #FFFFFF; font-weight: 500; font-size: 20px; position: absolute; top: 31px; right: 17px; border: none; background-color: unset; }
    .icon-sousuo:hover{ color: #FFFFFF; }
    .search-right{ right: 415px; }
  .layui-layout-admin .layui-footer{ left: 0; right: auto; width: 100%; }
  .layui-layout-admin .layui-footer{ background-color: #26292F; color: #A9A9A9; text-align: center; }
  .layui-body{ left: 88px; display:flex; justify-content:center; align-items:center; }
  #container{ width: 60%; height: 80%; }
  /*侧栏展开时*/
  .layui-layout-admin .layui-side:hover{ width: 200px; transition: 1s; }
	.layui-layout-admin .layui-side:hover .nav-ico-text{ text-align: center; width: 100%; display: inline; transition: 0.5s; }
  .layui-layout-admin .layui-side:hover .layui-nav-tree .layui-nav-item a{ height: 45px; margin: 0; }
	.layui-nav-tree .layui-nav-item{}
	.layui-layout-admin .layui-side:hover .nav-ico-samll{ transition: 1s; transform: scale(0.7); }
	.nav-ico-samll{transition: 2s; transform: scale(1);}
  /*缩起时*/
	.nav-ico-text{ display: block; line-height: 14px; text-align: left; padding-top: 10px;}
  .layui-nav-tree .layui-nav-item a{ height: 68px; margin: 30px 0; }
  .layui-layout-admin .layui-side{ width: 88px; transition: 1s; }
  .rigth-float-box{ position: absolute; top: 42px; right: 29px; }
  .rigth-float-box .monitor{ width: 244px; height: 104px; background: url(<?= WEB?>/theme/default/images/bgBox.png) no-repeat; }
  .rigth-float-box .monitor *{color: #51FFFF;}
  .rigth-float-box .monitor h5{ font-size: 24px; padding: 15px 0 0 24px; }
  .rigth-float-box .monitor p{font-size: 48px; padding-left: 24px;}
  .rigth-float-box .monitor p span{font-size: 20px; padding-left: 15px;}
  .rigth-float-box .info{ width: 240px; height: 230px; background: rgba(38,41,46,0.6); margin-top: 23px; }
  .info ul li{ padding: 15px 0; }
  .info ul li .info-top .layui-badge-dot{ width: 12px; height: 12px; margin: 0 9px; background-color: #F75757; }
  .info ul li .info-top .info-title{ font-size: 18px; color: #FFF; font-family: "微软雅黑"; letter-spacing: 4px; }
  .info ul li .info-top .info-num{ font-size: 16px; color: #FFF; float: right; padding-right: 10px; }
  .info ul li .info-top{ margin-bottom: 5px; padding-left: 3px; }
  .info ul li .layui-progress{ height: 4px; width: 90%; margin: 0 auto; }
  .info ul li .layui-progress .layui-progress-bar{ height: 4px; }
  .info ul li .layui-progress .layui-bg-red{ background-color: #F75757!important; }
  .info ul li .layui-progress .layui-progress-bar .layui-progress-text{ position: absolute; top: 8px; font-size: 14px; color: #E2E2E2; left: 0px; }
  .info ul li .layui-bg-green{ background-color: #50DC3E!important; }
  .weather{ width: 210px; height: 55px; float: right; color: #FFF; margin-top: 24px; }
  .weather .cloud{ font-size: 24px; }
  .weather .cloud b{ font-weight: normal; }
  .weather .cloud span:nth-of-type(1){ margin-right: 18px; }
  .weather .cloud span:nth-of-type(2){ margin-right: 14px; }
  .weather .date{ font-size: 14px; float: right; margin-right: 20px; }
  .gateway-info{
    width: 394px;
    height: 96%;
    position: absolute;
    right: 0;
    top: 2px;
    border: 15px solid transparent;
    background-color: #26292E;
    -padding: 10px 20px;
    -moz-border-image:url("<?= WEB?>/theme/default/images/borderBg.png") 30 30 round; /* Old Firefox */
    -webkit-border-image:url("<?= WEB?>/theme/default/images/borderBg.png") 30 30 round; /* Safari */
    -o-border-image:url("<?= WEB?>/theme/default/images/borderBg.png") 30 30 round; /* Opera */
    border-image:url("<?= WEB?>/theme/default/images/borderBg.png") 30 30 round;
	overflow: auto;
  }
  .closeBtn{ width: 19px; height: 19px; display: block; float: right; background: url("<?= WEB?>/theme/default/images/closeBtn.png") no-repeat; }
  .device-box{ width: 321px; height: 104px; margin: 41px 0 40px 35px; background: url("<?= WEB?>/theme/default/images/box.png") no-repeat; }
  .device-box ul{ padding:54px 0 0 9px; }
  .device-box ul li{ float: left; padding: 0 11px; }
  .device-box ul li .port .layui-badge-dot{ width: 12px; height: 12px; border: #989899 2px solid; }
  .device-box ul li .port-num{ display: block; text-align: center; padding-top: 3px; }
  .device-box ul li .port-on{ color: #FFF; }
  .device-box ul li .port .port-red{ background-color: #F34131; }
  .device-box ul li .port .port-green{ background-color: #42D738; }
  .device-box ul li .port .port-yellow{ background-color: #FFCD00; }
  .device-box ul li .port .port-gray{ background-color: #C2C2C2; }
  .gateway-info .layui-table[lay-even] tr:nth-child(even){ background-color: #26292E; }
  .gateway-info .layui-table[lay-skin=line]{ border: #373D43 1px solid; }
  .gateway-info .layui-table td{ border:none; padding: 9px 0; font-size: 12px; }
  .gateway-info .layui-table{ background-color: #32373D; }
  .gateway-info .layui-table tr td:nth-of-type(1){ text-align: right; color: #FFF; }
  .gateway-info .layui-table tr td:nth-of-type(2){ color: #6EB2D5; padding-left: 10px; }
  .gateway-info .layui-table tbody tr:hover, .layui-table-hover{ background-color: #3B4046!important; }
  .bottom-btn{ position: relative; }
  .bottom-btn .layui-btn-primary{ background: rgba(0,0,0,0); color: #FFF; }
  .port-panel{ position: absolute;  z-index: 9; width: 392px; padding: 30px 10px; border:#5A656D 1px solid; background: #26292E; right: 5px; top: 160px; display: none; }
  .port-panel .closeBtn{ position: absolute; right: 10px; top: 10px; }
  /*网口小三角*/
  .port0:after, .port-panel:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 61px;    
    position: absolute;
    width: 0;
  } 
   .port0:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 63px;   /*  after的left需比before多2px  */
  }
  .port0:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   /*  before的top需比after小4px  */
  }
  .port1:after, .port1:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 97px;    
    position: absolute;
    width: 0;
  } 
   .port1:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 99px;  
  }
  .port1:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port2:after, .port2:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 136px;    
    position: absolute;
    width: 0;
  } 
   .port2:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 138px;  
  }
  .port2:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port3:after, .port3:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 173px;    
    position: absolute;
    width: 0;
  } 
   .port3:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 175px;  
  }
  .port3:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port4:after, .port4:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 211px;    
    position: absolute;
    width: 0;
  } 
   .port4:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 213px;  
  }
  .port4:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port5:after, .port5:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 249px;    
    position: absolute;
    width: 0;
  } 
   .port5:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 251px;  
  }
  .port5:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port6:after, .port6:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 287px;    
    position: absolute;
    width: 0;
  } 
   .port6:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 289px;  
  }
  .port6:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port7:after, .port7:before {
    border: solid transparent;
    content: ' ';
    height: 0;
    top: -20px;
    left: 326px;    
    position: absolute;
    width: 0;
  } 
   .port7:after {
    border-width: 10px;
    border-bottom-color: #26292E;
    top: -20px;
    left: 328px;  
  }
  .port7:before {
    border-width: 12px;
    border-bottom-color: #545F67;
    top: -24px;   
  }
  .port-panel .layui-table[lay-even] tr:nth-child(even){ background-color: #26292E; }
  .port-panel .layui-table td{ border:none; padding: 9px 0; font-size: 12px; }
  .port-panel .layui-table{ background-color: #32373D; border: none; }
  .port-panel .layui-table tr td:nth-of-type(1){ text-align: right; color: #FFF; }
  .port-panel .layui-table tr td:nth-of-type(2){ color: #6EB2D5; padding-left: 10px; }
  .port-panel .layui-table tbody tr:hover, .layui-table-hover{ background-color: #3B4046!important; }
  .selectStyle .layui-form-item{ margin: 0; }
  .selectStyle .layui-form-item .layui-input-block{ margin: 0; }
  .selectStyle .layui-form .layui-input-inline:nth-of-type(1){ width: 140px; }
  .selectStyle .layui-form .layui-input-inline:nth-of-type(2){ width: 88px; }
  .selectStyle .layui-select-title input{ height: 32px; background: #32373D; color: #6EB2D5; border: none; }
  .selectStyle button{ height: 32px; line-height: 32px; background: #4286ED; width: 88px; margin-left: 8px; }
  .selectStyle .layui-anim-upbit{ background: #26292E; }
  .selectStyle .layui-anim-upbit .layui-this{ background-color: #32373D; }
  </style>
</head>
<body class="layui-layout-body body-bg">
<?php $this->beginBody() ?>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo"><img src="<?= WEB?>/theme/default/images/logo.png" alt=""></div>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <div class="layui-nav layui-layout-left title-color">
      集中控制与安全运维系统
    </div>
    <!--搜索start-->
    <div class="layui-nav layui-layout-right search-right">
      <div class="search">
      	<form class="layui-form" action="">
            <input type="text" name="search" class="search-input" placeholder="搜索">
            <button lay-submit lay-filter="search" class="iconfont icon-sousuo"></button>
      	</form>
      </div>
    </div>
    <!--搜索end-->

    <!--天气start-->
    <div class="weather">
      <div class="cloud">
        <img src="<?= WEB?>/theme/default/images/cloud.png" alt="">
        <span><b>28</b>&#8451;</span>
        <span>星期<b>一</b></span>
      </div>
      <div class="date">2017.09.19</div>
    </div>
    <!--天气end-->
  </div>
  
  <!--侧边栏start-->
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree"  lay-filter="test">
        <li class="layui-nav-item">
          <a class="" href="javascript:;">
          	<img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/avatar.png" alt="">
          	<span class="nav-ico-text">admin</span>
          </a>
          <dl class="layui-nav-child">
            <dd><a href="<?= \yii\helpers\Url::toRoute(['/user/info/update'])?>"><i class="fa fa-fw fa-user-o" aria-hidden="true"></i> 个人信息</a></dd>
            <dd><a href="<?= \yii\helpers\Url::toRoute(['/user/info/password'])?>"><i class="fa fa-fw fa-lock" aria-hidden="true"></i> 修改密码</a></dd>
            <dd><a href="<?= \yii\helpers\Url::toRoute(['/user/default/logout'])?>"><i class="fa fa-fw fa-sign-out" aria-hidden="true"></i> 登出</a></dd>
          </dl>
        </li>
        <li class="layui-nav-item">
          <a href="javascript:;">
          	<img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/administration.png" alt="">
          	<span class="nav-ico-text">运维菜单</span>
          </a>
          <dl class="layui-nav-child">
            <dd><a href="javascript:;">全部工单</a></dd>
            <dd><a href="javascript:;">我的工单</a></dd>
          </dl>
        </li>
        <li class="layui-nav-item">
          <a href="">
                <img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/security.png" alt="">
                <span class="nav-ico-text">安全管理</span>
          </a>
        </li>
        <li class="layui-nav-item">
          <a href="">
          	<img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/statistics.png" alt="">
          	<span class="nav-ico-text">统计分析</span>
          </a>
         </li>
         <li class="layui-nav-item">
          <a href="">
          	<img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/log.png" alt="">
          	<span class="nav-ico-text">日志管理</span>
          </a>
         </li>
         <li class="layui-nav-item">
          <a href="javascript:;">
          	<img class="nav-ico-samll" src="<?= WEB?>/theme/default/images/personnel.png" alt="">
          	<span class="nav-ico-text">人员管理</span>
          </a>
          <dl class="layui-nav-child">
            <dd><a href="javascript:;">用户列表</a></dd>
            <dd><a href="javascript:;">人员分配</a></dd>
          </dl>
         </li>
      </ul>
    </div>
  </div>
  <!--侧边栏end-->
  
  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div id="container"></div>
    
    <!--监测信息start-->
    <div class="rigth-float-box">
      <div class="monitor">
        <h5>监测处理次数</h5>
        <p>19356<span>次</span></p>
      </div>
      <div class="info">
        <ul>
          <li>
            <div class="info-top">
              <span class="layui-badge-dot"></span>
              <span class="info-title">异常</span>
              <span class="info-num"><b><?= $state[2];?></b>个</span>
            </div>
            <div class="layui-progress" lay-showPercent="yes">
              <div class="layui-progress-bar layui-bg-red" lay-percent="<?= ($state[2]/$total)*100;?>%"></div>
            </div>
          </li>
          <li>
            <div class="info-top">
              <span class="layui-badge-dot layui-bg-orange"></span>
              <span class="info-title">待修</span>
              <span class="info-num"><b><?= $state[1];?></b>个</span>
            </div>
            <div class="layui-progress" lay-showPercent="yes">
              <div class="layui-progress-bar layui-bg-orange" lay-percent="<?= ($state[1]/$total)*100;?>%"></div>
            </div>
          </li>
          <li>
            <div class="info-top">
              <span class="layui-badge-dot layui-bg-green"></span>
              <span class="info-title">正常</span>
              <span class="info-num"><b><?= $state[0];?></b>个</span>
            </div>
            <div class="layui-progress" lay-showPercent="yes">
              <div class="layui-progress-bar layui-bg-green" lay-percent="<?= ($state[0]/$total)*100;?>%"></div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <!--监测信息end-->
    
    <!--网关信息start-->
    <div id="gatewayBox" class="gateway-info" style="display: none;">
      <a class="closeBtn" id="close-gateway" href="javascript:;"></a>
      <div class="device-box">
        <ul>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">1</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">2</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">3</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">4</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">5</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">6</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">7</span></div>
          </li>
          <li>
            <div class="port"><span class="layui-badge-dot port-gray"></span><span class="port-num">8</span></div>
          </li>
        </ul>
      </div>
      <table class="layui-table" lay-even lay-skin="line">
        <colgroup>
          <col width="120">
          <col width="240">
          <col>
        </colgroup>
        <tbody>
          <tr>
            <td>设备连接的数目：</td>
            <td class="gateway_counts"></td>
          </tr>
          <tr>
            <td>责任人：</td>
            <td class="gateway_worker"></td>
          </tr>
          <tr>
            <td>ip：</td>
            <td class="gateway_ip"></td>
          </tr>
          <tr>
            <td>mac：</td>
            <td class="gateway_mac"></td>
          </tr>
          <tr>
            <td>工作状态：</td>
            <td class="gateway_state"></td>
          </tr>
          <tr>
            <td>位置：</td>
            <td class="gateway_address"></td>
          </tr>
          <tr>
            <td>电线杆编号：</td>
            <td class="gateway_pole"></td>
          </tr>
          <tr>
            <td>启动时间：</td>
            <td class="gateway_addtime"></td>
          </tr>
          <tr>
            <td>温度：</td>
            <td class="gateway_temperature"></td>
          </tr>
          <tr>
            <td>湿度：</td>
            <td class="gateway_humidity"></td>
          </tr>
          <tr>
            <td>震动：</td>
            <td class="gateway_vibration"></td>
          </tr>
        </tbody>
      </table>
      <div class="bottom-btn">
        <button id="listWorkOrder" class="layui-btn layui-btn-primary">维修记录</button>
        <button id="addWorkOrder" class="layui-btn layui-btn-normal">发送工单</button>
      </div>
    </div>
    <!--网关信息end-->
    <!--网口信息start-->
    <div id="portBox" class="port-panel port1">
      <a class="closeBtn" id="close-port" href="javascript:;"></a>
      <table class="layui-table" lay-even lay-skin="line">
        <colgroup>
          <col width="120">
          <col width="270">
          <col>
        </colgroup>
        <tbody>
          <tr>
            <td>网口：</td>
            <td class="port_id"></td>
          </tr>
          <tr>
            <td>状态：</td>
            <td class="port_state"></td>
          </tr>
          <tr>
            <td>报文数：</td>
            <td class="port_pkg"></td>
          </tr>
          <tr>
            <td>速率：</td>
            <td class="port_pps"></td>
          </tr>
          <tr>
            <td>流量：</td>
            <td class="port_bytes"></td>
          </tr>
          <tr>
            <td>带宽：</td>
            <td class="port_bandwidth"></td>
          </tr>
          <tr>
            <td>设备MAC：</td>
            <td class="port_mac"></td>
          </tr>
          <tr>
            <td>设备IP：</td>
            <td class="port_ip"></td>
          </tr>
          <tr>
            <td>刷新时间：</td>
            <td class="port_time"></td>
          </tr>
          <tr>
            <td>设置端口状态：</td>
            <td class="selectStyle">          
              <form class="layui-form" action="">
                <div class="layui-form-inline">
                    <div class="layui-input-inline">
                        <select class="set_port_state" name="state" lay-verify="required">
                            <option value="1">绑定设备</option>
                            <option value="2">开放端口</option>
                            <option value="3">关闭端口</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                      <button id="set-port-state" data-port="" class="layui-btn layui-btn-normal" lay-submit lay-filter="port_change">确定</button>
                    </div>
                  </div>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--网口信息end-->
  </div>
  
    <div class="layui-footer">
    <!-- 底部固定区域 -->
    武汉雨滴科技有限公司提供技术支持 Powered by © 2000-2017 yuder.cn
    </div>
    <!--工单弹窗start-->
    <div id="workOrderBox" style="display: none;">
      
        <form class="layui-form" action="<?= \yii\helpers\Url::toRoute(['/workorder/default/add'])?>">
            <input type="hidden" id="target_id" name="target_id" value="" />
            <div class="layui-form-item">
                <input type="hidden" id="worker_id" name="worker_id" value="" />
                <label class="layui-form-label">负责人</label>
                <div id="worker_name" class="layui-input-block" style="line-height: 36px;">
                    
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">故障类型</label>
                <div class="layui-input-block" style="margin-right: 185px;">
                    <select name="content_type" lay-verify="required">
                        <option value="1">设备无响应</option>
                        <option value="2">端口故障</option>
                        <option value="3">流量异常</option>
                        <option value="4">温度异常</option>
                        <option value="0">其他原因</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述补充</label>
                <div class="layui-input-block" style="margin-right: 20px;">
                    <textarea placeholder="请输入问题描述" class="layui-textarea" name="content" id="content" lay-verify=""></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                  <button class="layui-btn" lay-submit lay-filter="add">发送工单</button>
                </div>
            </div>
        </form>
      
    </div>
    <!--工单弹窗end-->
</div>
<script type="text/javascript" src="<?= WEB?>/theme/default/lib/jquery/jquery-1.9.1.min.js?<?= VERSION?>"></script>  
<script type="text/javascript" src="<?= WEB?>/theme/default/lib/layui-new/layui.js?<?= VERSION?>"></script>  

<script src="http://cache.amap.com/lbs/static/es5.min.js"></script>
<script src="http://webapi.amap.com/maps?v=1.4.0&key=84b2f7930d0992706ffd65960e9ecf52"></script>
<script src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<script>
//JavaScript代码区域
layui.use('element', function(){
   var element = layui.element;
  
});
var staticBasePath = '<?= WEB."/theme/default";?>'; // 静态资源路径
var getAllUrl = '<?= \yii\helpers\Url::toRoute(['map'])?>';
var getBasicUrl = '<?= \yii\helpers\Url::toRoute(['basic'])?>';
var getGatewayUrl = '<?= \yii\helpers\Url::toRoute(['view'])?>';
var getPortUrl = '<?= \yii\helpers\Url::toRoute(['port'])?>';
var addUrl = '<?= \yii\helpers\Url::toRoute(['add'])?>';
var setPortStateUrl = '<?= \yii\helpers\Url::toRoute(['changeport'])?>';
</script>
<script type="text/javascript" src="<?= WEB?>/theme/default/js/map.js?<?= VERSION?>"></script>
<?php $this->endBody() ?>
</body>
</html>
 <?php $this->endPage() ?>     