<?php

namespace system\modules\stats\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tab_stats_env".
 *
 * @property integer $id
 * @property integer $time
 * @property integer $gateway_id
 * @property integer $temperature
 * @property integer $humidity
 * @property integer $vibration
 * @property integer $total
 */
class StatsEnv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_stats_env';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'gateway_id', 'vibration', 'total'], 'integer'],
            [['temperature', 'humidity'], 'double']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '流水id',
            'time' => '统计时间戳',
            'gateway_id' => '网关id',
            'temperature' => '平均温度',
            'humidity' => '平均湿度',
            'vibration' => '平均震动次数',
            'total' => '统计数量',
        ];
    }

    /**
     * 根据网关id和时间段获取对应的 温度，湿度，震动次数的数据
     * @param $gateway_id
     * @param null $from
     * @param null $to
     * @return mixed
     */
    public static function getDataByGateway($gateway_id, $from = null, $to = null)
    {
        // 默认从今天开始
        if (is_null($from)) {
            $from = strtotime(date("Ymd 00:00:00"));
        }

        // 默认24小时小时内的数据
        if (is_null($to)) {
            $to = $from+86400-1;
        }

        $envData = self::find()
            ->select(['id', 'time', 'gateway_id', 'temperature', 'humidity', 'vibration'])
            ->where(['gateway_id' => $gateway_id])
            ->andWhere(['>=', 'time', $from])
            ->andWhere(['<=', 'time', $to])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();

        //echo '<pre>'; print_r($envData);exit;

//        $newEnvData['time'] = ArrayHelper::getColumn($envData, 'time'); // 时间
//        $newEnvData['temperature'] = ArrayHelper::getColumn($envData, 'temperature'); // 温度
//        $newEnvData['humidity'] = ArrayHelper::getColumn($envData, 'humidity');  // 湿度
//        $newEnvData['vibration'] = ArrayHelper::getColumn($envData, 'vibration');  // 震动次数

        /**
         * 返回的数据格式如下：temperature => [
         *  [time, 数据],
         *  [time, 数据]
         * ]
         */

        $newEnvData = [];
        foreach ($envData as $item) {
            $newEnvData['temperature'][] = [$item['time'].'000', $item['temperature']];
            $newEnvData['humidity'][] = [$item['time'].'000', $item['humidity']];
            $newEnvData['vibration'][] = [$item['time'].'000', $item['vibration']];
        }

        //echo '<pre>';print_r($newEnvData);exit;

        return $newEnvData;
    }
}
