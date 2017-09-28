<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/17
 * Time: 下午2:12
 */

namespace system\modules\gateway\controllers;


class StatisticsController extends BaseController
{
    // @todo 环境信息统计分析
    public function actionEnv()
    {
        return $this->render('index');
    }
}