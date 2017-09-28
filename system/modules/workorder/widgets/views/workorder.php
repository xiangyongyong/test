<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/4/6
 * Time: 下午2:51
 */

?>


<div id="workOrderBox" style="display: none;">
    <div style="margin: 15px;">
        <form class="layui-form" action="<?= \yii\helpers\Url::toRoute(['/workorder/default/add'])?>">
            <input type="hidden" name="target_id" value="<?= $data['targetId']?>" />
            <div class="layui-form-item">
                <label class="layui-form-label">目标</label>
                <div class="layui-input-block form-input-text">
                    <?= $data['targetName']?>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">问题描述</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入问题描述" class="layui-textarea" name="content" id="content" lay-verify="required"></textarea>
                </div>
            </div>
            <button lay-filter="add" lay-submit style="display: none;"></button>
        </form>
    </div>
</div>

<script type="text/javascript">
    var form;
    layui.use(['layer', 'form'], function() {
        var $ = layui.jquery,
            layer = layui.layer;
        form = layui.form();

        // 创建新工单
        var addBoxIndex = -1;
        $('.addWorkOrder').on('click', function() {
            if(addBoxIndex !== -1)
                return;
            addBoxIndex = layer.open({
                type: 1,
                title: '发起工单',
                content: $("#workOrderBox"),
                btn: ['保存', '取消'],
                shade: false,
                offset: ['100px', '30%'],
                area: ['450px', '300px'],
                //zIndex: 19950924,
                //maxmin: true,
                yes: function(index) {
                    //触发表单的提交事件
                    $('form.layui-form').find('button[lay-filter=add]').click();
                },
                success: function(layero, index) {
                    //弹出窗口成功后渲染表单
                    var form = layui.form();
                    form.render();
                    form.on('submit(add)', function(data) {
                        //console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
                        $.post('<?= \yii\helpers\Url::toRoute(['/workorder/default/add'])?>', data.field, function (res) {
                            //console.log(res);
                            var res = JSON.parse(res);
                            if (res.code == 0) {
                                layer.closeAll();
                                $("#content").val('');
                                addBoxIndex = -1;
                            }
                            layer.msg(res.message);
                        });

                        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                    });
                    //console.log(layero, index);
                },
                end: function() {
                    addBoxIndex = -1;
                }
            });
        });

    });

</script>