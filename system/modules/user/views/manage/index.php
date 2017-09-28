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
$canAdd = Yii::$app->user->can('user/manage/add');
$canEdit = Yii::$app->user->can('user/manage/edit');
$canDelete = Yii::$app->user->can('user/manage/delete');
$canBindgroup = Yii::$app->user->can('user/manage/bindgroup');
?>


<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li class="layui-this">用户列表</li>
        <?php if ($canAdd):?>
            <li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增用户</a></li>
        <?php endif;?>
    </ul>
</div>

<form action="" method="get">

    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>

    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>" <?php if ('' === $status) echo 'class="layui-this"';?> >全部</a>
        <?php foreach ($user_status_list as $key => $value): ?>
        <a href="<?= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'status'=>$key])?>" <?php if ($status != '' && $key == $status) echo 'class="layui-this"'; ?> ><?= $value?></a>
        <?php endforeach;?>
    </span>
</form>

<div class="layui-form">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>头像</th>
            <!--<th>用户名</th>
            <th>姓名</th>-->
            <th>联系方式</th>
            <th>状态</th>
            <th width="400">绑定组</th>
            <?php if ($canEdit || $canDelete || $canBindgroup):?>
            <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><?= \yii\helpers\Html::encode($item['user_id'])?></td>
                <td>
                    <div class="pull-left"><img class="avatar-small img-circle" src="<?= $item['avatar']?>" /></div>
                    <div class="pull-left" style="padding-left:8px; padding-top: 10px; line-height: 20px;">
                        <?= \yii\helpers\Html::encode($item['username'])?><br /><?= \yii\helpers\Html::encode($item['realname'])?>
                    </div>
                </td>
                <!--<td></td>
                <td></td>-->
                <td>
                    <div class="pull-left" style="padding-left:5px; padding-top: 10px; line-height: 20px;">
                        <?= \yii\helpers\Html::encode($item['phone'])?> <br />
                        <?= \yii\helpers\Html::encode($item['email'])?>
                    </div>
                </td>
                <td><?php
                    if (isset($user_status_list[$item['status']])) {
                        echo \yii\helpers\Html::encode($user_status_list[$item['status']]);
                    } else {
                        echo '--';
                    }
                    ?>
                </td>
                <td class="word-break">
                    <?php
                        if ($item['gatewaygroup']) {
                            $groups = \yii\helpers\ArrayHelper::getColumn($item['gatewaygroup'], 'group_id');
                            echo \system\modules\group\models\Group::getNamePathByGroups($groups);
                        } else {
                            echo '--';
                        }
                    ?>
                </td>
                <?php if ($canEdit || $canDelete || $canBindgroup):?>
                <td>
                    <?php if ($canBindgroup):?>
                    <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['bindgroup', 'id' => $item['user_id']])?>" ">绑定组</a>
                    <?php endif;?>
                    <?php if ($canEdit):?>
                        <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['edit', 'id' => $item['user_id']])?>" ">编辑</a>
                    <?php endif;?>
                    <?php if ($canDelete):?>
                    <button class="layui-btn layui-btn-primary layui-btn-small delete-item" data-id="<?= $item['user_id']?>">删除</button>
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