<?php

namespace system\modules\stats\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tab_stats_port".
 *
 * @property integer $id
 * @property integer $time
 * @property integer $gateway_id
 * @property integer $if_port
 * @property double $pkg_num
 * @property double $bytes
 * @property double $pps
 * @property double $bandwidth
 * @property integer $total
 */
class StatsPort extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_stats_port';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'gateway_id', 'if_port', 'total'], 'integer'],
            [['pkg_num', 'bytes', 'pps', 'bandwidth'], 'number'],
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
            'if_port' => '网口号',
            'pkg_num' => '平均ip包数量',
            'bytes' => '平均流量',
            'pps' => '速率',
            'bandwidth' => '带宽',
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
            $from = strtotime(date("Ymd"));
        }

        // 默认24小时小时内的数据
        if (is_null($to)) {
            $to = $from+86400-1;
        }

        $data = self::find()
            ->where(['gateway_id' => $gateway_id])
            ->andWhere(['>=', 'time', $from])
            ->andWhere(['<=', 'time', $to])
            ->asArray()
            ->all();

        //echo '<pre>';

        $res = ArrayHelper::index($data, 'id', 'if_port');

        $newData = [];
        foreach ($res as $k => $item) {
            //$newData['port'][] = '网口'.$k;
            $newData[$k] = ArrayHelper::getColumn($item, 'pps', false);
        }

        return $newData;
    }
}
