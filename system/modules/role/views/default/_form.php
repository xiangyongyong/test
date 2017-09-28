<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午1:49
 */

/* @var $model \system\modules\role\models\AuthRole*/

$isEdit = false;
if (!$model->isNewRecord) {
    $isEdit = true;
}

// 加载所有的菜单权限
$navBar = Yii::$app->params['navBar'];

// 用户当前拥有的权限
$model->permission = $model->permission ?: [];

// 用户
$isEdit = !$model->isNewRecord;

?>
<form class="layui-form" method="post" action="" style="margin-right: 30px;">
    <div class="row">
        <div class="col-lg-9 pull-left">
            <div style="margin-right: 50px;">
                <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="layui-form-item">
                    <label class="layui-form-label">角色名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" lay-verify="required|config_name" autocomplete="off" placeholder="不能重复" class="layui-input" value="<?= $model->name?>">
                    </div>
                </div>

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">角色说明</label>
                    <div class="layui-input-block">
                        <textarea name="description" placeholder="请输入内容" class="layui-textarea"><?= $model->description?></textarea>
                        <div class="layui-form-mid layui-word-aux"></div>
                    </div>
                </div>

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">分配权限</label>

                    <?php foreach ($navBar as $key1 => $value1):?>
                        <div class="layui-input-block">
                            <!--主菜单-->
                            <div class="checked">
                                <input type="checkbox" name="permission[]" lay-skin="primary" title="<?= $value1['title']?>" value="<?= $value1['href']?>" <?php if (in_array($value1['href'], $model->permission)) {echo 'checked=""';} ?> lay-filter="allChoose">
                            </div>
                            <?php if (isset($value1['children'])): ?>
                                <div style="margin-left: 50px;" class="checked-box">
                                    <?php foreach ($value1['children'] as $key2 => $value2): ?>
                                        <!--二级菜单-->
                                        <div class="checked2">
                                            <input type="checkbox" name="permission[]" lay-skin="primary" title="<?= $value2['title']?>" value="<?= $value2['href']?>" <?php if (in_array($value2['href'], $model->permission)) {echo 'checked=""';} ?> lay-filter="allChoose2">
                                            <!--三级权限-->
                                            <?php if (isset($value2['childItem'])):?>
                                                <div style="margin-left: 50px;" class="checked-child">
                                                    <?php foreach ($value2['childItem'] as $key3 => $value3):?>
                                                        <input type="checkbox" name="permission[]" lay-skin="primary" title="<?= $value3?>" value="<?= $key3?>" <?php if (in_array($key3, $model->permission)) {echo 'checked=""';} ?> lay-filter="allChoose3">
                                                    <?php endforeach;?>
                                                </div>
                                            <?php endif;?>

                                        </div>

                                    <?php endforeach; ?>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endforeach;?>

                    <!--<div class="layui-input-block">
                        <div>
                            <input type="checkbox" name="like1[write]" lay-skin="primary" title="主菜单1" checked="">
                        </div>
                        <div>
                            <input type="checkbox" name="like1[write]" lay-skin="primary" title="主菜单2" checked="">
                        </div>

                        <div style="margin-left: 50px;">
                            <div>
                                <input type="checkbox" name="like1[write]" lay-skin="primary" title="子菜单1" checked="">
                            </div>

                            <div style="margin-left: 50px;">
                                <input type="checkbox" name="like1[write]" lay-skin="primary" title="子菜单权限1" checked="">
                                <input type="checkbox" name="like1[read]" lay-skin="primary" title="子菜单权限2">
                                <input type="checkbox" name="like1[game]" lay-skin="primary" title="子菜单权限3" disabled="">
                            </div>
                            <div>
                                <input type="checkbox" name="like1[read]" lay-skin="primary" title="子菜单2">
                            </div>
                            <div>
                                <input type="checkbox" name="like1[game]" lay-skin="primary" title="子菜单3">
                            </div>
                        </div>
                    </div>-->
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 pull-left">
            成员：
            <div>
                <?php foreach ($user as $key => $value):?>
                    <?php
                    if (isset($allUser[$value])) {
                        $data = $allUser[$value];
                    } else {
                        continue;
                    }
                    ?>
                    <div class='system-tip' data-tip='<?=$data['realname'].'<br>'.$data['username']?>'>
                        <input type="checkbox" name="users[]" value="<?= $data['user_id']?>" lay-skin="primary" title="<img class='avatar-mini2 img-circle' src='<?= $data['avatar']?>'> <?= $data['realname']?>" checked="checked">
                    </div>
                <?php endforeach;?>
                <?php foreach ($allUser as $k => $v):?>
                    <?php if (in_array($k, $user)) continue;?>
                    <div class='system-tip' data-tip='<?=$v['realname'].'<br>'.$v['username']?>'>
                        <input type="checkbox" name="users[]" value="<?= $v['user_id']?>" lay-skin="primary" title="<img class='avatar-mini2 img-circle' src='<?= $v['avatar']?>'> <?= $v['realname']?>">
                    </div>
                <?php endforeach;?>
            </div>
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
                    url : '<?= \yii\helpers\Url::toRoute(['', 'action'=> 'name-exit', 'id' => $model->role_id, 'name' => ''])?>'+value,
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


        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('.layui-input-block').find('.checked-box :checkbox');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        form.on('checkbox(allChoose2)', function(data){
            var child2 = $(data.elem).parent('.checked2').find('.checked-child :checkbox');
            var test = $(data.elem).parent().parent().parent().find(".checked :checkbox");
            test.prop("checked",true);
            child2.each(function(index, item){
                item.checked = data.elem.checked;
                //console.log('item-3',item);
            });
            form.render('checkbox');
        });
        form.on('checkbox(allChoose3)', function(data){
            var parent = $(data.elem).parent().parent().children().eq(0);
            var parents = $(data.elem).parent().parent().parent().parent().find(".checked :checkbox");
            //console.log(parents);
            parent.prop("checked",true);
            parents.prop("checked",true);
            form.render('checkbox');
        });

    });
</script>
