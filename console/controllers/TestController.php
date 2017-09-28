<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/28
 * Time: 下午4:46
 */

namespace console\controllers;


use system\modules\stats\models\StatsEnv;
use system\modules\stats\models\StatsPort;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionEnv()
    {
        // 将env中的数据统计出来
        // 先把网关id统计出来，然后按照网关id对数据进行整理
        $sql = "SELECT 
            gateway_id,
            UNIX_TIMESTAMP(hour) as time,
            total,
            alltemperature/total as temperature, 
            allhumidity/total as humidity,
            allvibration/total as vibration 
            FROM (
                SELECT 
                gateway_id,
                FROM_UNIXTIME(`add_time`, '%Y-%m-%d %H:00:00') as hour, 
                count(*) as total , 
                SUM(temperature) as alltemperature, 
                SUM(humidity) as allhumidity, 
                SUM(vibration) as allvibration
                FROM tab_env 
                WHERE gateway_id = 5
                group by hour
            ) t";
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        //print_r($data);exit;
        foreach ($data as $item) {
            $item['temperature'] = sprintf("%.1f", $item['temperature']); // 保留一位小数
            $item['humidity'] = sprintf("%.1f", $item['humidity']);       // 保留一位小数
            $item['vibration'] = ceil($item['vibration']);              // 向上取整

            $model = new StatsEnv();
            if ($model->load($item, '') && $model->save()) {

            } else {
                break;
            }
        }

    }

    public function actionPort()
    {
        // 将env中的数据统计出来
        // 先把网关id统计出来，然后按照网关id对数据进行整理
        $sql = "SELECT 
            UNIX_TIMESTAMP(hour) as time,
            gateway_id,
            if_port,
            total,
            round(allpkgnum/total, 2) as pkg_num, 
            round(allbytes/total, 2) as bytes, 
            round(allpps/total, 2) as pps, 
            round(allbandwidth/total, 2) as bandwidth
            FROM (
                SELECT FROM_UNIXTIME(`add_time`, '%Y-%m-%d %H:00:00') as hour, gateway_id, if_port, 
                count(*) as total , 
                SUM(pkg_num) as allpkgnum, 
                SUM(bytes) as allbytes, 
                SUM(pps) as allpps, 
                SUM(bandwidth) as allbandwidth 
                FROM tab_port_info
                WHERE gateway_id = 4 
                group by hour,if_port
            ) t";
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        //print_r($data);exit;AND add_time>=1490716800 AND add_time < 1490803200
        foreach ($data as $item) {
            /*$item['temperature'] = sprintf("%.1f", $item['temperature']); // 保留一位小数
            $item['humidity'] = sprintf("%.1f", $item['humidity']);       // 保留一位小数
            $item['vibration'] = ceil($item['vibration']);              // 向上取整*/

            $model = new StatsPort();
            if ($model->load($item, '') && $model->save()) {

            } else {
                break;
            }
        }

    }



}