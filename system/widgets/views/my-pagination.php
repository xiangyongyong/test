<?php
/* @var $contId string 分页的容器id  */
/* @var $pagination \yii\data\Pagination 数据分页类  */

// 生成url
$params = Yii::$app->request->getQueryParams();
if (isset($params['page'])) unset($params['page']);
$params[0] = Yii::$app->controller->getRoute();
$url = Yii::$app->urlManager->createUrl($params);
if (strpos($url, '?') === false) {
    $url .= '?';
} else {
    $url .= '&';
}
//echo $url;
?>

<div>总计：<?= $pagination->totalCount?>条记录 共<?= $pagination->getPageCount()?>页，每页显示<?= $pagination->defaultPageSize?>条</div>
<div id="<?= $contId?>" class="page"></div>

<script type="text/javascript">
    layui.use(['laypage', 'layer'], function() {
        var laypage = layui.laypage
            , layer = layui.layer;

        laypage({
            cont: '<?= $contId?>' // 分页容器
            , curr: <?= $pagination->page+1 ?> // 当前页码
            , pages: <?= $pagination->getPageCount()?> //总页数
            , groups: 5 //连续显示分页数
            //, skip: true // 是否显示快速跳转
            , jump: function (obj, first) {
                // 如果不是首页，点击后跳转
                if (!first) {
                    location.href = '<?= $url?>page='+obj.curr;
                }
            }
        });
    });
</script>