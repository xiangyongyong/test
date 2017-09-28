<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:12
 */

namespace system\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class BaseController extends Controller
{

    //关闭csrf验证的方法
    public $disableCsrfAction = [];

    // 基础忽略列表，列表中不做权限验证
    public $ignoreList = [
        'main/default/index', // 布局页面
        'main/default/welcome', //欢迎页面
        'main/default/error', // 错误页面
        'main/comment/add', // 评论页面
        'user/info/update', // 个人信息
        'user/info/password', // 修改密码
        'main/attach/upload-avatar', // 上传头像
        'notify/user/my', // 我的消息
        'notify/user/notread', // 未读消息
        'workorder/default/add', // 增加工单
    ];

    // 依赖检查，如果有值的权限，那么可以放行，key是权限，值是依赖的权限，满足一个即可放行
    public $dependIgnoreList = [
        /*//上传头像，如果有user/base/add或者user/base/edit的权限，那么不需要检测user/base/upload-avatar的权限
        'user/base/upload-avatar' => [
            'user/base/add',
            'user/base/edit',
        ],*/
        //获取组织结构
//        'group/default/ajax' => [
//            'group/default/index', //组织结构首页
//            'gateway/gateway/edit', // 网关编辑
//        ],

    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // 必须是登录状态
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied by default
                ],
            ],
        ];
    }

    /**
     * 在程序执行之前，对访问的方法进行权限验证.
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        // 判断是否可以访问
        if (!$this->checkAccess()) {
            throw new ForbiddenHttpException('当前IP：'.Yii::$app->request->getUserIP().'不允许访问系统，请联系管理员！');
        }

        // 关闭csrf验证
        if (in_array($action->id, $this->disableCsrfAction)) {
            $this->enableCsrfValidation = false;
        }

        if (parent::beforeAction($action)) {
            //如果未登录，则直接返回
            if(Yii::$app->user->isGuest){
                //$this->goHome();
                //Yii::$app->end();
                $this->redirect(Yii::$app->user->loginUrl);
                Yii::$app->end();
                return false;
            }

            //获取路径
            $path = Yii::$app->request->pathInfo ?: Yii::$app->defaultRoute;
            //忽略列表
            if (in_array($path, $this->ignoreList)) {
                return true;
            }

            //忽略依赖列表
            if(array_key_exists($path, $this->dependIgnoreList)){
                $dependArr = $this->dependIgnoreList[$path];
                foreach ($dependArr as $onePath) {
                    if($this->can($onePath)){
                        return true;
                    }
                }
            }

            if ($this->can($path)) {
                return true;
            } else {
                throw new ForbiddenHttpException('没有权限访问');
            }
        }

        return false;
    }

    // 简单调用系统自带的can方法进行校验
    private function can($path)
    {
        if (Yii::$app->user->can($path)) {
            return true;
        }
        return false;
    }

    /**
     * ajax返回
     * @param $data
     */
    public function ajaxReturn($data)
    {
        echo Json::encode($data);
        exit;
    }

    // 闪屏消息，在桌面停留3s钟后自动消失
    public function flashMsg($type, $message)
    {
        \Yii::$app->getSession()->setFlash($type, $message);
    }

    /**
     * 判断当前用户IP是否允许访问
     * @return bool if access is granted
     */
    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP(); // 用户ip
        $forbidden = Yii::$app->systemConfig->getValue('SYSTEM_FORBIDDEN_IP', []); // 禁止ip 优先级更改
        $allow = Yii::$app->systemConfig->getValue('SYSTEM_ALLOW_IP', []);  // 允许ip

        //echo $ip; print_r($forbidden);print_r($allow);exit;

        // 如果在禁止ip内，那么直接返回false
        foreach ($forbidden as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return false;
            }
        }

        // 如果允许ip为空，那么返回true
        if (empty($allow)) {
            return true;
        }

        foreach ($allow as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }

        return false;
    }



}