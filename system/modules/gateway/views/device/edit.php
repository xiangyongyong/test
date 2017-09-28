<?php
/* @var $model \system\modules\gateway\models\Device */

$this->title = '修改网关';
// 设备列表
$device_list = Yii::$app->systemConfig->getValue('DEVICE_TYPE_LIST', []);
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">设备列表</a></li>
        <li class="layui-this">修改设备</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<form class="layui-form" method="post" action="" style="margin-right: 300px;">
    <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

    <div class="layui-form-item">
        <label class="layui-form-label">设备ID</label>
        <div class="layui-input-block form-input-text""><?= $model->dev_id ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">网关名称</label>
        <div class="layui-input-block form-input-text""><?= \system\modules\gateway\models\Gateway::getName($model->gateway_id) ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">端口</label>
        <div class="layui-input-block form-input-text""><?= $model->if_port ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">厂商</label>
        <div class="layui-input-inline">
            <select name="factory_id" lay-verify="" lay-search>
                <?php foreach ($factory as $fid => $fname):?>
                    <option value="0"></option>
                    <option value="<?= $fid?>" <?php if ($fid == $model->factory_id): ?> selected <?php endif; ?>><?= $fname?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux">输入可以搜索，如果没有厂家，<a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute('factory/add')?>', '新增厂家', 'fa-cog')">点我添加厂家</a></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">Mac</label>
        <div class="layui-input-block form-input-text""><?= $model->mac ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">Ip</label>
        <div class="layui-input-block form-input-text""><?= $model->ip ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">设备类型</label>
        <div class="layui-input-inline">
            <select name="dev_type" lay-verify="" lay-search>
                <?php foreach ($device_list as $did => $dname):?>
                    <option value="0"></option>
                    <option value="<?= $did?>" <?php if ($did == $model->dev_type): ?> selected <?php endif; ?>><?= $dname?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux">输入可以搜索，如果没有所需类型，<a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/main/config/edit', 'name' => 'DEVICE_TYPE_LIST'])?>', '设备类型', 'fa-cog')">点我编辑设备类型</a></div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
