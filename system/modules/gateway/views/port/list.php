<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
/* @var $pagination \yii\data\Pagination  */
//\yii\bootstrap\BootstrapAsset::register($this, \yii\web\View::PH_HEAD);

?>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>时间</th>
            <th>网关</th>
            <th>端口</th>
            <th>状态</th>
            <th>MAC</th>
            <th>IP</th>
            <th>IP包数量</th>
            <th>流量</th>
            <th>组</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= date('Y-m-d H:i:s', $item['add_time'])?></td>
            <td><?= \yii\helpers\Html::encode($item['gateway_id'])?></td>
            <td><?= \yii\helpers\Html::encode($item['if_port'])?></td>
            <td><?= \yii\helpers\Html::encode($item['action'])?></td>
            <td><?= \yii\helpers\Html::encode($item['mac'])?></td>
            <td><?= \yii\helpers\Html::encode($item['ip'])?></td>
            <td><?= \yii\helpers\Html::encode($item['pkg_num'])?></td>
            <td><?= \yii\helpers\Html::encode($item['bytes'])?></td>
            <td>组名称</td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
    'pagination' => $pagination,
])?>