//单选形式
var setting = {
    view: {
        //autoCancelSelected: false,
        //selectedMulti: true,
        showLine: true
    },
    // 数据使用JSON数据
    data: {
        simpleData: {
            enable: true,
            idKey: 'id',
            pIdKey: 'pid'
        }
    },
    check: {
        //enable: true
    },
    // 强行异步加载父节点的子节点
    async: {
        enable: true,
        type: 'get',
        dataType: "text",
        autoParam: ['id', 'name', 'pid'],
        url: ztree_get_url
    },
    callback: {
        onAsyncSuccess: onAsyncSuccess,
        onClick:zTreeOnSelect
    }
};

var zNodes = null;

//获取ztree对象
var zTree;
//异步加载完毕后的回调
var firstAsyncSuccessFlag = 0;
function onAsyncSuccess(event, treeId, msg) {
    if (firstAsyncSuccessFlag == 0) {
        try {
            //展开全部节点
            expandAllNodes(zTree.getNodes());
            //展开第一个结点
            /*var selectedNode = zTree.getSelectedNodes();
            var nodes = zTree.getNodes();
            zTree.expandNode(nodes[0], true);*/
            firstAsyncSuccessFlag = 1;

            //currentZTreeId 当前选中的id  单个id
            /*var currentNode = zTree.getNodeByParam("id", currentZTreeId, null);
            zTree.selectNode(currentNode);
            zTreeOnSelect('', '', currentNode);*/

            // 当前选中的节点，多个节点
            if(typeof currentZTreeId != 'undefined' && currentZTreeId != ''){
                var zTreeIds = currentZTreeId.split(",");
                //console.log(zTreeIds);
                for (i=0; i<zTreeIds.length; i++)
                {
                    var currentNode = zTree.getNodeByParam("id", zTreeIds[i], null);
                    zTree.selectNode(currentNode);
                    zTreeOnSelect('', '', currentNode);
                }
            }
        } catch (err) {

        }
    }
}
// 展开全部节点
function expandAllNodes(nodes) {
    if (!nodes) return;
    for (var i=0, l=nodes.length; i<l; i++) {
        zTree.expandNode(nodes[i], true, false, false);
        if (nodes[i].isParent && nodes[i].zAsync) {
            expandAllNodes(nodes[i].children);
        }
    }
}
//监听点击事件
/*var zTreeOnSelect = function (event, treeId, treeNode){
    console.log('js文件中监听到了');
};*/
// 选中节点
/*function zTreeOnSelect(event, treeId, treeNode) {
    $("#zTreeSelect").html(treeNode.id+'：'+treeNode.name);
    $("#zTreeId").val(treeNode.id);
}*/
// 创建tree
function createTree(domId) {
    $.fn.zTree.init($("#"+domId), setting, zNodes);
    zTree = $.fn.zTree.getZTreeObj(domId);
}