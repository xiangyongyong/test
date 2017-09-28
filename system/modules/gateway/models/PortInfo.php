<?php

namespace system\modules\gateway\models;

use system\modules\stats\models\StatsPort;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tab_port_info}}".
 *
 * @property integer $info_id
 * @property integer $gateway_id
 * @property integer $if_port
 * @property integer $action
 * @property string $mac
 * @property string $ip
 * @property integer $pkg_num
 * @property integer $bytes
 * @property integer $add_time
 * @property integer $group_id
 * @property double $pps
 * @property double $bandwidth
 */
class PortInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_port_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gateway_id', 'if_port', 'pkg_num', 'bytes', 'add_time', 'group_id', 'action'], 'integer'],
            [['mac'], 'string', 'max' => 20],
            [['ip'], 'string', 'max' => 64],
            [['pps', 'bandwidth'], 'double'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'info_id' => 'ID号',
            'gateway_id' => '网关ID',
            'if_port' => '网口号',
            'action' => '状态',
            'mac' => '设备MAC',
            'ip' => '设备IP',
            'pkg_num' => 'IP包数量',
            'bytes' => '流量',
            'add_time' => '时间',
            'group_id' => '组ID',
            'pps' => '速率',
            'bandwidth' => '带宽',
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 统计端口数据, 进行统计，根据时间判断当前时间应该放到哪个时间段内，时间肯定在一个小时内，比如60，那么
        // 当前的分钟数
        $time = $this->_getTime($this->add_time);

        $statsModel = StatsPort::find()->where(['gateway_id' => $this->gateway_id, 'if_port' => $this->if_port, 'time' => $time])->one();
        if (!$statsModel) {
            $statsModel = new StatsPort();
            $statsModel->gateway_id = $this->gateway_id; // 网关id
            $statsModel->if_port = $this->if_port; // 网口号
            $statsModel->time = $time;
        }

        // 重新计算温度，湿度，震动 ，温度和湿度用一位小数保存，震动保存是整数
        $statsModel->pkg_num = sprintf("%.2f", ($statsModel->pkg_num + $this->pkg_num)/2);
        $statsModel->bytes = sprintf("%.2f", ($statsModel->bytes + $this->bytes)/2);
        $statsModel->pps = sprintf("%.2f", ($statsModel->pps + $this->pps)/2);
        $statsModel->bandwidth = sprintf("%.2f", ($statsModel->bandwidth + $this->bandwidth)/2);
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
        $stats_interval = Yii::$app->systemConfig->getValue('STATS_INTERVAL_GATEWAY_PORT', 60);
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


    // 根据网关id获取网口的数据
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

        $data = self::find()
            ->select(['gateway_id', 'if_port', 'bytes', 'add_time'])
            ->where(['gateway_id' => $gateway_id])
            ->andWhere(['>=', 'add_time', $from])
            ->andWhere(['<=', 'add_time', $to])
            ->asArray()
            ->all();


        //echo '<pre>'; print_r($data); exit;

        // 把数据进行汇总，包括：1，按照网口进行汇总；2，对日期加上000；3，把流量对字节转换为MB
        $newData = [];
        foreach ($data as $item) {
            $newData[$item['if_port']][] = [$item['add_time'].'000', round($item['bytes']/1024/1024, 2)];
        }

        ksort($newData);

        //print_r($newData);exit;

        return $newData;
    }
}
