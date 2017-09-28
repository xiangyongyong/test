<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
$log_types = Yii::$app->systemConfig->getValue('LOG_TYPE_LIST', []);
// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 当前组
$type = Yii::$app->request->get('type');
?>
<form action="" method="get">
    <div class="layui-input-inline" style="width: 300px;">
        <input type="text" name="keyword" required lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
    </div>
    <button class="layui-btn layui-btn-normal">搜索</button>

    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
        <a href="<?= \yii\helpers\Url::toRoute([''])?>" <?php if ('' == $type) echo 'class="layui-this"';?> >全部</a>
            <?php foreach ($log_types as $key => $value): ?>
                <a href="<?= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'type'=>$key])?>" <?php if ($key == $type) echo 'class="layui-this"'; ?> ><?= $value?></a>
            <?php endforeach;?>
    </span>
</form>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>操作日志</h5>
    </div>
    <div class="ibox-content">
        <div class="feed-activity-list">
            <?php foreach ($logs as $log):?>
                <div class="feed-element">

                    <!--<div>
                        <strong></strong>
                        <div class="comment word-break">
                            <span class="label layui-bg-green">
                                <?php
/*                                if (isset($log_types[$log['type']])) {
                                    echo $log_types[$log['type']];
                                } else {
                                    echo \yii\helpers\Html::encode($log['type']);
                                }
                                */?>
                            </span>&nbsp;<?/*= \yii\helpers\Html::encode($log['content']) */?></div>
                        <small class="text-muted"><?/*= date('Y-m-d H:i:s', $log['add_time'])*/?></small>
                        <small class="text-muted"><?/*= \yii\helpers\Html::encode($log['ip']) */?></small>
                        <small class="text-muted"><?php /*if (isset($log['user'])) echo $log['user']['realname']; else echo '系统';*/?></small>
                    </div>-->


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
                        <div class="comment word-break">
                                {<?php
                                if (isset($log_types[$log['type']])) {
                                    echo $log_types[$log['type']];
                                } else {
                                    echo \yii\helpers\Html::encode($log['type']);
                                }
                                ?>}

                            <?= \yii\helpers\Html::encode($log['content'])?>
                        </div>
                    </div>

                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
    'pagination' => $pagination,
]) ?>

