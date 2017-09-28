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
            <th>温度</th>
            <th>湿度</th>
            <th>位置</th>
            <th>震动</th>
            <th>组</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= date('Y-m-d H:i:s', $item['add_time'])?></td>
            <td><?= \yii\helpers\Html::encode($item['gateway_id'])?></td>
            <td><?= \yii\helpers\Html::encode($item['temperature'])?></td>
            <td><?= \yii\helpers\Html::encode($item['humidity'])?></td>
            <td><?= \yii\helpers\Html::encode($item['location'])?></td>
            <td><?= \yii\helpers\Html::encode($item['vibration'])?></td>
            <td>组名称</td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
        'pagination' => $pagination,
])?>

