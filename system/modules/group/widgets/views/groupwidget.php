<?php
/* @var $this \yii\web\View*/
use yii\helpers\Html;
\system\modules\group\assets\ZTreeGroupSelectAsset::register($this);
?>

<?= Html::hiddenInput($inputName, $group_id, [
    'id' => 'zTreeId'.$id,
]) ?>
<div>当前选择的是：<span id="zTreeSelect<?= $id?>" style="color:#009688;"></span></div>
<div <?= $divOption?>>
    <ul id="<?= $id?>" class="ztree"></ul>
</div>

<script type="text/javascript">
    <?php $this->beginBlock('beginJs');?>
    // 组数据
    //声明ztree当前选中的id
    var currentZTreeId = '<?= $group_id?>';
    // 获取数据的url
    var ztree_get_url = '<?= \yii\helpers\Url::toRoute($getUrl) ?>';

    <?php if ($onSelect): ?>

        var zTreeOnSelect = <?= $onSelect?>;

    <?php else: ?>

        <?php if (!$isMulti):?>
            var zTreeOnSelect = function(event, treeId, treeNode) {
                $("#zTreeSelect<?= $id?>").html(treeNode.id + ':' + treeNode.name);
                $("#zTreeId<?= $id?>").val(treeNode.id);
                //console.log('页面中监听到了');
            };
        <?php else:?>
            //已经选中的tid
            var selectNodes = [];
            var zTreeOnSelect = function(event, treeId, treeNode) {
                //如果点击的treeId已经存在，那么取消选中
                var idIndex = $.inArray(treeNode.id, selectNodes);
                if(idIndex > -1){
                    selectNodes.splice(idIndex, 1);
                }
                //如果不存在，那么选中
                else{
                    selectNodes.push(treeNode.id);
                }
                //保存已选中的名称
                var selectNameOfNode = [];
                //进行选择操作
                if(selectNodes.length>0){
                    $.each(selectNodes, function(n, id){
                        var node = zTree.getNodeByParam('id', id, null);
                        //将数据保存在名称数组中
                        selectNameOfNode.push(node.id + ':' + node.name);
                        if(n==0){
                            zTree.selectNode(node);
                        }else{
                            // @todo 选择多个时会导致页面滚动，待修正
                            zTree.selectNode(node, true);
                        }
                    });
                }
                //取消选择
                else{
                    selectNameOfNode = [];
                    zTree.cancelSelectedNode(treeNode);
                }
                //console.log('页面中监听到了');
                //数据更新到input和页面中
                $("#zTreeId<?= $id?>").val(selectNodes);
                $("#zTreeSelect<?= $id?>").html(selectNameOfNode.join('，'));
            };
        <?php endif;?>

    <?php endif;?>

    <?php $this->endBlock()?>
    <?php $this->registerJs($this->blocks['beginJs'], \yii\web\View::POS_BEGIN)?>

    <?php $this->beginBlock('endJs');?>
    createTree('<?= $id?>');
    <?php $this->endBlock()?>

    <?php $this->registerJs($this->blocks['endJs'], \yii\web\View::POS_END)?>
</script>


