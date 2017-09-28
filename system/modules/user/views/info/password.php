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
        <li><a href="<?= \yii\helpers\Url::toRoute('update')?>">个人资料</a></li>
        <li class="layui-this">修改密码</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<div class="row">
    <div class="col-lg-6">
        <form class="layui-form" method="post" action="" style="margin-right: 50px;">
            <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block form-input-text">
                    <?= $model->username?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">原始密码</label>
                <div class="layui-input-block">
                    <input type="password" name="oldPassword" lay-verify="required" autocomplete="off" class="layui-input" placeholder="请输入旧密码"  value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-block">
                    <input type="password" name="newPassword" lay-verify="required" autocomplete="off" class="layui-input" placeholder="请输入新密码"  value="">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">确认密码</label>
                <div class="layui-input-block">
                    <input type="password" name="newPasswordRepeat" lay-verify="required" autocomplete="off" class="layui-input" placeholder="请重复新密码"  value="">
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
