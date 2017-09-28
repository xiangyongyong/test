<?php

namespace system\modules\operation\controllers;

use system\modules\gateway\models\Device;
use system\modules\gateway\models\Gateway;
use system\modules\group\models\Group;
use system\modules\user\models\User;
use yii\helpers\ArrayHelper;
use system\modules\operation\models\UserGatewayGroup;

/**
 * Default controller for the `modules` module
 */
class DefaultController extends BaseController
{
    
    public function actionMap()
    {        
        $this->layout = false;
        $user_id = \yii::$app->user->getIdentity()->user_id;
        $user = User::find();
        $userInfo = User::findOne($user_id);
        //print_r($userInfo->company_id);exit;
        //根据不同角色查找对应的网关1-管理员 10-高级运维 20-一般运维
        $gatewayQuery = Gateway::find()
                        ->select(['gateway_id', 'state', 'longitude', 'latitude']);
        if($userInfo->role_id == '1'){
            //管理员查看所有
            $gateway = $gatewayQuery->asArray()->all();

            $gatewayList = ArrayHelper::index($gateway, 'gateway_id');         
        }else if($userInfo->role_id == '10'){   
            //高级运维查看本公司员工负责的所有网关
            $company_id = $userInfo->company_id;
            
            $users = $user
                    ->select('user_id')
                    ->where(['company_id' => $company_id])
                    ->asArray()->all();
            $a = '';
            $b = '';
            $gatewaygroups = [];
            $gatewayids = [];
            foreach ($users as $key => $value) {                    
                $groupsAll = UserGatewayGroup::find()
                        ->select('target_id')
                        ->where(['user_id' => $value['user_id'], 'type' => 'group'])
                        ->asArray()->all();
                $gatewaysAll = UserGatewayGroup::find()
                        ->select('target_id')
                        ->where(['user_id' => $value['user_id'], 'type' => 'gateway'])
                        ->asArray()->all();
                
                if(!empty($groupsAll)){
                    foreach ($groupsAll as $key => $group) {
                        // 查询此group下对所有组
                        $groups = Group::getChildIdsById($group['target_id']);    
                        $a .= ','.implode(',', $groups);                       
                    }                    
                }
                if(!empty($gatewaysAll)){
                    $gateways = ArrayHelper::index($gatewaysAll, 'target_id');
                    $gateways = array_keys($gateways);
                    $b .= ','.implode(',', $gateways);                                      
                }
            }
            $groups = explode(',', ltrim($a));            
            $gatewayQuery->where(['is_group' => 0])->andWhere(['group_id' => $groups]);
            $gateway = $gatewayQuery->asArray()->all();
            $gatewaygroups = ArrayHelper::index($gateway, 'gateway_id');
            
            $gateways = explode(',', ltrim($b));
            $gatewayQuery->where(['is_group' => 0])->andWhere(['gateway_id' => $gateways]);                        
            $gateway = $gatewayQuery->asArray()->all();
            $gatewayids = ArrayHelper::index($gateway, 'gateway_id');

            $gatewayList = array_merge($gatewaygroups, $gatewayids);
            $gatewayList = ArrayHelper::index($gatewayList, 'gateway_id');
        }else{         
            //一般运维查看自己负责的工单
            $groupsAll = UserGatewayGroup::find()
                    ->select('target_id')
                    ->where(['user_id' => $user_id, 'type' => 'group'])
                    ->asArray()->all();
            $gatewaysAll = UserGatewayGroup::find()
                    ->select('target_id')
                    ->where(['user_id' => $user_id, 'type' => 'gateway'])
                    ->asArray()->all();
            
            $gatewaygroups = [];
            $gatewayids = [];
            if(!empty($groupsAll)){
                $a = '';
                foreach ($groupsAll as $key => $group) {
                    // 查询此group下对所有组
                    $groups = Group::getChildIdsById($group['target_id']);    
                    $a .= ','.implode(',', $groups);                       
                }  
                $groups = explode(',', ltrim($a));            
                $gatewayQuery->andWhere(['group_id' => $groups]);
                $gateway = $gatewayQuery->asArray()->all();
                $gatewaygroups = ArrayHelper::index($gateway, 'gateway_id');
            }
            if(!empty($gatewaysAll)){
                $gateways = ArrayHelper::index($gatewaysAll, 'target_id');
                $gateways = array_keys($gateways);

                $gatewayQuery->andWhere(['gateway_id' => $gateways]);                        
                $gateway = $gatewayQuery->asArray()->all();
                $gatewayids = ArrayHelper::index($gateway, 'gateway_id');
            }
                                               
            $gatewayList = array_merge($gatewaygroups, $gatewayids);
            $gatewayList = ArrayHelper::index($gatewayList, 'gateway_id');
        } 
        $state = array_column($gatewayList,'state');
        $state = array_count_values($state);
        $state[0] = isset($state[0]) ? $state[0] : 0 ;
        $state[1] = isset($state[1]) ? $state[1] : 0 ;
        $state[2] = isset($state[2]) ? $state[2] : 0 ;
        $total = count($gatewayList);
        $get = \Yii::$app->request->get();
        if (isset($get['ajax']) && $get['ajax'] == 'getAll') {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => $gatewayList, 
            ]);
        }

        return $this->render('map', [
            'state' => $state,
            'total' => $total,  
        ]);
    }
    
    //获取网关基本信息
    public function actionBasic() {
        $get = \Yii::$app->request->get();

        if (isset($get['gateway_id'])) {
            $gateway_id = $get['gateway_id'];
            $state = [Gateway::STATE_NORMAL => "正常",Gateway::STATE_MAINTENANCE => "维护中",Gateway::STATE_OFFLINE => "离线"];
            // 网关基本信息
            $gateway = Gateway::find()
                    ->select('gateway_id, address, state')
                    ->where(['gateway_id' => $gateway_id])
                    ->asArray()->one();            
            $gateway['state'] = $state[$gateway['state']];
            if(Gateway::getUserByGateway($gateway_id)){
                $gateway['worker_name'] = User::findOne(Gateway::getUserByGateway($gateway_id))->realname;
            }else{
                $gateway['worker_name'] = '--';
            }
            $gateway['suspending'] = Gateway::getSuspendingOrders($gateway_id);
            
            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => [
                    'gateway' => $gateway,
                ]
            ]);
        }
    }
    
    //获取网关详细信息
    public function actionView()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['gateway_id'])) {
            $gateway_id = $get['gateway_id'];
            $state = [Gateway::STATE_NORMAL => "正常",Gateway::STATE_MAINTENANCE => "维护中",Gateway::STATE_OFFLINE => "离线"];
            // 网关基本信息
            $gateway = Gateway::find()
                    ->select('gateway_id, ip, mac, state, address, pole, add_time')
                    ->where(['gateway_id' => $gateway_id])
                    ->asArray()->one();            
            $gateway['state'] = $state[$gateway['state']];
            $gateway['add_time'] = date("Y-m-d H:i:s", $gateway['add_time']);
            if(Gateway::getUserByGateway($gateway_id)){
                $gateway['worker_id'] = Gateway::getUserByGateway($gateway_id);
                $gateway['worker_name'] = User::findOne($gateway['worker_id'])->realname;
            }else{
                $gateway['worker_id'] = '';
                $gateway['worker_name'] = '--';
            }         
            
            // 设备信息
            // 获取网关下设备的实时数据
            $devices = Device::getRealTimeData($gateway_id);
            $gateway['device_counts'] = count($devices);
            // 网关实时环境数据
            $statsEnv = Gateway::getRealTimeData($gateway_id);        

            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => [
                    'gateway' => $gateway,
                    'device' => $devices,
                    'statsEnv' => $statsEnv,
                ]
            ]);
        } 
    }
    
    //获取网关端口详细信息
    public function actionPort() 
    {
        $get = \Yii::$app->request->get();
        
        if (isset($get['gateway_id']) && isset($get['port_id'])) {
            $gateway_id = $get['gateway_id'];
            $port_id = $get['port_id'];
            $device = Device::getRealTimePortinfoData($gateway_id, $port_id);
            
            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => [
                    'device' => $device,
                ]
            ]);
        }
    }
    
    //管理员可以操作正常设备的扫描功能
    public function actionChangestudy()
    {
        $get = \Yii::$app->request->get();
        // 开始／关闭 设备扫描 @TODO 写日志
        if (isset($get['state'])) {
            $state = $get['state'];
            $id = $get['id'];
            $model = Gateway::findOne($id);
            $arr = [];
            if ($state == 2 || $state == 0) {
                $arr = ['gateway_id' => $id,'type'=>81, 'command' => $state];
                $res = \Yii::$app->redis->rpush("list:cmd:".$id, json_encode($arr));
                if ($res) {
                    // xx打开了网关扫描功能；xx关闭了网关扫描功能；
                    $stateMsg = $state == 0 ? '关闭了' : '开启了';
                    $content = \Yii::$app->user->identity->realname . $stateMsg . '网关'.$id.'的扫描功能('.$model->gateway_name.');';
                    // 写日志
                    \Yii::$app->systemLog->write([
                        'type' => 'gateway',
                        'target_id' => $id,
                        'content' => $content,
                    ]);
                    return $this->ajaxReturn([
                        'code' => 0,
                        'message' => '操作成功，1分钟后生效',
                    ]);
                }
            }
        }

        return $this->ajaxReturn([
            'code' => 1,
            'message' => '操作失败，请重试',
        ]);
    }
    
    //管理员可以操作正常设备的端口的打开和关闭
    public function actionChangeport()
    {
        $get = \Yii::$app->request->get();
        // 端口操作 @TODO 写日志       
        if (isset($get['state'], $get['port'])) {
            $state = $get['state']; // 动作
            $port = $get['port']; // 网口
            $id = $get['gateway_id']; // 网关ID
            $stateMap = [
                1 => '绑定设备',
                2 => '开放端口',
                3 => '关闭端口'
            ];
            if (array_key_exists($state, $stateMap)) {
                $arr = ['gateway_id' => $id,'type'=>80, 'command' => $port.','.$state];
                $res = \Yii::$app->redis->rpush("list:cmd:".$id, json_encode($arr));
                if ($res) {
                    // xx操作了网关5的3号网口：绑定设备
                    $content = \Yii::$app->user->identity->realname . '操作了网关'.$id.'的'.$port.'号网口:'.$stateMap[$state].';';
                    // 写日志
                    \Yii::$app->systemLog->write([
                        'type' => 'gateway',
                        'target_id' => $id,
                        'content' => $content,
                    ]);

                    return $this->ajaxReturn([
                        'code' => 0,
                        'message' => '操作成功，1分钟后生效',
                    ]);
                }
            }
        }

        return $this->ajaxReturn([
            'code' => 1,
            'message' => '操作失败，请重试',
        ]);
        
    }
    
    /**
     * 创建工单
     */
    public function actionAdd()
    {
        $get = \Yii::$app->request->get();

        if (!isset($get['target_id'], $get['worker_id'], $get['content_type'])) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '数据不全',
            ]);
        }
        
        if(($get['content_type'] == 0) && empty($get['content'])){
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '请填写原因',
            ]);
        }

        $res = \Yii::$app->systemWorkorder->add([
            'target_id' => $get['target_id'],
            'worker_id' => $get['worker_id'],
            'content_type' => $get['content_type'],
            'content' => $get['content']
        ]);

        if ($res) {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => '添加成功'
            ]);
        }

        return $this->ajaxReturn([
            'code' => 1,
            'message' => '添加失败'
        ]);
    }
}
