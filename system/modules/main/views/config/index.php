<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
// 组列表数组
$groups = Yii::$app->systemConfig->getValue('CONFIG_GROUP_LIST', []);
$types = Yii::$app->systemConfig->getValue('CONFIG_TYPE_LIST', []);
// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 当前组
$group = Yii::$app->request->get('group');

// 权限
$canAdd = Yii::$app->user->can('main/config/add');
$canEdit = Yii::$app->user->can('main/config/edit');
$canDelete = Yii::$app->user->can('main/config/delete');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">配置列表</li>
        <?php if ($canAdd):?>
        <li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增配置</a></li>
        <?php endif;?>
    </ul>
    <!--<div class="layui-tab-content"></div>-->
</div>

<form action="" method="get">

    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>

    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>" <?php if ('' == $group) echo 'class="layui-this"';?> >全部</a>
        <?php foreach ($groups as $key => $value): ?>
        <a href="<?= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'group'=>$key])?>" <?php if ($key == $group) echo 'class="layui-this"'; ?> ><?= $value?></a>
        <?php endforeach;?>
    </span>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>标题</th>
            <th>分组</th>
            <th>类型</th>
            <?php if ($canEdit || $canDelete):?>
                <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><?= \yii\helpers\Html::encode($item['id'])?></td>
                <td><?= \yii\helpers\Html::encode($item['name'])?></td>
                <td><?= \yii\helpers\Html::encode($item['title'])?></td>
                <td><?php
                    if (isset($groups[$item['group']])) {
                        echo \yii\helpers\Html::encode($groups[$item['group']]);
                    } else {
                        echo '--';
                    }
                    ?></td>
                <td><?php
                    if (isset($types[$item['type']])) {
                        echo \yii\helpers\Html::encode($types[$item['type']]);
                    } else {
                        echo '--';
                    }
                    ?></td>
                <?php if ($canEdit || $canDelete):?>
                <td>
                    <?php if ($canEdit):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['id']])?>" ">编辑</a>
                    <?php endif;?>
                    <?php if ($canDelete):?>
                    <button class="layui-btn layui-btn-primary layui-btn-small delete-item" data-id="<?= $item['id']?>">删除</button>
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
