<?php
use yii\helpers\Html;
\yii\web\JqueryAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title>系统登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="<?= WEB?>/theme/default/lib/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="<?= WEB?>/theme/default/css/login.css" />
    <script type="text/javascript">
        // 闪屏消息
        var flashMsgs = <?= \system\widgets\FlashMsg::widget()?>;
        // 如果存在父框架，那么刷新
        if(self!=top){
            top.location.href=self.location.href;
        }
    </script>
    <script type="text/javascript" src="<?= WEB?>/theme/default/lib/layui/layui.js"></script>
    <script type="text/javascript" src="<?= WEB?>/theme/default/js/global.js"></script>
    <?php $this->head() ?>
</head>

<body class="beg-login-bg">
<?php $this->beginBody() ?>
    <div class="beg-login-box">
        <div class="header">
            <h1>系统登录</h1>
        </div>
        <div class="beg-login-main">
            <form action="" class="layui-form" method="post">
                <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe612;</i>
                    </label>
                    <input type="text" name="username" lay-verify="required" autocomplete="off" placeholder="请输入用户名" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe642;</i>
                    </label>
                    <input type="password" name="password" lay-verify="required" autocomplete="off" placeholder="请输入密码" class="layui-input">
                </div>
                <!--<div class="layui-form-item w140">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe605;</i>
                    </label>
                    <input type="text" name="username" lay-verify="required" autocomplete="off" placeholder="请输入验证码" class="layui-input">
                    <div class="code">
                        <img src="images/0.jpg"/>
                    </div>
                </div>-->
                <?php if ($showCaptcha):?>
                    <div class="layui-form-item w140">
                        <label class="beg-login-icon">
                            <i class="layui-icon">&#xe605;</i>
                        </label>
                        <input type="text" name="verifyCode" lay-verify="required" autocomplete="off" placeholder="请输入验证码" class="layui-input">
                        <div class="code">
                        <?= \yii\captcha\Captcha::widget([
                                'captchaAction' => '/user/default/captcha',
                                'name' => 'verifyCode',
                                'attribute' => 'verifyCode',
                                'template' => '{image}',
                                'imageOptions'=>[
                                        'alt'=>'点击换图',
                                        'title'=>'点击换图',
                                        'style'=>'cursor:pointer'
                                ]
                        ])?>
                        </div>
                    </div>
                <?php endif;?>
                <div class="layui-form-item">
                    <div class="beg-pull-left beg-login-remember">
                        <label>记住我？</label>
                        <input type="checkbox" name="rememberMe" value="0" lay-skin="switch" title="记住帐号">
                    </div>
                    <div class="beg-pull-right">
                        <button class="layui-btn layui-btn-primary" lay-submit="">
                            <i class="layui-icon">&#xe605;</i> 登录
                        </button>
                    </div>
                    <div class="beg-clear"></div>
                </div>
            </form>
        </div>
        <div class="footer">
            <p>© <?= date('Y')?> <?= Yii::$app->systemConfig->getValue('COMPANY_NAME')?> 版权所有</p>
        </div>
    </div>

    <script>
        layui.use(['layer', 'form'], function() {
            var layer = layui.layer,
                $ = layui.jquery,
                form = layui.form();
        });
    </script>
<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>