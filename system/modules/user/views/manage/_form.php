<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午1:49
 */

/* @var $model \system\modules\user\models\User*/

// 用户状态数组
$user_status_list = Yii::$app->systemConfig->getValue('USER_STATUS_LIST', []);
$isEdit = false;
if (!$model->isNewRecord) {
    $isEdit = true;
}

if ($model->avatar) {
    $avatar = $model->avatar;
} else {
    $avatar = '/upload/avatar/default/'.rand(1, 10).'.jpg';
}

?>
<div class="row">
    <div class="col-lg-6">
        <form class="layui-form" method="post" action="">
            <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">头像</label>
                <div class="layui-input-inline" style="width: 250px;">
                    <img id="avatar_img" width="80" height="80" style="border-radius: 50%;" src="<?= $avatar?>">
                    <input type="file" name="avatarFile" class="layui-upload-file" lay-ext="jpg|jpeg|png|gif" lay-title="上传新头像">
                    <input type="hidden" name="avatar" value="<?= $avatar?>">
                </div>
                <div class="layui-input-inline" style="line-height: 80px;">
                    <p>建议上传300*300的图片</p>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required|config_name" autocomplete="off" placeholder="只能使用英文且不能重复" class="layui-input" value="<?= $model->username?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="realname" lay-verify="required" placeholder="请输入真实姓名" autocomplete="off" class="layui-input" value="<?= $model->realname?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="password" <?php if (!$isEdit) echo 'lay-verify="required"'; ?> autocomplete="off" class="layui-input" placeholder="请输入"  value="">
                </div>
                <div class="layui-form-mid layui-word-aux"><?php if ($isEdit) echo '如果不填写，则不修改密码'; ?></div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <?php foreach ($user_status_list as $k => $v):?>
                            <option value="<?= $k?>" <?php if ($k == $model->status) echo 'selected="selected"'; ?> ><?= $v?></option>
                        <?php endforeach; ?>
                    </select>
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
            ,success: function(res){
                //console.log(res); //上传成功返回值，必须为json格式
                if (res.code == 0) {
                    $("#avatar_img").attr('src', res.data.src);
                    $(":input[name=avatar]").val(res.data.src);
                }

                layer.msg(res.message);
            }
        });

        //自定义验证规则
        form.verify({
            config_name: function(value) {
                var message;
                $.ajax({
                    type : "get",
                    url : '<?= \yii\helpers\Url::toRoute(['', 'action'=> 'name-exit', 'id' => $model->user_id, 'username' => ''])?>'+value,
                    async : false,
                    success : function(res){
                        res = eval("(" + res + ")");
                        if (res.code == 1) {
                            //a = 'hello';
                            message = res.message;
                        }
                    }
                });
                return message;
            }
        });
    });
</script>
