var setting = {
    view: {
        selectedMulti: false,
        addHoverDom: addHoverDom,
        removeHoverDom: removeHoverDom,
        showLine: true
    },
    check: {
        //enable: true
    },
    // 数据使用JSON数据
    data: {
        simpleData: {
            enable: true,
            idKey: 'id',
            pIdKey: 'pid'
        }
    },
    // 强行异步加载父节点的子节点
    async: {
        enable: true,
        type: 'get',
        dataType: "text",
        autoParam: ['id', 'name', 'pid'],
        url: ztree_get_url
    },
    edit: {
        enable: true,
        editNameSelectAll: true,
        showRemoveBtn: showRemoveBtn,
        showRenameBtn: showRenameBtn
    },
    callback: {
        onAsyncSuccess: onAsyncSuccess,
        //beforeEditName: beforeEditName,
        beforeRemove: beforeRemove,
        beforeDrop: beforeDrop,
        onRemove: onRemove,
        onRename: onRename,
        onDrop: onDrop,
        onCheck:false,
        onClick: channelZTreeOnSelect,
    }
};

var zNodes = null;

//获取ztree对象
var zTree, className = "dark", current_group_id;

function showBtn(){
    $("#save").show();
}
// 异步加载完毕-展开第一个节点
function onAsyncSuccess_first(event, treeId, msg) {
    //调用默认展开第一个结点
    var selectedNode = zTree.getSelectedNodes();
    var nodes = zTree.getNodes();
    zTree.expandNode(nodes[0], true);

    /*var childNodes = zTree.transformToArray(nodes[0]);
     zTree.expandNode(childNodes[1], true);*/
    //zTree.selectNode(childNodes[1]);
    //var childNodes1 = zTree.transformToArray(childNodes[1]);
    //zTree.checkNode(childNodes1[1], true, true);

    //加载完毕后，选中某个节点
    if (current_group_id) {
        console.log('节点：', current_group_id );
        var currentNode = zTree.getNodeByParam("id", current_group_id, null);
        zTree.selectNode(currentNode);
    }
}
//异步加载完成事件-展开全部节点
function onAsyncSuccess(event, treeId, msg) {
    console.log('异步加载完成了');
    expandNodes(zTree.getNodes());
    //加载完毕后，选中某个节点
    if (current_group_id) {
        console.log('节点：', current_group_id );
        var currentNode = zTree.getNodeByParam("id", current_group_id, null);
        zTree.selectNode(currentNode);
    }
}
//展开全部节点
function expandNodes(nodes) {
    if (!nodes) return;
    for (var i=0, l=nodes.length; i<l; i++) {
        zTree.expandNode(nodes[i], true, false, false);
        if (nodes[i].isParent && nodes[i].zAsync) {
            expandNodes(nodes[i].children);
        }
    }
}

//初始化ztree
function createTree(domId) {
    $.fn.zTree.init($("#"+domId), setting, zNodes);
    zTree = $.fn.zTree.getZTreeObj(domId);
}

// 实现树形节点高级操作  增删改
function beforeDrag(treeId, treeNodes) {
    return true;
}

//拖拽
function beforeDrop(treeId, treeNodes, targetNode, moveType){
    return !(targetNode == null || (moveType != "inner" && !targetNode.parentTId));
}
function onDrop(event, treeId, treeNodes, targetNode, moveType){
    console.log('拖拽完成:', treeNodes, targetNode, moveType);
    if (moveType) {
        /*var one_bak = {
         type: 'edit',
         id: treeNodes[0].id,
         name: treeNodes[0].name,
         pid: targetNode.id
         };*/
        var one = {
            type: 'drag',
            id: treeNodes[0].id, //操作的id
            target_id: targetNode.id //拖拽到目的id
        };
        saveOne(one);
    }
}

// 编辑
function beforeEditName(treeId, treeNode) {
    className = (className === "dark" ? "":"dark");
    zTree.selectNode(treeNode);
    return confirm("进入节点 -- " + treeNode.name + " 的编辑状态吗？");
}
function onRename(event, treeId, treeNode, isCancel) {
    //整理数据
    //console.log(treeId, treeNode, isCancel);
    //如果编辑已经存在的数据时按了取消键
    if ('path' in treeNode && isCancel) {
        return ;
    }
    //类型：编辑或者添加
    var type = 'path' in treeNode ? 'edit' : 'add';
    var one = {
        type: type,
        id: treeNode.id,
        name: treeNode.name,
        pid: treeNode.pid
    };
    saveOne(one);
}

//保存一条数据
function saveOne(data) {
    //提交服务器
    $.ajax({
        type: "POST",
        url: ztree_update_url,
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        //dataType: "json",
        success: function (message) {
            //console.log('成功了', message);
            //location.reload();
            zTree.reAsyncChildNodes(null, "refresh");
        },
        error: function (data) {
            console.log('保存失败了', data);
            alert('操作失败，请稍后重试');
        }
    });
}

// 删除
function beforeRemove(treeId, treeNode) {
    className = (className === "dark" ? "" : "dark");
    //zTree.selectNode(treeNode);
    if (treeNode.isParent){
        alert('其下还有子节点，不能删除');
        return false;
    }
    return confirm("删除后无法恢复！\n确认要删除--" + treeNode.name + "--吗？");
}
function onRemove(event, treeId, treeNode) {
    var one = {
        type: 'delete',
        id: treeNode.id
    };
    saveOne(one);
}

//监听点击事件
var channelZTreeOnSelect = function (event, treeId, treeNode){

};

//是否显示编辑按钮 canUpdate 是权限
function showRenameBtn(treeId, treeNode) {
    return canUpdate;
}
//是否显示移除按钮
function showRemoveBtn(treeId, treeNode) {
    return canUpdate && treeNode.id!=1;
}

var newCount = 1;
function addHoverDom(treeId, treeNode) {
    var sObj = $("#" + treeNode.tId + "_span");
    if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0) {
        return;
    }
    //是否有编辑组织架构的权限
    if (canUpdate) {
        var addStr = "<span class='button add' id='addBtn_" + treeNode.tId + "' title='add node' onfocus='this.blur();'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_" + treeNode.tId);
        if (btn) {
            btn.bind("click", function () {
                var newNode = zTree.addNodes(treeNode, {id: (100 + newCount), pId: treeNode.id, name: "node" + (newCount++)});
                //console.log(newNode);
                zTree.editName(newNode[0]);
                return false;
            });
        }
    }

}

function removeHoverDom(treeId, treeNode) {
    $("#addBtn_" + treeNode.tId).unbind().remove();
}

//保存数据
var newZTreeData = [];
//保存更改的数据
function saveAll(){
    var zTreeObj = zTree.getNodes();
    newZTreeData = [];
    getArr(zTreeObj);
    //提交服务器
    $.ajax({
        type: "POST",
        url: ztree_ajax_url,
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(newZTreeData),
        dataType: "json",
        success: function (message) {
            location.reload();
        },
        error: function (message) {
        }
    });
}
//从对象中获取数据
function getArr(zTreeObj){
    $.each(zTreeObj, function(n, obj){
        var one = {
            id:obj.id,
            name:obj.name,
            tId:obj.tId,
            parentTId:obj.parentTId
        };
        //判断是否新数据
        one.isNew = 'status' in obj ? 0 : 1;
        newZTreeData.push(one);
        //如果存在子数组，递归
        if('children' in obj && obj.children.length>0){
            getArr(obj.children);
        }
    });
}

$(document).ready(function () {
    createTree("treeStruct");
});