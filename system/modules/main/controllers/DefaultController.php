<?php

namespace system\modules\main\controllers;
use system\modules\gateway\models\Device;
use system\modules\gateway\models\Gateway;
use system\modules\user\models\User;
use system\modules\workorder\models\WorkOrder;

/**
 * 默认控制器，负责：主布局，默认的错误页面，欢迎页面
 */
class DefaultController extends BaseController
{


    /**
     * 主页布局
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = '@system/views/layouts/main-frame';
        return $this->render('index');
    }

    /**
     * 欢迎页面，包含基本系统信息
     * @return string
     */
    public function actionWelcome()
    {
        //$this->layout = '@system/views/layouts/main';
        $userCount = User::find()->count();
        $gatewayCount = Gateway::find()->count();
        $deviceCount = Device::find()->count();
        $workorderCount = WorkOrder::find()->where(['worker_id' => \Yii::$app->user->identity->getId(), 'state' => WorkOrder::$STATE_NOT_FINISH])->count();
        return $this->render('welcome', [
            'data' => [
                'user' => $userCount,
                'gateway' => $gatewayCount,
                'device' => $deviceCount,
                'workorder' => $workorderCount,
            ],
        ]);
    }

}