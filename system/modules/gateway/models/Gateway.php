<?php
namespace system\modules\gateway\models;

use system\modules\group\models\Group;
use system\modules\operation\models\UserGatewayGroup;
use system\modules\workorder\models\WorkOrder;
use system\modules\gateway\models\Device;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%tab_gateway}}".
 *
 * @property integer $gateway_id
 * @property string $gateway_name
 * @property string $gateway_desc
 * @property string $mac
 * @property string $ip
 * @property integer $state
 * @property integer $add_time
 * @property integer $group_id
 * @property string $location
 * @property double $longitude
 * @property double $latitude
 * @property integer $is_correct
 * @property string $address
 * @property string $pole
 * @property integer $data_update_at
 */
class Gateway extends \yii\db\ActiveRecord
{
    const STATE_NORMAL = 0; // 正常
    const STATE_MAINTENANCE = 1; // 维护中
    const STATE_OFFLINE = 2; // 设备离线异常，只有一定时间没有收到数据时才是次状态

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_gateway}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time', 'group_id', 'is_correct', 'state', 'data_update_at'], 'integer'],
            [['gateway_name'], 'string', 'max' => 32],
            [['longitude', 'latitude'], 'number'],
            [['mac'], 'string', 'max' => 20],
            [['ip', 'pole', 'location'], 'string', 'max' => 64],
            [['address', 'gateway_desc'], 'string', 'max' => 255],
            [['mac'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gateway_id' => 'ID号',
            'gateway_name' => '名称',
            'gateway_desc' => '描述',
            'mac' => 'MAC',
            'ip' => 'ip',
            'state' => '网关状态',
            'add_time' => '创建时间',
            'group_id' => '组',
            'location' => '高德定位经纬度',
            'is_correct' => '是否校正过',
            'address' => '地址描述',
            'pole' => '电线杆标号',
            'data_update_at' => '数据更新时间',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->add_time = time();
            }

            // 更改了坐标
            if ($this->isAttributeChanged('longitude') || $this->isAttributeChanged('latitude')) {
                // @TODO 将高德坐标转换为地址，并且根据位置重新分组
                if ($this->longitude>0 && $this->latitude>0) {
                    $addressArray = \Yii::$app->systemMap->regeo($this->longitude, $this->latitude);

                    if ($addressArray) {

                        // 按照省市区乡镇街道来建立组;
                        $group_id = Group::createGroup([
                            $addressArray['province'], // 省
                            $addressArray['city'], // 市
                            $addressArray['district'], // 区县
                            $addressArray['township'], // 街道／乡镇
                        ]);

                        $this->group_id = $group_id ? $group_id : 1;

                        $this->address = $addressArray['formatted_address'];
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $log = false; // 是否要写日志
        // 写日志
        if ($insert) {
            $data = [
                'Mac' => $this->mac,
                'Ip' => $this->ip,
            ];
            $content = '新增了网关：' . $this->gateway_id . '; 详细数据：' . Json::encode($data) . ';';
            $log = true;
        } else {
            $data = [
                'gateway_name' => $this->gateway_name,
                'gateway_desc' => $this->gateway_desc,
                'group_id' => $this->group_id,
            ];
            $content = '编辑了网关：' . $this->gateway_name.'; ';

            foreach ($data as $key => $newValue) {
                if(!isset($changedAttributes[$key]) || $changedAttributes[$key] == $newValue || $key == 'data_update_time') {
                    continue;
                }
                // 组单独处理
                if ($key == 'group_id') {
                    $oldGroup = $changedAttributes['group_id'] ? Group::getNameById($changedAttributes['group_id']) . '('.$changedAttributes['group_id'].')' : ' ';
                    $newGroup = Group::getNameById($this->group_id) . '('.$this->group_id.')';
                    $content .= "组: {$oldGroup}=>{$newGroup}; ";
                } else {
                    $content .= $this->attributeLabels()[$key] . ':' . $changedAttributes[$key] . '=>' . $this->$key . '; ';
                }

                // 写日志
                $log = true;
            }
        }

        if ($log) {
            \Yii::$app->systemLog->write([
                'type' => 'gateway', // 类型
                'target_id' => $this->gateway_id, // 目标
                'content' => $content, // 内容
            ]);
        }

    }

    /**
     * 获取网关的名称
     * @param $id
     * @return string
     */
    public static function getName($id)
    {
        $model = self::findOne($id);
        if (!$model) {
            return '';
        }
        return $model->gateway_name;
    }

    /**
     * 获取redis中网关的实时数据
     * @param $gateway_id
     * @return array
     */
    public static function getRealTimeData($gateway_id)
    {
        $gateway = \Yii::$app->redis->hgetall('hash:gateway:'.$gateway_id);
        if (!$gateway) {
            return [];
        }

        // 是否扫描
        $is_study = \Yii::$app->systemConfig->getValue('GATEWAY_IS_STUDY', []);

        $data = [];
        for ($i=0; $i<count($gateway); $i++) {
            if ($i%2) {
                if (is_numeric($gateway[$i-1])) {
                    $data['custom_port_info'][$gateway[$i-1]] = $gateway[$i];
                } else {
                    $data[$gateway[$i-1]] = $gateway[$i];

                    // 时间处理
                    if ($gateway[$i-1] == 'data_time') {
                        $data['data_time_desc'] = date('Y-m-d H:i:s', $data['data_time']);
                    }

                    // 是否扫描处理
                    if ($gateway[$i-1] == 'is_study') {
                        $data['is_study_desc'] = isset($is_study[$data['is_study']]) ? $is_study[$data['is_study']] : '--';
                    }
                    
                    // 温度处理
                    if ($gateway[$i-1] == 'temperature') {
                        $data['temperature'] = isset($data['temperature']) ? $data['temperature'] : '--';
                    }
                    
                    // 湿度处理
                    if ($gateway[$i-1] == 'humidity') {
                        $data['humidity'] = isset($data['humidity']) ? $data['humidity'] : '--';
                    }
                    
                    // 振动处理
                    if ($gateway[$i-1] == 'vibration') {
                        $data['vibration'] = isset($data['vibration']) ? $data['vibration'] : '--';
                    }  
                }
            }
        }

        return $data;
    }

    /**
     * 根据网关id获取对应的组id
     * @param $gateway_id
     * @return int
     */
    public static function getGroupById($gateway_id)
    {
        $model = self::findOne($gateway_id);
        if ($model && $model->group_id) {
            return $model->group_id;
        }

        return false;
    }

    /**
     * 根据网关获取对应的管理员
     * @param $id int 网关组id
     * @return bool
     */
    public static function getUserByGateway($id)
    {
        $model = self::findOne($id);
        if (!$model || !$model->group_id) {
            return false;
        }

        // 根据group_id获取对应的用户
        $users = UserGatewayGroup::getUsersByGroup($model->group_id, $model->is_group);
        if ($users) {
            // 取第一个用户
            return $users[0];
        }

        // 如果此网关没有绑定任何用户，那么去它的父级组去找
        if (\Yii::$app->systemConfig->getValue('WORK_ORDER_TO_PARENT', 1)) {
            $model = Group::findOne($model->group_id);
            if (!$model) {
                return false;
            }

            // 获取父级path
            $parentIds = explode('-', trim($model->path, '-'));
            //print_r($parentIds);
            for ($i = count($parentIds)-1; $i>=0; $i--) {
                $pid = $parentIds[$i];
                if ($pid == 0) {
                    continue;
                }
                // 查找每个组绑定的用户
                $users = UserGatewayGroup::getUsersByGroup($pid);
                if ($users){
                    // 取第一个用户
                    return $users[0];
                }
            }
        }

        return false;
    }
    
    //获取设备信息
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['gateway_id' => 'gateway_id']);
    }
    
    //获取环境信息
    public function getEnv()
    {
        return $this->hasOne(Env::className(), ['gateway_id' => 'gateway_id']);
    }
    
    //获取端口信息
    public function getPortInfo()
    {
        return $this->hasOne(PortInfo::className(), ['gateway_id' => 'gateway_id']);
    }
    
    //获取指定网关的未处理工单条数
    public static function getSuspendingOrders($id)
    {
        return WorkOrder::find()
                ->andWhere(['state' => WorkOrder::STATE_SUSPENDING])
                ->andWhere(['type' => 'gateway'])
                ->andWhere(['target_id' => intval($id)])
                ->count();
    }
    
    //获取指定网关的设备连接数数
    public static function getDeviceStates($id)
    {
        return Device::find()
                ->where(['gateway_id' => $id])
                ->count();
    }

    /**
     * 根据某个网关组查询其下的所有网关
     * @param null $group_id
     * @return array|\yii\db\ActiveRecord[]
     */
    /*public static function getAllByGroupId($group_id = null)
    {
        $query = Gateway::find()
            ->select(['gateway_id', 'longitude', 'latitude']);

        if ($group_id) {
            // 查询此group下对所有组
            $groups = Group::getChildIdsById($group_id);
            $query->andWhere(['group_id' => $groups]);
        }

        $gateway = $query->asArray()->all();
        $gateway = ArrayHelper::index($gateway, 'gateway_id');

        return $gateway;
    }*/

}
