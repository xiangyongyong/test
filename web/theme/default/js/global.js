/**
 * Created by ligang on 2017/3/13.
 * deleteUrl 删除的基础url，需要在模板中定义
 */
var deleteUrl = '';
var $, layer, form; // 设置为全局变量
layui.use(['form', 'element'], function() {
    $ = layui.jquery,
        layer = layui.layer, //获取当前窗口的layer对象
        form = layui.form();

    // tip小提示
    $('.system-tip').on('mouseenter', function () {
        //console.log(this);
        layer.tips($(this).attr('data-tip'), this, {
            tips: [2, '#a2a2a2'],
            time: 30000
        });
    }).on('mouseleave', function () {
        layer.closeAll('tips');
    });

    // 闪屏消息
    $.each(flashMsgs, function(index, value){
        layer.msg(value);
    });

    // 公共方法：删除项目
    $('.delete-item').on('click', function () {
        var id = $(this).attr('data-id');
        layer.confirm('确认删除此记录？', {
            btn: ['删除','取消'] //按钮
        }, function(){
            $.get(deleteUrl+id, function (res) {
                var data = JSON.parse(res);
                if (data.code == 0) {
                    layer.msg(data.message, {
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        location.reload(); //刷新当前页面
                    });
                } else {
                    layer.msg(data.message);
                }
            });
        }, function(){

        });
    });
});

// 打开一个tab
function openUrl(url, title, icon, refresh) {
    //这是核心的代码。
    parent.tab.tabAdd({
        href: url, //地址
        icon: icon,
        title: title,
        refresh: refresh
    });
}