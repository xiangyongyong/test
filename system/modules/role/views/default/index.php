<?php
/**
 * 用户列表
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */

// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 用户状态列表
$user_status_list = Yii::$app->systemConfig->getValue('USER_STATUS_LIST', []);

// 当前组
$status = Yii::$app->request->get('status', '');
// 权限判断
$canAdd = Yii::$app->user->can('role/default/add');
$canEdit = Yii::$app->user->can('role/default/edit');
$canDelete = Yii::$app->user->can('role/default/delete');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">角色列表</li>
        <?php if ($canAdd):?><li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增角色</a></li><?php endif;?>
    </ul>
    <!--<div class="layui-tab-content"></div>-->
</div>

<form action="" method="get">

    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>
    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>">全部</a>
    </span>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>描述</th>
            <th>成员</th>
            <?php if ($canEdit || $canDelete):?>
                <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><?= \yii\helpers\Html::encode($item['role_id'])?></td>
                <td><?= \yii\helpers\Html::encode($item['name'])?></td>
                <td><?php
                    if ($item['description']) {
                        echo '<div class="system-tip" data-tip="'. \yii\helpers\Html::encode($item['description']) .'">'.\yii\helpers\Html::encode(\yii\helpers\StringHelper::truncate($item['description'], 10)).'</div>';
                    } else {
                        echo '--';
                    }
                    ?></td>
                <td><?php
                    if (isset($users[$item['role_id']])) {
                        foreach ($users[$item['role_id']] as $user) {
                            //echo $user['realname'].'('.$user['username'].')；';
                            echo "<span class='system-tip' data-tip='{$user['realname']}<br>{$user['username']}'><img class='avatar-mini img-circle' src='{$user['avatar']}' alt='{$user['realname']}' /> " . $user['realname'].'</span> ';
                        }
                    }
                    ?></td>
                <?php if ($canEdit || $canDelete):?>
                <td>
                    <?php if ($canEdit):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['role_id']])?>" ">编辑</a>
                    <?php endif;?>
                    <?php if ($canDelete):?>
                    <button class="layui-btn layui-btn-primary layui-btn-small delete-item" data-id="<?= $item['role_id']?>">删除</button>
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
