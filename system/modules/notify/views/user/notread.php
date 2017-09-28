<a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute('/notify/user/my')?>', '我的消息', 'fa fa-bell-o', true);" class="admin-header-user">
    <i class="fa fa-bell-o" aria-hidden="true"></i> 消息 <?php if ($count>0): ?><span class="badge layui-bg-red"><?= $count?></span><?php endif;?>
    <span class="layui-nav-more"></span>
</a>
<dl class="layui-nav-child" style="width: 220px;">
    <?php if ($count == 0):?>
        <dd>
            <a href="javascript:;"> -没有新通知- </a>
        </dd>
    <?php else:?>
        <?php foreach ($data as $item):?>
            <dd>
                <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute('/notify/user/my')?>', '我的消息', 'fa fa-bell-o', true);"> <?= $item['notify']['content']?></a>
            </dd>
        <?php endforeach;?>
    <?php endif;?>
</dl>