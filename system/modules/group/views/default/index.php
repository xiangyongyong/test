<?php

// 加载静态文件
\system\modules\group\assets\ZTreeGroupEditAsset::register($this);

$this->registerJs("
    //是否有编辑权限
    var canUpdate = ". Yii::$app->user->can('group/default/update') .";
    //获取组数据的url
    var ztree_get_url = '".\yii\helpers\Url::to(['/group/default/ajax'])."';
    //保存组数据的url
    var ztree_ajax_url = '".\yii\helpers\Url::to(['/group/default/save'])."';
    //更新组数据的url
    var ztree_update_url = '".\yii\helpers\Url::to(['/group/default/update'])."';
", yii\web\View::POS_BEGIN);
?>

<blockquote class="layui-elem-quote">
    <h4>注意事项：</h4>
    <p>修改组会实时保存；</p>
    <p>删除组时：如果其下有子组织，则无法删除节点</p>
</blockquote>

<div class="row layui-clear">

    <div class="col-lg-10 pull-left">
        <ul id="treeStruct" class="ztree col-lg-12"></ul>
    </div>

    <div class="col-lg-2 pull-left"></div>

</div>