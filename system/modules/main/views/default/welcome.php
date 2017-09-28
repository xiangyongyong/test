<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/16
 * Time: 下午5:05
 */

?>


<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>欢迎登录系统</legend>
</fieldset>

<div class="row layui-clear">

    <div class="col-lg-4 col-lg-offset-4 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-commred">
                <i class="fa fa-calendar-check-o" aria-hidden="true" style="font-size: 8em;"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/workorder/default/my'])?>', '我的工单', 'fa fa-tasks');" style="color: #ff0000;">
                    <h1 style="font-size: 4em;"><?= $data['workorder']?></h1>
                    <span style="font-size: 2em;">异常信息</span>
                </a>
            </div>
        </div>
    </div>

    <!--<div class="col-lg-2 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-orange">
                <i class="fa fa-user-o" aria-hidden="true"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:;" data-title="用户总量">
                    <h1>10</h1>
                    <span>用户总量</span>
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-2 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-yellow">
                <i class="fa fa-user-o" aria-hidden="true"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:;" data-title="用户总量">
                    <h1>10</h1>
                    <span>用户总量</span>
                </a>
            </div>
        </div>
    </div>-->
</div>

<div class="row layui-clear">

    <div class="col-lg-3 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-blue">
                <i class="fa fa-laptop" aria-hidden="true"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/gateway/gateway/index'])?>', '网关管理', 'fa fa-laptop');">
                    <h1><?= $data['gateway']?></h1>
                    <span>网关数量</span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-yellow-green">
                <i class="fa fa-server" aria-hidden="true"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/gateway/device/index'])?>', '设备管理', 'fa fa-server');">
                    <h1><?= $data['device']?></h1>
                    <span>设备总数</span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 pull-left">
        <div class="panel">
            <div class="symbol bgcolor-dark-green">
                <i class="fa fa-users" aria-hidden="true"></i>
            </div>
            <div class="value tab-menu">
                <a href="javascript:openUrl('<?= \yii\helpers\Url::toRoute(['/user/manage/index'])?>', '用户管理', 'fa fa-users');">
                    <h1><?= $data['user']?></h1>
                    <span>用户总量</span>
                </a>
            </div>
        </div>
    </div>

</div>

<div class="row" style="display: none;">
    <div class="col-lg-6 pull-left">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>产品信息</h5>
            </div>
            <div class="ibox-content">
                <div class="ibox-content no-padding">

                    <table class="layui-table margin0" lay-even lay-skin="nob">
                        <colgroup>
                            <col width="100">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td class="text-r bold">系统名称</td>
                            <td class="c666"> 智能机箱管理系统</td>
                        </tr>
                        <tr>
                            <td class="text-r bold">系统版本</td>
                            <td class="c666 word-break">2.0.0</td>
                        </tr>
                        <tr>
                            <td class="text-r bold">发布日期</td>
                            <td class="c666">2017-01-01</td>
                        </tr>
                        <tr>
                            <td class="text-r bold">开发者</td>
                            <td class="c666">xx科技有限公司</td>
                        </tr>
                        <tr>
                            <td class="text-r bold">服务器环境</td>
                            <td class="c666">Linux</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 pull-left">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>系统公告</h5>
            </div>
            <div class="ibox-content">
                <ul>请各位登录用户做好以下几点工作：
                    <li>1. 点击右上角的个人资料页面，查看个人信息是否正确，如果不正确，请务必进行修改，特别是姓名和手机号码，需要随时随地可以联系到的手机号码；</li>
                    <li>2. 在个人资料页面中，上传自己的个人头像图片；</li>
                    <li>3. 初次登录系统以后请务必修改原始密码，同时建议定期修改个人密码；</li>
                    <li>4. 由于权限不同，要管理的内容也不太一样，所以如果出现没有权限访问等字样不用担心；</li>
                    <li>5. 如果确实需要某些页面的访问权限，请联系管理员进行授权；</li>
                    <li>6. 如果离开电脑，请务必登出账号，以免出现安全隐患；</li>
                </ul>


                <!--<p>智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台</p>
                <p>智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，智能机箱管理平台，</p>
                <p>智能机箱管理平台，智能机箱管理平台，</p>
                <p>智能机箱管理平台，智能机箱管理平台，</p>
                <p>智能机箱管理平台，智能机箱管理平台，智能机箱管理平台</p>
                <p>智能机箱管理平台，智能机箱管理平台，</p>
                <p>智能机箱管理平台，智能机箱管理平台，智能机箱管理平台</p>
                <p>智能机箱管理平台，智能机箱管理平台，</p>-->
            </div>
        </div>
    </div>
</div>
