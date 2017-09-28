<?php
/* @var $model \system\modules\workorder\models\WorkOrder */

$canAdd = Yii::$app->user->can('workorder/default/add');

$state_list = Yii::$app->systemConfig->getValue('WORD_ORDER_STATE_LIST', []);
?>
<!--<div class="layui-progress layui-progress-big" lay-showPercent="yes">
    <div class="layui-progress-bar layui-bg-green" lay-percent="50%"></div>
</div>-->

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">工单列表</a></li>
        <?php if ($canAdd):?><li><a href="<?= \yii\helpers\Url::toRoute('add')?>">新增工单</a></li><?php endif;?>
        <li class="layui-this">查看工单</li>
    </ul>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>工单信息</h5>
    </div>
    <div class="ibox-content">
        <form class="layui-form" name="workorder" method="post" action="" style="margin-right: 300px;">
            <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

            <div class="layui-form-item">
                <label class="layui-form-label">创建于</label>
                <div class="layui-input-block" style="line-height: 38px;"><?= date('Y-m-d H:i:s', $model->created_at)?></div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">网关</label>
                <div class="layui-input-block" style="line-height: 38px;">
                    <?php
                    if ($model->gateway) {
                        echo '网关'.$model->gateway->gateway_id.'-'.$model->gateway->gateway_name;
                    }
                    ?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline" style="line-height: 38px;">
                    <?php
                    if (isset($state_list[$model->state])) {
                        echo $state_list[$model->state];
                    } else {
                        echo '--';
                    }
                    ?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">发起人</label>
                <div class="layui-input-block" style="line-height: 38px;">
                    <?php
                    if ($model->user_id == 0) {
                        echo '系统';
                    }
                    else if ($model->user) {
                        echo $model->user->realname.'-'.$model->user->username;
                    }
                    ?>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">责任人</label>
                    <div class="layui-input-inline"  style="line-height: 38px;">
                        <?php
                        if ($model->worker) {
                            echo $model->worker->realname.'-'.$model->worker->username;
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">问题描述</label>
                <div class="layui-input-block" style="line-height: 38px;">
                    <?= $model->content;?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">更新时间</label>
                <div class="layui-input-block" style="line-height: 38px;">
                    <?= date('Y-m-d H:i:s', $model->update_at)?>
                </div>
            </div>

            <?php if ($model->state == 2 || $model->state == 3): ?>

                <div class="layui-form-item">
                    <label class="layui-form-label">完成时间</label>
                    <div class="layui-input-block" style="line-height: 38px;">
                        <?= date('Y-m-d H:i:s', $model->finished_at)?>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">完成备注</label>
                    <div class="layui-input-block" style="line-height: 38px;">
                        <?= $model->finished_remark;?>
                    </div>
                </div>

            <?php endif; ?>

            <?php if ($model->state == 0 || $model->state == 1): ?>
            <div class="layui-form-item">
                <label class="layui-form-label">更改责任人</label>
                <div class="layui-input-inline">
                    <select name="worker_id"  lay-verify="" lay-search>
                        <?php foreach ($users as $k => $v):?>
                            <option value="<?= $k?>" <?php if ($k == $model->worker_id) echo 'selected="selected"'; ?> > <?= $v['realname'].'-'.$v['username']?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
                <div class="layui-input-inline"  style="line-height: 38px;">
                    <button class="layui-btn" name="action" value="changeWorker">更改</button>
                </div>
            </div>
            <?php endif;?>

            <div class="layui-form-item" id="remarkDiv"  style="display: none;">
                <label class="layui-form-label">处理意见</label>
                <div class="layui-input-block">
                    <textarea name="finish_remark" id="finish_remark" placeholder="处理意见必填" class="layui-textarea"></textarea>
                </div>
            </div>

            <?php if ($model->state == 0 || $model->state == 1):?>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block" style="line-height: 38px;">
                    <?php if ($model->state == 0): ?>
                        <button class="layui-btn" lay-submit name="action" value="handler">开始处理这个工单</button>
                    <?php endif;?>

                    <?php if ($model->state == 0 || $model->state == 1): ?>
                        <button class="layui-btn layui-btn-warm" name="action" value="close" lay-submit lay-filter="showRemark">关闭此工单</button>
                    <?php endif; ?>

                    <?php if ($model->state == 1): ?>
                    <button class="layui-btn" name="action" value="resolve" lay-submit lay-filter="showRemark" >完成此工单</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif;?>

        </form>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>沟通记录</h5>
    </div>
    <div class="ibox-content">
        <div>
            <?php foreach ($comment as $item): ?>
                <div>
                    <?php
                        if ($item['user']) {
                            echo $item['user']['realname'];
                        }
                    ?>:
                </div>
                <div><?= $item['content']?><br /><?= date('Y-m-d H:i:s', $item['create_at'])?></div>
                <hr>
            <?php endforeach;?>
        </div>

        <div>
            <form class="layui-form" method="post" action="<?= \yii\helpers\Url::toRoute(['/main/comment/add'])?>" style="margin-right: 300px;">
                <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="target_type" value="workorder">
                <input type="hidden" name="target_id" value="<?= $model->order_id?>">
                <div class="layui-form-item">
                    <textarea name="content" placeholder="请输入内容" class="layui-textarea" required lay-filter="required"></textarea>
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn" lay-submit="">立即提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    layui.use(['form', 'element'], function() {
        var form = layui.form(), $ = layui.jquery;
        form.on('submit(showRemark)', function(data){
            if ($('#remarkDiv').css('display') == 'none') {
                $("#remarkDiv").show();
                $("#finish_remark").prop("required", true);
                $("#finish_remark").prop('lay-filter', 'required');
                form.render();
                return false;//阻止表单跳转。如果需要表单跳转，去掉这段即可。
            }
        });
    });
</script>



