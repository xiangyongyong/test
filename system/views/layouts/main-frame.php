<?php
use yii\helpers\Html;

/* @var $this \yii\web\View*/
$this->title = Yii::$app->systemConfig->getValue('SYSTEM_NAME');


// 当前登录用户的权限  对菜单显示与否进行判断

// 解析href参数
function parseHref($href)
{
    $url = '#';
    if (is_array($href)) {
        $url = $href;
        $url[0] = '/' . $href[0];
    } else {
        $url = '/' . $href;
    }
    return $url;
}

// 判断是否可以访问
function canAccess($href)
{
    if (is_array($href)) {
        return Yii::$app->user->can($href[0]);
    } else {
        return Yii::$app->user->can($href);
    }
}

$navBar = [];



// 解析url
foreach (Yii::$app->params['navBar'] as $key => $value) {

    if (isset($value['href'])) {
        // 判断是否有权限，如果没有跳过
        if (!canAccess($value['href'])) {
            continue;
        }

        $navBar[$key] = $value;
        $href = parseHref($value['href']);
        $navBar[$key]['href'] = \yii\helpers\Url::toRoute($href);
    }

    if (isset($value['children'])) {
        foreach ($value['children'] as $k => $child) {
            // 如果没有权限，那么跳过
            if (!canAccess($child['href'])) {
                // 删除此权限
                unset($navBar[$key]['children'][$k]);
                continue;
            }

            $href = parseHref($child['href']);

            $navBar[$key]['children'][$k]['href'] = \yii\helpers\Url::toRoute($href);
        }
    }
}

$navBar = array_values($navBar);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
	<head>
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
		<title><?= Html::encode($this->title) ?></title>
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="format-detection" content="telephone=no">

		<link rel="stylesheet" href="<?= WEB?>/theme/default/lib/layui/css/layui.css?<?= VERSION?>" media="all" />
		<link rel="stylesheet" href="<?= WEB?>/theme/default/css/global.css?<?= VERSION?>" media="all">
		<link rel="stylesheet" href="<?= WEB?>/theme/default/css/style.css?<?= VERSION?>" media="all">
		<link rel="stylesheet" href="<?= WEB?>/theme/default/css/custom.css?<?= VERSION?>" media="all">
		<link rel="stylesheet" href="<?= WEB?>/theme/default/lib/font-awesome/css/font-awesome.min.css?<?= VERSION?>">
        <?php $this->head() ?>
        <script type="text/javascript">
            <?php $this->beginBlock('mainJs');?>
            var navs = <?= json_encode($navBar)?>;
            var basePath = '<?= WEB ?>';
            var getNotifyUrl = '<?= \yii\helpers\Url::toRoute(['/notify/user/notread'])?>';
            // 如果存在父框架，那么刷新
            if(self!=top){
                top.location.href=self.location.href;
            }
            <?php $this->endBlock();?>
            <?php $this->registerJs($this->blocks['mainJs'], \yii\web\View::POS_BEGIN)?>
        </script>
	</head>

	<body>
    <?php $this->beginBody() ?>
		<div class="layui-layout layui-layout-admin" style="border-bottom: solid 5px #1aa094;">
			<div class="layui-header header header-demo">
				<div class="layui-main">
					<div class="admin-login-box">
						<a class="logo" style="left: 0;" href="/">
							<span style="font-size: 22px;"><?= Yii::$app->systemConfig->getValue('SYSTEM_NAME')?></span>
						</a>
						<div class="admin-side-toggle" title="隐藏菜单栏">
							<i class="fa fa-bars" aria-hidden="true"></i>
						</div>
						<div class="admin-side-full" title="全屏显示">
							<i class="fa fa-arrows-alt" aria-hidden="true"></i>
						</div>
					</div>
					<ul class="layui-nav admin-header-item">
                        <li class="layui-nav-item" id="userNotify">
                        </li>
						<li class="layui-nav-item">
							<a href="javascript:;" class="admin-header-user">
								<img src="<?= Yii::$app->user->identity->avatar?>" />
								<span><?= Yii::$app->user->identity->username?></span>
							</a>
							<dl class="layui-nav-child">
								<dd>
									<a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/user/info/update'])?>', '个人信息', 'fa fa-user-o');"><i class="fa fa-fw fa-user-o" aria-hidden="true"></i> 个人信息</a>
								</dd>
                                <dd>
                                    <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/user/info/password'])?>', '修改密码', 'fa fa-lock');"><i class="fa fa-fw fa-lock" aria-hidden="true"></i> 修改密码</a>
                                </dd>
								<dd>
									<a href="<?= \yii\helpers\Url::toRoute(['/user/default/logout'])?>"><i class="fa fa-fw fa-sign-out" aria-hidden="true"></i> 登出</a>
								</dd>
							</dl>
						</li>
					</ul>
					<ul class="layui-nav admin-header-item-mobile">
						<li class="layui-nav-item">
							<a href="<?= \yii\helpers\Url::toRoute(['/user/default/logout'])?>"><i class="fa fa-sign-out" aria-hidden="true"></i> 登出</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="layui-side layui-bg-black" id="admin-side">
				<div class="layui-side-scroll" id="admin-navbar-side" lay-filter="side"></div>
			</div>
			<div class="layui-body" style="bottom: 0;border-left: solid 2px #1AA094;" id="admin-body">
				<div class="layui-tab admin-nav-card layui-tab-brief" lay-filter="admin-tab">
					<ul class="layui-tab-title">
						<li class="layui-this">
							<i class="fa fa-home" aria-hidden="true"></i>
							<cite>欢迎登录</cite>
						</li>
					</ul>
					<div class="layui-tab-content" style="min-height: 150px; padding: 0;">
						<div class="layui-tab-item layui-show">
							<iframe src="<?= \yii\helpers\Url::to(['/main/default/welcome'])?>"></iframe>
						</div>
					</div>
				</div>
			</div>
			<div class="layui-footer footer footer-demo" id="admin-footer">
				<div class="layui-main">
					<p>© <?= date('Y')?> <?= Yii::$app->systemConfig->getValue('COMPANY_NAME')?> 版权所有</p>
				</div>
			</div>
			<div class="site-tree-mobile layui-hide">
				<i class="layui-icon">&#xe602;</i>
			</div>
			<div class="site-mobile-shade"></div>

			<!--锁屏模板 start-->
			<script type="text/template" id="lock-temp">
				<div class="admin-header-lock" id="lock-box">
					<div class="admin-header-lock-img">
						<img src="<?= WEB?>/theme/default/images/0.jpg"/>
					</div>
					<div class="admin-header-lock-name" id="lockUserName">beginner</div>
					<input type="text" class="admin-header-lock-input" value="输入密码解锁.." name="lockPwd" id="lockPwd" />
					<button class="layui-btn layui-btn-small" id="unlock">解锁</button>
				</div>
			</script>
			<!--锁屏模板 end -->

			<script type="text/javascript" src="<?= WEB?>/theme/default/lib/layui/layui.js?<?= VERSION?>"></script>
			<script type="text/javascript" src="<?= WEB?>/theme/default/lib/jquery/js.cookie.js?<?= VERSION?>"></script>
			<script type="text/javascript" src="<?= WEB?>/theme/default/lib/SwfObject/swfobject.js?<?= VERSION?>"></script>
            <!--<script type="text/javascript" src="theme/default/datas/nav.js"></script>-->
			<script src="<?= WEB?>/theme/default/js/index.js?<?= VERSION?>"></script>
		</div>
        <div id="sound" style="display: none;"></div>
    <?php $this->endBody() ?>
	</body>

</html>
<?php $this->endPage() ?>