<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午5:18
 */
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">配置列表</a></li>
        <li class="layui-this">新增配置</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<?= $this->render('_form', [
        'model' => $model,
]);?>