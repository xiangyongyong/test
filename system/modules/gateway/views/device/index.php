<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
$this->title = '设备管理';
// 设备类型列表
$device_types = Yii::$app->systemConfig->getValue('DEVICE_TYPE_LIST', []);
// 端口扫描状态列表
$port_states  = Yii::$app->systemConfig->getValue('PORT_STATE_LIST', []);

// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 设备类型
$device_type = Yii::$app->request->get('device_type');

// 权限
$canEdit = Yii::$app->user->can('gateway/device/edit');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">设备列表</li>
    </ul>
</div>

<form action="" method="get">

    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>

    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>" <?php if ('' == $device_type) echo 'class="layui-this"';?> >全部</a>
            <?php foreach ($device_types as $key => $value): ?>
                <a href="<?= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'device_type'=>$key])?>" <?php if ($key == $device_type) echo 'class="layui-this"'; ?> ><?= $value?></a>
            <?php endforeach;?>
    </span>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>类型</th>
            <th>网关</th>
            <th>端口</th>
            <th>厂家</th>
            <th>MAC</th>
            <th>IP</th>
            <th>状态</th>
            <th>添加时间</th>
            <?php if ($canEdit):?>
                <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= \yii\helpers\Html::encode($item['dev_id'])?></td>
            <td><?php
                if (isset($device_types[$item['dev_type']])) {
                    echo $device_types[$item['dev_type']];
                } else {
                    echo '--';
                }
                ?></td>
            <td><?php
                if (isset($item['gateway']) && $item['gateway']['gateway_name']) {
                    $url = \yii\helpers\Url::toRoute(['/gateway/gateway/view', 'id' => $item['gateway_id']]);
                    echo "<a href=\"javascript:openUrl('{$url}', '网关{$item['gateway_id']}', 'fa fa-cubes')\">{$item['gateway']['gateway_name']}</a>";
                } else {
                    echo \yii\helpers\Html::encode($item['gateway_id']);
                }
                ?></td>
            <td><?= \yii\helpers\Html::encode($item['if_port'])?></td>
            <td><?php
                if (isset($item['factory']) && $item['factory']['factory_name']) {
                    echo "<div class='system-tip' data-tip='联系人： {$item['factory']['name']}<br/>电话：{$item['factory']['telephone']}'>{$item['factory']['factory_name']}</div>";
                } else {
                    echo '--';
                }
                ?></td>
            <td><?= \yii\helpers\Html::encode($item['mac'])?></td>
            <td><?= \yii\helpers\Html::encode($item['ip'])?></td>
            <td><?php
                $action = Yii::$app->redis->hget("hash:port_info:{$item['gateway_id']}:{$item['if_port']}", 'action');
                if (isset($port_states[$action])) {
                    echo $port_states[$action];
                } else {
                    echo '--';
                }
                ?></td>
            <td><?= date('Y-m-d H:i:s', $item['add_time'])?></td>
            <?php if ($canEdit):?>
                <td>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['dev_id']])?>" ">编辑</a>
                </td>
            <?php endif;?>

        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
    'pagination' => $pagination,
])?>