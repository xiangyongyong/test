<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:13
 */
// 搜索关键字
$keyword = Yii::$app->request->get('keyword');
// 当前状态
//$state = Yii::$app->request->get('state', '');
// 状态组
$stategroup = Yii::$app->request->get('stategroup', '');

// 组列表数组
$workOrderState = Yii::$app->systemConfig->getValue('WORD_ORDER_STATE_LIST', []);

// 权限判断
//$canAdd = Yii::$app->user->can('workorder/default/add');
$canView = Yii::$app->user->can('workorder/default/view');
//$canDelete = Yii::$app->user->can('workorder/default/delete');

$path = Yii::$app->request->pathInfo;
//echo $path;

?>

<div class="layui-tab layui-tab-brief">
    <ul class="layui-tab-title">
        <?php if ($path == 'workorder/default/index'):?>
        <li class="layui-this">全部工单</li>
        <li><a href="<?= \yii\helpers\Url::toRoute('my')?>">我的工单</a></li>
        <?php elseif ($path == 'workorder/default/my'): ?>
            <li><a href="<?= \yii\helpers\Url::toRoute('index')?>">全部工单</a></li>
            <li class="layui-this">我的工单</li>
        <?php endif;?>
    </ul>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox bd-none">
            <div class="ibox-content">

                <form action="" method="get">
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="text" name="keyword" placeholder="请输入" autocomplete="off" class="layui-input" value="<?= $keyword?>">
                    </div>
                    <button class="layui-btn layui-btn-normal">搜索</button>

                    <!--<span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
                        <a href="<?/*= \yii\helpers\Url::toRoute([''])*/?>" <?php /*if ('' === $state) echo 'class="layui-this"';*/?> >全部</a>
                                        <?php /*foreach ($workOrderState as $key => $value): */?>
                                            <a href="<?/*= \yii\helpers\Url::toRoute(['', 'keyword' => $keyword, 'state'=>$key])*/?>" <?php /*if ($state != '' && $key == $state) echo 'class="layui-this"'; */?> ><?/*= $value*/?></a>
                                        <?php /*endforeach;*/?>
                    </span>-->

                    <span class="layui-breadcrumb" lay-separator="|" style="margin-left: 30px;">
                        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 0])?>" <?php if ('' != $stategroup && 0 == $stategroup) echo 'class="layui-this"';?> >全部</a>
                        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 1])?>" <?php if ('' === $stategroup || 1 == $stategroup) echo 'class="layui-this"';?> >待处理</a>
                        <a href="<?= \yii\helpers\Url::toRoute(['', 'stategroup' => 2])?>" <?php if (2 == $stategroup) echo 'class="layui-this"';?> >已处理</a>
                    </span>
                </form>

                <div class="project-list">
                    <table class="layui-table" lay-skin="nob">
                        <colgroup>
                            <col width="100">
                            <col width="100">
                            <col>
                            <col width="200">
                            <col width="100">
                        </colgroup>
                        <tbody>
                        <?php foreach ($data as $item): ?>
                        <tr>
                            <td class="project-status"><?= $item['order_id']?></td>
                            <td class="project-status">
                                <?php if ($item['state'] == 0):?>
                                    <div class="label layui-bg-orange"><?= $workOrderState[$item['state']]?></div>
                                <?php elseif($item['state'] == 1):?>
                                    <div class="label layui-bg-blue"><?= $workOrderState[$item['state']]?></div>
                                <?php elseif($item['state'] == 2):?>
                                    <div class="label layui-bg-green"><?= $workOrderState[$item['state']]?></div>
                                <?php elseif($item['state'] == 3):?>
                                    <div class="label bg-gray"><?= $workOrderState[$item['state']]?></div>
                                <?php endif;?>
                            </td>
                            <td class="project-title">
                                <a href="<?= \yii\helpers\Url::toRoute(['view', 'id' => $item['order_id']])?>" "><?= \yii\helpers\Html::encode($item['content'])?></a>
                                <br/>
                                <small class="text-muted">
                                    <?php
                                    if ($item['user_id'] == 0) {
                                        echo '系统';
                                    } else if (isset($item['user'])) {
                                        //echo $item['user']['realname'].'('.$item['user']['username'].')';
                                        echo $item['user']['realname'];
                                    } else {
                                        echo '--';
                                    }
                                    ?> 创建于 <?= date('Y-m-d H:i:s', $item['created_at'])?></small>
                            </td>
                            <td class="project-completion">
                                <?php
                                if ($item['worker']) {
                                    //echo $item['worker']['realname']; // . '(' . $item['worker']['username'] . ')';
                                    echo "<div class='system-tip' data-tip='{$item['worker']['realname']}<br>{$item['worker']['username']}'><img class='avatar-mini img-circle' src='{$item['worker']['avatar']}' alt='{$item['worker']['realname']}' /> " . $item['worker']['realname'].'</div>';
                                } else {
                                    echo '--';
                                }
                                ?>
                            </td>
                            <?php if ($canView):?>
                                <td class="project-actions">
                                    <?php if ($canView):?>
                                        <a class="layui-btn layui-btn-primary layui-btn-small" href="<?= \yii\helpers\Url::toRoute(['view', 'id' => $item['order_id']])?>" ">查看</a>
                                    <?php endif;?>
                                </td>
                            <?php endif;?>
                        </tr>
                        <?php endforeach;?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= \system\widgets\MyPaginationWidget::widget([
    'pagination' => $pagination,
])?>
