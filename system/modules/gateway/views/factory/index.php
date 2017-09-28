<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
$keyword = Yii::$app->request->get('keyword');
// 权限判断
$canAdd = Yii::$app->user->can('gateway/factory/add');
$canEdit = Yii::$app->user->can('gateway/factory/edit');
$canDelete = Yii::$app->user->can('gateway/factory/delete');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">厂商列表</li>
        <?php if ($canAdd):?><li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增厂商</a></li><?php endif;?>
    </ul>
</div>

<form action="" method="get">
    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>联系人</th>
            <th>电话</th>
            <th>创建时间</th>
            <?php if ($canEdit || $canDelete):?>
                <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= \yii\helpers\Html::encode($item['factory_id'])?></td>
            <td><?= \yii\helpers\Html::encode($item['factory_name'])?></td>
            <td><?= \yii\helpers\Html::encode($item['name'])?></td>
            <td><?= \yii\helpers\Html::encode($item['telephone'])?></td>
            <td><?= date('Y-m-d H:i:s', $item['add_time'])?></td>
            <?php if ($canEdit || $canDelete):?>
            <td>
                <?php if ($canEdit):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['factory_id']])?>" ">编辑</a>
                <?php endif;?>
                <?php if ($canDelete):?>
                    <button class="layui-btn layui-btn-primary layui-btn-small delete-item" data-id="<?= $item['factory_id']?>">删除</button>
                <?php endif;?>
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
