<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午1:49
 */

/* @var $model \system\modules\main\models\Config*/

?>

<form class="layui-form" method="post" action="" style="margin-right: 300px;">
    <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">厂商名称</label>
        <div class="layui-input-block">
            <input type="text" name="factory_name" lay-verify="required|config_name" autocomplete="off" placeholder="请输入" class="layui-input" value="<?= $model->factory_name?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">联系人</label>
        <div class="layui-input-block">
            <input type="text" name="name" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $model->name?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">联系电话</label>
        <div class="layui-input-block">
            <input type="text" name="telephone" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $model->telephone?>">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    layui.use(['form', 'element'], function() {
        var $ = layui.jquery,
            layer = layui.layer, //获取当前窗口的layer对象
            form = layui.form();
        //自定义验证规则
        form.verify({
            config_name: function(value) {
                var message;
                $.ajax({
                    type : "get",
                    url : '<?= \yii\helpers\Url::toRoute(['', 'action'=> 'name-exit', 'id' => $model->factory_id, 'factory_name' => ''])?>'+value,
                    async : false,
                    success : function(res){
                        res = eval("(" + res + ")");
                        if (res.code == 1) {
                            //a = 'hello';
                            message = res.message;
                        }
                    }
                });
                return message;
            }
        });
    });
</script>
