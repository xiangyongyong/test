<?php
/* @var $model \system\modules\workorder\models\WorkOrder */

// 权限
$canAdd = Yii::$app->user->can('workorder/default/add');
$canEdit = Yii::$app->user->can('workorder/default/edit');

$state_list = Yii::$app->systemConfig->getValue('WORD_ORDER_STATE_LIST', []);

?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">工单列表</a></li>
        <li><a href="<?= \yii\helpers\Url::toRoute('my')?>">我的工单</a></li>
        <li class="layui-this">查看工单</li>
    </ul>
</div>

<div class="row">
    <div class="col-lg-8 pull-left">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox-head layui-clear">
                            <h2 style="font-weight: lighter;"><?php
                                if ($model->gateway) {
                                    echo '网关'.$model->gateway->gateway_id.'-'.$model->gateway->gateway_name;
                                }
                                ?></h2>
                        </div>
                        <dl class="dl-horizontal mt10">
                            <dt>状态：</dt>
                            <dd>
                                <?php if ($model->notify->userNotify->is_read == 0):?>
                                    <div class="label layui-bg-orange">未读</div>
                                <?php else:?>
                                    <div class="label layui-bg-green">已读</div>
                                <?php endif;?>

                                <?php if ($model->state == 0):?>
                                    <div class="label layui-bg-orange"><?= $state_list[$model->state]?></div>
                                <?php elseif($model->state == 1):?>
                                    <div class="label layui-bg-blue"><?= $state_list[$model->state]?></div>
                                <?php elseif($model->state == 2):?>
                                    <div class="label layui-bg-green"><?= $state_list[$model->state]?></div>
                                <?php elseif($model->state == 3):?>
                                    <div class="label bg-gray"><?= $state_list[$model->state]?></div>
                                <?php endif;?>
                            </dd>
                        </dl>
                        <div class="row layui-clear">
                            <div class="col-lg-5 pull-left">
                                <dl class="dl-horizontal">
                                    <dt>责任人：</dt>
                                    <dd><?php
                                        if ($model->worker) {
                                            //echo $model->worker->realname; //.'-'.$model->worker->username;
                                            echo "<span class='system-tip' data-tip='{$model->worker->realname}<br>{$model->worker->username}'><img class='avatar-mini img-circle' src='{$model->worker->avatar}' alt='{$model->worker->realname}' /> " . $model->worker->realname . '</span> ';
                                        } else {
                                            echo '--';
                                        }
                                        ?></dd>
                                    <dt>发起人：</dt>
                                    <dd><?php
                                        if ($model->user_id == 0) {
                                            echo '系统';
                                        }
                                        else if ($model->user) {
                                            //echo $model->user->realname; //.'-'.$model->user->username;
                                            echo "<span class='system-tip' data-tip='{$model->user->realname}<br>{$model->user->username}'><img class='avatar-mini img-circle' src='{$model->user->avatar}' alt='{$model->user->realname}' /> " . $model->user->realname . '</span> ';
                                        } else {
                                            echo '--';
                                        }
                                        ?></dd>
                                </dl>
                            </div>
                            <div class="col-lg-7 pull-left" id="cluster_info">
                                <dl class="dl-horizontal">

                                    <dt>最后更新：</dt>
                                    <dd><?= date('Y-m-d H:i:s', $model->update_at)?></dd>
                                    <dt>创建于：</dt>
                                    <dd><?= date('Y-m-d H:i:s', $model->created_at)?></dd>
                                </dl>
                            </div>
                            <div class="col-lg-12 pull-left">
                                <dl class="dl-horizontal">
                                    <dt>描述：</dt>
                                    <dd><?= $model->content;?></dd>
                                    <?php if ($model->state == 2 || $model->state == 3): ?>
                                    <dt>完成时间：</dt>
                                    <dd><?= date('Y-m-d H:i:s', $model->finished_at)?></dd>
                                    <dt>处理意见：</dt>
                                    <dd><?= $model->finished_remark;?></dd>
                                    <?php endif; ?>
                                </dl>
                            </div>

                        </div>
                    </div>
                </div>

                <?php if ($model->state == 0 || $model->state == 1):?>
                <div class="row">
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                        <legend>处理工单</legend>
                    </fieldset>
                    <form class="layui-form" name="workorder" method="post" action="" style="margin-right: 30px;">
                        <input name="<?= Yii::$app->request->csrfParam?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

                        <div class="col-lg-12">

                            <?php if ($canEdit): ?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">更改责任人</label>
                                    <div class="layui-input-inline">
                                        <select name="worker_id"  lay-verify="" lay-search>
                                            <?php foreach ($users as $k => $v):?>
                                                <option value="<?= $k?>" <?php if ($k == $model->worker_id) echo 'selected="selected"'; ?> > <?= $v['realname'].'-'.$v['username']?></option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>
                                    <div class="layui-input-inline form-input-text"">
                                        <button class="layui-btn" name="action" value="changeWorker" lay-submit lay-filter="changeWorker">更改</button>
                                        <a class="layui-btn layui-btn-warm" href="<?= \yii\helpers\Url::toRoute(['urge','order_id' => $model->order_id, 'gateway_desc' => $model->gateway->gateway_desc, 'worker_id' => $model->worker_id])?>" name="" value="" lay-submit lay-filter="">催单(<?=$model->urge_num?>)</a>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">故障原因</label>
                                    <div class="layui-input-inline">
                                        <select name="problem"  lay-verify="" lay-search>
                                            <?php foreach (yii::$app->params['problemVal'] as $k => $v):?>
                                                <option value="<?= $k?>" <?php if ($k == $model->problem) echo 'selected="selected"'; ?> ><?=$v?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="layui-input-inline form-input-text"">
                                        <button class="layui-btn" name="action" value="setProblem" lay-submit lay-filter="changeWorker">更改</button>
                                    </div>
                                </div>


                            <?php endif;?>

                            <div class="layui-form-item" id="remarkDiv"  style="display: none;">
                                <label class="layui-form-label">处理意见</label>
                                <div class="layui-input-block">
                                    <textarea name="finish_remark" id="finish_remark" placeholder="处理意见必填" class="layui-textarea"></textarea>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label"></label>
                                <div class="layui-input-block form-input-text"">
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

                        </div>
                    </form>
                    <div class="ibox-content bt-none">
                        <?php foreach ($model->urge as $item): ?>
                        <div class="feed-element">
                            <?=$item->content?>
                            <?=date('Y-m-d H:i:s', $item->created_at)?>
                            <?php if ($item->userNotify->is_read == 0):?>
                                <div class="label layui-bg-orange">未读</div>
                            <?php else:?>
                                <div class="label layui-bg-green">已读</div>
                            <?php endif;?>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php endif;?>


                <div class="row">
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px; margin-bottom: 0;">
                        <legend>沟通记录</legend>
                    </fieldset>
                    <div class="ibox-content bt-none">
                        <div class="feed-activity-list">
                            <?php foreach ($comment as $item): ?>
                            <div class="feed-element">
                                <div class="avatar">
                                    <div class="avatar-box">
                                        <?php
                                        if ($item['user']) {
                                            echo "<img class='avatar-mini img-circle' src='{$item['user']['avatar']}' /> ";
                                        } else {
                                            echo "<img class='avatar-mini img-circle' src='/upload/avatar/default/system.png' /> ";
                                        }
                                        ?>
                                    </div>
                                    <p class="name"><?= $item['user']['realname']?></p>
                                    <small class="text-muted"><?= date('Y-m-d H:i:s', $item['create_at'])?></small>
                                </div>
                                <div class="comment-box">
                                    <div class="comment"><?= $item['content']?></div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>

                        <div style="padding-top: 20px;">
                            <form class="layui-form" method="post" action="<?= \yii\helpers\Url::toRoute(['/main/comment/add'])?>" style="margin-right: 30px;">
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
            </div>
        </div>
    </div>
    <div class="col-lg-4 pull-left">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>操作日志</h5>
            </div>
            <div class="ibox-content">

                <div class="feed-activity-list">
                    <?php foreach ($logs as $log):?>
                    <div class="feed-element">

                        <div class="avatar">
                            <div class="avatar-box">
                                <?php
                                if ($log['user']) {
                                    echo "<img class='avatar-mini img-circle' src='{$log['user']['avatar']}' /> ";
                                } else {
                                    echo "<img class='avatar-mini img-circle' src='/upload/avatar/default/system.png' /> ";
                                }
                                ?>
                            </div>
                            <p class="name"><?= $log['user']['realname'] ?: '系统'?></p>
                            <small class="text-muted"><?= date('Y-m-d H:i:s', $log['add_time'])?></small>
                            <small class="text-muted"><?= $log['ip']?></small>
                        </div>
                        <div class="comment-box">
                            <div class="comment"><?= $log['content']?></div>
                        </div>

                    </div>
                    <?php endforeach;?>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    layui.use(['form', 'element', 'laydate'], function() {
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
        form.on('submit(changeWorker)', function(data){
            if ($('#remarkDiv').css('display') == 'block') {
                $("#remarkDiv").show();
                $("#finish_remark").prop("required", false);
                $("#finish_remark").prop('lay-filter', false);
                form.render();
                //return false;//阻止表单跳转。如果需要表单跳转，去掉这段即可。
            }
        });
    });
</script>
