<?php

namespace system\modules\gateway\models;

use system\modules\stats\models\StatsEnv;
use Yii;

/**
 * This is the model class for table "{{%tab_env}}".
 *
 * @property integer $env_id
 * @property integer $gateway_id
 * @property integer $temperature
 * @property integer $humidity
 * @property string $location
 * @property integer $vibration
 * @property integer $add_time
 * @property integer $group_id
 */
class Env extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_env}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gateway_id', 'temperature', 'humidity', 'vibration', 'add_time', 'group_id'], 'integer'],
            [['location'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'env_id' => 'ID号',
            'gateway_id' => '网关ID',
            'temperature' => '温度',
            'humidity' => '湿度',
            'location' => '位置信息',
            'vibration' => '震动次数',
            'add_time' => '添加时间',
            'group_id' => '组ID',
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // @todo 更新网关的状态；
        $gatewayModel = Gateway::findOne($this->gateway_id);
        $gatewayModel->data_update_at = $this->add_time;
        // 将离线的设备 设置为正常状态；
        if ($gatewayModel->state == Gateway::STATE_OFFLINE) {
            $gatewayModel->state = Gateway::STATE_NORMAL;
        }
        $gatewayModel->save();

        // 进行统计，根据时间判断当前时间应该放到哪个时间段内，时间肯定在一个小时内，比如60，那么
        //echo $current_timezone = date_default_timezone_get();
        // 当前的分钟数
        $time = $this->_getTime($this->add_time);

        $statsModel = StatsEnv::find()->where(['gateway_id' => $this->gateway_id, 'time' => $time])->one();
        if (!$statsModel) {
            $statsModel = new StatsEnv();
            $statsModel->gateway_id = $this->gateway_id;
            $statsModel->time = $time;
        }

        // 重新计算温度，湿度，震动 ，温度和湿度用一位小数保存，震动保存是整数
        $statsModel->temperature = sprintf("%.1f", ($statsModel->temperature + $this->temperature)/2);
        $statsModel->humidity = sprintf("%.1f", ($statsModel->humidity + $this->humidity)/2);
        $statsModel->vibration = ceil(($statsModel->vibration + $this->vibration)/2);
        $statsModel->total += 1; // 统计数+1

        $statsModel->save();
    }

    /**
     * 根据给定的时间和时间间隔计算出应该属于哪个时间
     * @param $time
     * @return false|int
     */
    private function _getTime($time)
    {
        // 统计时间间隔，必须大于10，小于60
        $stats_interval = Yii::$app->systemConfig->getValue('STATS_INTERVAL_GATEWAY_ENV', 60);
        // 如果超过60分钟，那么按照60来计算
        if ($stats_interval > 60) {
            $stats_interval = 60;
        } else if ($stats_interval < 10) {
            $stats_interval = 10;
        }

        $minute = date('i', $time) + 0; // 转换为int，并且将08转换为8的形式
        $currentDate = date('Y-m-d H:00:00', $time); // 默认显示当前小时
        for ($i=0; $i<60/$stats_interval; $i++) {
            if ($i*$stats_interval <= $minute && ($i+1)*$stats_interval > $minute) {
                $currentMinute = $i * $stats_interval;
                $currentDate = date('Y-m-d H:'.$currentMinute.':00', $time);
            }
        }
        return strtotime($currentDate);
    }


}
