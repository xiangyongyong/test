<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午5:18
 */
$this->title = '编辑角色';
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">角色列表</a></li>
        <li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增角色</a></li>
        <li class="layui-this">修改角色</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<?= $this->render('_form', [
    'model' => $model,
    'user' => $user,
    'allUser' => $allUser,
]);?>