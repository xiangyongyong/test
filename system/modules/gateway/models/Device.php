<?php

namespace system\modules\gateway\models;

use system\core\utils\StringUtil;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%tab_dev}}".
 *
 * @property integer $dev_id
 * @property integer $gateway_id
 * @property integer $if_port
 * @property integer $factory_id
 * @property string $mac
 * @property string $ip
 * @property integer $action
 * @property integer $dev_type
 * @property integer $add_time
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gateway_id', 'if_port', 'factory_id', 'action', 'dev_type', 'add_time'], 'integer'],
            [['mac'], 'string', 'max' => 20],
            [['ip'], 'string', 'max' => 64],
            [['mac'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dev_id' => 'ID号',
            'gateway_id' => '网关id',
            'if_port' => '端口',
            'factory_id' => '厂商',
            'mac' => 'MAC',
            'ip' => 'IP',
            'action' => '端口状态',
            'dev_type' => '设备类型',
            'add_time' => '添加时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->add_time = time();
            }

            return true;
        }

        return false;
    }

    /**
     * 获取设备中对应的网关
     * @return \yii\db\ActiveQuery
     */
    public function getGateway()
    {
        return $this->hasOne(Gateway::className(), ['gateway_id' => 'gateway_id']);
    }

    /**
     * 获取厂家信息
     * @return \yii\db\ActiveQuery
     */
    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['factory_id' => 'factory_id']);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 写日志
        if ($insert) {
            $data = [
                '网关id' => $this->gateway_id,
                '端口' => $this->if_port,
                'IP' => $this->ip,
            ];
            $content = '新增了设备：' . $this->mac . '; 详细数据：' . Json::encode($data);
            $change = true;
        } else {
            $data = [
                'gateway_id' => $this->gateway_id,
                'if_port' => $this->if_port,
                'ip' => $this->ip,
                'factory_id' => $this->factory_id,
                'dev_type' => $this->dev_type,
            ];
            $content = '编辑了设备：' . $this->mac.'; ';
            $change = false;
            foreach ($data as $key => $newValue) {
                if(!isset($changedAttributes[$key]) || $changedAttributes[$key] == $newValue) {
                    continue;
                }
                $change = true;
                $content .= $this->attributeLabels()[$key] . ':' . $changedAttributes[$key] . '=>' . $this->$key . '; ';
            }
        }

        if ($change) {
            Yii::$app->systemLog->write([
                'type' => 'gateway', // 类型
                'target_id' => $this->gateway_id, // 目标
                'target_id2' => $this->dev_id, // 目标
                'content' => $content, // 内容
            ]);
        }
    }

    /**
     * 获取实时数据
     * @param $gateway_id
     * @return array
     */
    public static function getRealTimeData($gateway_id)
    {
        $data = self::find()
            ->select(['if_port', 'factory_id', 'mac', 'ip', 'dev_type', 'add_time'])
            ->where(['gateway_id' => $gateway_id])
            ->orderBy(['if_port' => SORT_ASC])
            ->asArray()
            ->all();

        //echo '<pre>';print_r($data);

        if (!$data) {
            return [];
        }
        // 设备类型
        $device_type = Yii::$app->systemConfig->getValue('DEVICE_TYPE_LIST', []);
        // 端口状态
        $port_state = Yii::$app->systemConfig->getValue('PORT_STATE_LIST', []);
        //print_r($device_type);exit;
        $newData = [];
        foreach ($data as $item) {
            // 获取实时状态
            $redisData = Yii::$app->redis->hmget("hash:port_info:".$gateway_id.":".$item['if_port'], 'action', 'time', 'pps', 'bytes','bandwidth');
            //print_r($redisData);exit;
            $action = $redisData[0];
            $time = $redisData[1];
            $pps = $redisData[2];
            $bytes = $redisData[3];
            $bandwidth = $redisData[4];
            // 重新整理数据
            $item['factory_name'] = Factory::getName($item['factory_id']); // 厂商名称
            $item['dev_type_desc'] = isset($device_type[$item['dev_type']]) ? $device_type[$item['dev_type']] : '--' ; // 设备类型描述
            // 实时数据

            // 状态描述
            $action_desc = isset($port_state[$action]) ? $port_state[$action] : '--' ;
            if (in_array($action, [0, 3, 4])) {
                $action_desc = '<span style="color: #FF5722">'.$action_desc.'</span>';
            }

            // 时间描述
            $time_desc = date('Y-m-d H:i:s', $time);
            if (time()-$time>350) {
                $time_desc = '<span style="color: #FF5722">'.$time_desc.'秒</span>';
            }

            // pps 表现形式
            if (is_numeric($pps)) {
                $pps_desc = number_format($pps,4).' pps';
            } else {
                $pps_desc = '--';
            }
            
            // bytes 表现形式
            if (is_numeric($bytes)) {
                $bytes_desc = number_format($bytes/1024,4).'B';
            } else {
                $bytes_desc = '--';
            }

            // 带宽表现形式
            if (is_numeric($bandwidth)) {
                $bandwidth_desc = number_format($bandwidth*8/1024,4).' kbps';
            } else {
                $bandwidth_desc = '--';
            }

            $item['action'] = $action; // 状态
            $item['action_desc'] = $action_desc;
            $item['time'] = $time;
            $item['time_desc'] = $time_desc;
            $item['pps'] = $pps;            
            $item['pps_desc'] = $pps_desc;
            $item['bytes'] = $bytes;
            $item['bytes_desc'] = $bytes_desc;
            $item['bandwidth'] = $bandwidth;
            $item['bandwidth_desc'] = $bandwidth_desc;

            $newData[$item['if_port']] = $item;
        }

        //print_r($newData);exit;

        return $newData;
    }
    
    /**
     * 获取端口的实时数据
     * @param $gateway_id
     * @return array
     */
    public static function getRealTimePortinfoData($gateway_id, $port_id)
    {
        $data = self::find()
                ->select('dev_type')
                ->andWhere(['gateway_id' => $gateway_id])
                ->andWhere(['if_port' => $port_id])
                ->one();
        // 设备类型
        $device_type = Yii::$app->systemConfig->getValue('DEVICE_TYPE_LIST', []);
        // 端口状态
        $port_state = Yii::$app->systemConfig->getValue('PORT_STATE_LIST', []);
        //print_r($device_type);exit;
        $newData = [];
       
        // 获取实时状态
        $redisData = Yii::$app->redis->hmget("hash:port_info:".$gateway_id.":".$port_id, 'action', 'pkg_num', 'pps', 'bytes', 'bandwidth', 'mac', 'ip', 'time');
        //print_r($redisData);exit;
        $action = $redisData[0];
        $pkg_num = $redisData[1];
        $pps = $redisData[2];
        $bytes = $redisData[3];
        $bandwidth = $redisData[4];
        $mac = $redisData[5];
        $ip = $redisData[6];
        $time = $redisData[7];
        // 重新整理数据
        //$item['factory_name'] = Factory::getName($item['factory_id']); // 厂商名称
        $newData['dev_type_desc'] = isset($device_type[$data['dev_type']]) ? $device_type[$data['dev_type']] : '--' ; // 设备类型描述
        // 实时数据

        // 状态描述
        $action_desc = isset($port_state[$action]) ? $port_state[$action] : '--' ;
        if (in_array($action, [0, 3, 4])) {
            $action_desc = '<span style="color: #FF5722">'.$action_desc.'</span>';
        }

        // pps 表现形式
        if (is_numeric($pps)) {
            $pps_desc = number_format($pps,4).' pps';
        } else {
            $pps_desc = '--';
        }

        // bytes 表现形式
        if (is_numeric($bytes)) {
            $bytes_desc = number_format($bytes/1024,4).'B';
        } else {
            $bytes_desc = '--';
        }

        // 带宽表现形式
        if (is_numeric($bandwidth)) {
            $bandwidth_desc = number_format($bandwidth*8/1024,4).' kbps';
        } else {
            $bandwidth_desc = '--';
        }

        // 时间描述
        $time_desc = date('Y-m-d H:i:s', $time);
        if (time()-$time>350) {
            $time_desc = '<span style="color: #FF5722">'.$time_desc.'</span>';
        }
        
        $newData['port'] = $port_id;
        $newData['action'] = $action;
        $newData['action_desc'] = $action_desc;
        $newData['pkg_num'] = $pkg_num;                 
        $newData['pps_desc'] = $pps_desc;
        $newData['bytes_desc'] = $bytes_desc;
        $newData['bandwidth_desc'] = $bandwidth_desc;
        $newData['time_desc'] = $time_desc; 
        $newData['mac'] = $mac; 
        $newData['ip'] = $ip;        

        //print_r($newData);exit;

        return $newData;
    }


}
