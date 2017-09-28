<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午1:49
 */

/* @var $model \system\modules\main\models\Config*/

// 组列表数组
$groups = Yii::$app->systemConfig->getValue('CONFIG_GROUP_LIST', []);
$types = Yii::$app->systemConfig->getValue('CONFIG_TYPE_LIST', []);

?>


<form class="layui-form" method="post" action="" style="margin-right: 300px;">
    <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">配置标识</label>
        <div class="layui-input-block">
            <input type="text" name="name" lay-verify="required|config_name" autocomplete="off" placeholder="用户程序调用，只能使用英文且不能重复" class="layui-input" value="<?= $model->name?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">配置标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $model->title?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-inline">
            <input type="text" name="sort" lay-verify="number" autocomplete="off" class="layui-input"  value="<?php if ($model->sort != ''){ echo $model->sort; } else{ echo 0; }  ?>">
        </div>
        <div class="layui-form-mid layui-word-aux">用于分组显示的顺序，数字越大越靠前</div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">配置类型</label>
        <div class="layui-input-inline">
            <select name="type">
                <?php foreach ($types as $k => $v):?>
                    <option value="<?= $k?>" <?php if ($k == $model->type) echo 'selected="selected"'; ?> ><?= $v?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux">系统会根据不同类型解析配置值</div>
    </div>

    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">配置分组</label>
            <div class="layui-input-inline">
                <select name="group">
                    <option value="0">不分组</option>
                    <?php foreach ($groups as $k => $v):?>
                        <option value="<?= $k?>" <?php if ($k == $model->group) echo 'selected="selected"'; ?> ><?= $v?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">配置分组 用于批量设置 不分组则不会显示在系统设置中</div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">配置值</label>
        <div class="layui-input-block">
            <textarea name="value" placeholder="请输入内容" class="layui-textarea"><?= $model->value?></textarea>
            <div class="layui-form-mid layui-word-aux">如果是数组，每行一组数据，格式：组id=组名称，比如：1=开启</div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">配置项</label>
        <div class="layui-input-block">
            <textarea name="extra" placeholder="请输入内容" class="layui-textarea"><?= $model->extra?></textarea>
            <div class="layui-form-mid layui-word-aux">如果是枚举型 需要配置该项，格式同数组</div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">说明</label>
        <div class="layui-input-block">
            <textarea name="remark" placeholder="请输入内容" class="layui-textarea"><?= $model->remark?></textarea>
            <div class="layui-form-mid layui-word-aux">配置详细说明，会显示在界面上给用户进行配置</div>
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
                    url : '<?= \yii\helpers\Url::toRoute(['', 'action'=> 'name-exit', 'id' => $model->id, 'name' => ''])?>'+value,
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
