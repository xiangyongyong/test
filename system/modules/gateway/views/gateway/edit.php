<?php
/* @var $model \system\modules\gateway\models\Gateway */

$this->title = '修改网关';
// 网关的扫描状态列表
$is_study_list = Yii::$app->systemConfig->getValue('GATEWAY_IS_STUDY', []);
$is_study = Yii::$app->redis->hget("hash:gateway:{$model->gateway_id}", 'is_study');
?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">网关列表</a></li>
        <li class="layui-this">修改网关</li>
    </ul>
    <div class="layui-tab-content"></div>
</div>

<form class="layui-form" method="post" action="" style="margin-right: 300px;">
    <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

    <div class="layui-form-item">
        <label class="layui-form-label">网关ID</label>
        <div class="layui-input-block form-input-text""><?= $model->gateway_id ?></div>
    </div>

    <!--<div class="layui-form-item">
        <label class="layui-form-label">网关名称</label>
        <div class="layui-input-block">
            <input type="text" name="gateway_name" lay-verify="required|config_name" autocomplete="off" placeholder="请输入" class="layui-input" value="<?php /*= $model->gateway_name*/?>">
        </div>
    </div>-->

    <div class="layui-form-item">
        <label class="layui-form-label">网关描述</label>
        <div class="layui-input-block">
            <textarea name="gateway_desc" placeholder="请输入内容" autocomplete="off" class="layui-textarea"><?= $model->gateway_desc?></textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">Mac</label>
        <div class="layui-input-block form-input-text""><?= $model->mac
            ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">IP</label>
        <div class="layui-input-block form-input-text""><?= $model->ip ?></div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">是否扫描</label>
        <div class="layui-input-block form-input-text"">
            <?php
            if (isset($is_study_list[$is_study])) {
                echo $is_study_list[$is_study];
            } else {
                echo $is_study;
            }
            ?>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">所属组</label>
        <div class="layui-input-block form-input-text"">
            <div>
                <?php echo \system\modules\group\widgets\GroupWidget::widget([
                        'group_id' => $model->group_id,
                ])?>
            </div>
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
                    url : '<?= \yii\helpers\Url::toRoute(['', 'action'=> 'name-exit', 'id' => $model->gateway_id, 'gateway_name' => ''])?>'+value,
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
