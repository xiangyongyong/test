<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/4/6
 * Time: 上午11:05
 */

namespace system\modules\stats\controllers;


class GatewayController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEnv()
    {
        return $this->render('env');
    }

    public function actionPort()
    {
        return $this->render('port');
    }
}