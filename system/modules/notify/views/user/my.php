<?php

$state = Yii::$app->request->get('state');
?>
<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">

        <?php if (!$state): ?>
            <li class="layui-this">未读消息</li>
            <li><a href="<?= \yii\helpers\Url::toRoute(['', 'state' => 'all'])?>">全部消息</a></li>
        <?php elseif ($state == 'all'): ?>
            <li><a href="<?= \yii\helpers\Url::toRoute([''])?>">未读消息</a></li>
            <li class="layui-this">全部消息</a></li>
        <?php endif;?>

    </ul>
</div>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>我的消息</h5>
        <div class="ibox-tools pull-right">
            <button class="layui-btn layui-btn-mini readAll"><i class="layui-icon">&#xe605;</i>全部标记为已读</button>
        </div>
    </div>
    <div class="ibox-content">

        <div class="feed-activity-list">
            <?php foreach ($data as $item):?>
                <?php if (!isset($item['notify'])) {
                    continue;
                }
                $notify = $item['notify'];
                ?>
                <div class="feed-element" id="notifyId<?= $item['id'] ?>">

                    <div class="avatar">
                        <div class="avatar-box">
                            <?php
                            if ($notify['sender']) {
                                echo "<img class='avatar-mini img-circle' src='{$notify['sender']['avatar']}' /> ";
                            } else {
                                echo "<img class='avatar-mini img-circle' src='/upload/avatar/default/system.png' /> ";
                            }
                            ?>
                        </div>
                        <p class="name"><?= $notify['sender']['realname'] ?: '系统'?></p>
                        <small class="text-muted"><?= date('Y-m-d H:i:s', $notify['created_at'])?></small>
                    </div>
                    <div class="comment-box">
                        <div class="comment word-break">
                            <?= \yii\helpers\Html::encode($notify['content'])?> &nbsp;&nbsp;
                            <?php if ($item['is_read'] == 0):?>
                                <button class="layui-btn layui-btn-mini changeToRead" data-id="<?= $item['id']?>">标记为已读</button>
                            <?php else:?>
                                <button class="layui-btn layui-btn-primary layui-btn-mini"><i class="layui-icon">&#xe605;</i>已读</button>
                            <?php endif;?>
                        </div>
                    </div>

                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>

<script type="text/javascript">
    var form;
    layui.use(['layer', 'form'], function() {
        var $ = layui.jquery,
            layer = layui.layer;

        // 扫描状态更改
        $(".changeToRead").on('click', function () {
            //$(this).addClass('layui-btn-primary').removeClass('changeToRead').html('<i class="layui-icon">&#xe605;</i>已读');
            //return false;
            var that = $(this);
            var id = that.attr('data-id');
            $.get('<?= \yii\helpers\Url::toRoute(['', 'ajax' => 'changeToRead', 'id' => ''])?>'+id, function (res) {
                var res = JSON.parse(res);
                if (res.code == 0) {
                    that.addClass('layui-btn-primary').removeClass('changeToRead').html('<i class="layui-icon">&#xe605;</i>已读');
                }
                layer.msg(res.message);
            });
        });

        // 扫描状态更改
        $(".readAll").on('click', function () {
            $.get('<?= \yii\helpers\Url::toRoute(['', 'ajax' => 'readAll'])?>', function (res) {
                var res = JSON.parse(res);
                if (res.code == 0) {
                    layer.msg(res.message, {
                        //icon: 1,
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        window.location.reload();
                    });
                } else {
                    layer.msg(res.message);
                }

            });
        });

    });
</script>
