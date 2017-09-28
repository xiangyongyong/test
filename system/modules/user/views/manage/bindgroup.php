<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午5:18
 */
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">用户列表</a></li>
        <li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增用户</a></li>
        <li class="layui-this">绑定网关组</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<blockquote class="layui-elem-quote">
    <h4>说明：</h4>
    <p>只有维修人员需要绑定网关组；</p>
    <p>当网关组下的网关出现问题时，系统会自动派送给绑定了网关所在组的用户；</p>
    <p>如果上一步的网关组没人绑定，那么寻找网关组上一级组绑定的用户；</p>
</blockquote>


<form class="layui-form" method="post" action="" style="margin-right: 50px;">
    <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">用户</label>
        <div class="layui-input-block form-input-text""><?= $model->realname . '(' . $model->username . ')' ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">绑定组</label>
        <div class="layui-input-block form-input-text"">
            <div>
                <?php echo \system\modules\group\widgets\GroupWidget::widget([
                    'group_id' => $groups ? implode(',', $groups) : '',
                    'isMulti' => true,
                ])?>
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>


