<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */

$keyword = Yii::$app->request->get('keyword');

// 网关的扫描状态列表
$is_study_list = Yii::$app->systemConfig->getValue('GATEWAY_IS_STUDY', []);

$canView = Yii::$app->user->can('gateway/gateway/view');
$canEdit = Yii::$app->user->can('gateway/gateway/edit');

?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">网关列表</li>
    </ul>
</div>

<form action="" method="get">
    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>
</form>


<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <!--<th>名称</th>-->
            <th>备注</th>
            <th>MAC</th>
            <th>IP</th>
            <th>是否扫描</th>
            <th>创建时间</th>
            <th>所属组</th>
            <?php if ($canEdit || $canView):?>
            <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= \yii\helpers\Html::encode($item['gateway_id'])?></td>
            <!--<td><?php /*//= $item['gateway_name'] ? \yii\helpers\Html::encode($item['gateway_name']) : '--'*/?></td>-->
            <td><?php
                if ($item['gateway_desc']) {
                    echo '<div class="system-tip" data-tip="'. \yii\helpers\Html::encode($item['gateway_desc']) .'">'.\yii\helpers\Html::encode(\yii\helpers\StringHelper::truncate($item['gateway_desc'], 10)).'</div>';
                } else {
                    echo '--';
                }
                ?></td>
            <td><?= \yii\helpers\Html::encode($item['mac'])?></td>
            <td><?= \yii\helpers\Html::encode($item['ip'])?></td>
            <td>
                <?php
                $is_study = Yii::$app->redis->hget("hash:gateway:{$item['gateway_id']}", 'is_study');
                if (isset($is_study_list[$is_study])) {
                    echo $is_study_list[$is_study];
                } else if ($is_study) {
                    echo $is_study;
                } else {
                    echo '--';
                }
                ?>
            </td>
            <td><?= date('Y-m-d H:i:s', $item['add_time'])?></td>
            <td><?= \system\modules\group\models\Group::getNamePath($item['group_id'])?></td>
            <?php if ($canEdit || $canView):?>
            <td>
                <?php if ($canView):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['view', 'id' => $item['gateway_id']])?>" ">查看</a>
                <?php endif;?>
                <?php if ($canEdit):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['gateway_id']])?>" ">编辑</a>
                <?php endif;?>

            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
    'pagination' => $pagination,
])?>