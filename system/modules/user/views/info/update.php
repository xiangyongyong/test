<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午1:49
 */

/* @var $model \system\modules\main\models\Config*/

// 用户状态数组
$user_status_list = Yii::$app->systemConfig->getValue('USER_STATUS_LIST', []);
$isEdit = false;
if (!$model->isNewRecord) {
    $isEdit = true;
}
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">个人资料</li>
        <li><a href="<?= \yii\helpers\Url::toRoute('password')?>">修改密码</a></li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<div class="row">
    <div class="col-lg-6">
        <form class="layui-form" method="post" action="">
            <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">头像</label>
                <div class="layui-input-inline" style="width: 250px;">
                    <img id="avatar_img" width="80" height="80" style="border-radius: 50%;" src="<?= $model->avatar?>">
                    <input type="file" name="avatarFile" class="layui-upload-file" lay-ext="jpg|jpeg|png|gif" lay-title="上传新头像">
                    <input type="hidden" name="avatar" value="<?= $model->avatar?>">
                </div>
                <div class="layui-input-inline" style="line-height: 80px;">
                    <p>建议上传300*300的图片</p>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block form-input-text">
                    <?= $model->username?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="realname" lay-verify="required" placeholder="请输入真实姓名" autocomplete="off" class="layui-input" value="<?= $model->realname?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="text" name="phone" lay-verify="required|phone" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $model->phone?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="email" lay-verify="required|email" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $model->email?>">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    layui.use(['form', 'element', 'upload'], function() {
        var $ = layui.jquery,
            layer = layui.layer, //获取当前窗口的layer对象
            form = layui.form();

        layui.upload({
            url: '<?= \yii\helpers\Url::toRoute(['/main/attach/upload-avatar'])?>'
            , success: function (res) {
                //console.log(res); //上传成功返回值，必须为json格式
                if (res.code == 0) {
                    $("#avatar_img").attr('src', res.data.src);
                    $(":input[name=avatar]").val(res.data.src);
                }

                layer.msg(res.message);
            }
        });
    });
</script>
