<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 当前状态
$state = Yii::$app->request->get('state', '');

// 组列表数组
$workOrderState = Yii::$app->systemConfig->getValue('WORD_ORDER_STATE_LIST', []);

// 权限判断
$canAdd = Yii::$app->user->can('workorder/default/add');
$canView = Yii::$app->user->can('workorder/default/view');
$canDelete = Yii::$app->user->can('workorder/default/delete');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">工单列表</li>
        <?php if ($canAdd):?><li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增工单</a></li><?php endif;?>
    </ul>
</div>

<form action="" method="get">
    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>

    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>" <?php if ('' === $state) echo 'class="layui-this"';?> >全部</a>
        <?php foreach ($workOrderState as $key => $value): ?>
            <a href="<?= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'state'=>$key])?>" <?php if ($state != '' && $key == $state) echo 'class="layui-this"'; ?> ><?= $value?></a>
        <?php endforeach;?>
    </span>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>编号</th>
            <th>状态</th>
            <th>责任人</th>
            <th>内容</th>
            <th>发起人</th>
            <th>创建时间</th>
            <?php if ($canView || $canDelete):?>
                <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><?= \yii\helpers\Html::encode($item['order_id'])?></td>
                <td><?php
                    if (isset($workOrderState[$item['state']])) {
                        echo $workOrderState[$item['state']];
                    } else {
                        echo '--';
                    }
                    ?>
                </td>
                <td><?php
                    if ($item['worker']) {
                        echo $item['worker']['realname'] . '(' . $item['worker']['username'] . ')';
                    } else {
                        echo '--';
                    }
                    ?>
                </td>
                <td><?= \yii\helpers\Html::encode($item['content'])?></td>
                <td><?php
                    //\yii\helpers\Html::encode($item['user_id'])
                    if ($item['user_id'] == 0) {
                        echo '系统';
                    } else if (isset($item['user'])) {
                        echo $item['user']['realname'].'('.$item['user']['username'].')';
                    } else {
                        echo '--';
                    }
                    ?>
                </td>
                <td><?= date('Y-m-d H:i:s', $item['created_at'])?></td>
                <?php if ($canView || $canDelete):?>
                    <td>
                        <?php if ($canView):?>
                            <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['view', 'id' => $item['order_id']])?>" ">查看</a>
                        <?php endif;?>
                        <?php if ($canDelete):?>
                            <button class="layui-btn layui-btn-primary layui-btn-small delete-item" data-id="<?= $item['order_id']?>">删除</button>
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
