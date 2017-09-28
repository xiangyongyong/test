<?php

namespace system\modules\visual\controllers;

use system\modules\gateway\models\Device;
use system\modules\gateway\models\Gateway;
use system\modules\gateway\models\PortInfo;
use system\modules\group\models\Group;
use system\modules\stats\models\StatsEnv;
use system\modules\workorder\models\WorkOrder;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `visual` module
 */
class DefaultController extends BaseController
{
    public $layout = false;

    public $disableCsrfAction = ['post'];

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $get = \Yii::$app->request->get();

        // 要显示的网关组下的数据; 0代表所有网关，不做筛选；
        $group_id = \Yii::$app->request->get('group_id');
        if ($group_id == '' || is_null($group_id)) {
            $group_id = \Yii::$app->systemConfig->getValue('VISUAL_DEFAULT_GROUP', 0);
        }

        // 查询某个网关组下的所有网关
        //$gateway = Gateway::getAllByGroupId($group_id);

        $gatewayQuery = Gateway::find()
            ->select(['gateway_id', 'state', 'longitude', 'latitude']);

        if ($group_id) {
            // 查询此group下对所有组
            $groups = Group::getChildIdsById($group_id);
            $gatewayQuery->andWhere(['group_id' => $groups]);
        }

        $gateway = $gatewayQuery->asArray()->all();
        $gatewayList = ArrayHelper::index($gateway, 'gateway_id');
        //var_dump($gatewayList);exit;

        // 此组下的第一级分类; 比如 武汉市：20个，其他市10个，使用下钻功能，武汉市下的各个区，比如洪山有5个，青山有4个； 下钻时下面的两个图表状态要实时更改吗？
        $childsGroups = Group::getFirstChildsById($group_id);
        $gatewayGroup = [];
        foreach ($childsGroups as $child) {
            $groupArr = Group::getChildIdsById($child['id']);
            $count = Gateway::find()->where(['group_id' => $groupArr])->count();
            //$gatewayGroup[] = [$child['name'], $count];
            $gatewayGroup['name'][] = $child['name'];
            $gatewayGroup['count'][] = $count;
            $gatewayGroup['map'][$child['name']] = $child['id'];
        }

        //echo '<pre>';print_r($gatewayGroup);exit;

        // 工单的状态图表, 按照日，月，年来展示
        // 默认按找日期
        $workOrderType = \Yii::$app->request->get('workOrderType', 'day');
        $workOrderQuery = WorkOrder::find();
        // 开始时间和结束时间； 数据格式应该是这样的：[20170424,
        $workOrderData = [];

        if ($workOrderType == 'month') {
            // 查询最近12个月的数据
            $fromTime = strtotime('-11 months ' . date('Y-m-d 00:00:00'));
            $fromTime2 = date('Y-m-d H:i:s', $fromTime);
            for($i=0; $i<12; $i++) {
                $time = date('Y-m', strtotime('+'.$i.' months '.$fromTime2));
                $workOrderData[$time] = [0, 0, 0, 0];
            }

            $workOrderQuery->select(['FROM_UNIXTIME(created_at, "%Y-%m") as time']);
        }
        else if ($workOrderType == 'year') {
            // 查询最近3年的数据
            $fromTime = strtotime('-2 years ' . date('Y-00-00 00:00:00'));
            $fromTime2 = date('Y-m-d H:i:s', $fromTime);

            for($i=0; $i<3; $i++) {
                $time = date('Y', strtotime('+'.$i.' year '.$fromTime2));
                $workOrderData[$time] = [0, 0, 0, 0];
            }

            $workOrderQuery->select(['FROM_UNIXTIME(created_at, "%Y") as time']);
        }
        // 默认按天
        else {
            // 查询这个月的数据，或者查询最近30天的数据
            $fromTime = strtotime('-29 days ' . date('Y-m-d 00:00:00'));
            $fromTime2 = date('Y-m-d H:i:s', $fromTime);
            for($i=0; $i<30; $i++) {
                $time = date('Y-m-d', strtotime('+'.$i.' days '.$fromTime2));
                $workOrderData[$time] = [0, 0, 0, 0];
            }

            $workOrderQuery->select(['FROM_UNIXTIME(created_at, "%Y-%m-%d") as time']);
        }

        $workOrderArray = $workOrderQuery->addSelect(['state', 'COUNT(*) as count'])
            ->where(['>=', 'created_at', $fromTime])
            ->groupBy(['time', 'state'])
            ->asArray()
            ->all();


        $newData = [];
        foreach ($workOrderArray as $key => $item) {
            if ($key == 0) {
                // 把开始时间设置到数据开始的时间
                foreach ($workOrderData as $k => $v) {
                    if ($k == $item['time']) {
                        break;
                    }
                    unset($workOrderData[$k]);
                }
            }
            $workOrderData[$item['time']][$item['state']] = $item['count'];
        }

        //echo '<pre>'; print_r($workOrderData);exit;
        //$workOrderState = WorkOrder::find()->select(['state', 'count(*) as count'])->groupBy(['state'])->asArray()->all();

        // 网关实时状态统计图表; 这个会根据当前网关组实时的变化
        $gatewayStateQuery = Gateway::find()
            ->select(['state', 'count(*) as count']);
        if ($group_id) {
            // 查询此group下对所有组
            $gatewayStateQuery->andWhere(['group_id' => $groups]);
        }
        $gatewayState = $gatewayStateQuery
            ->groupBy(['state'])
            ->asArray()
            ->all();

        //print_r($gatewayState);exit;

        if (isset($get['ajax']) && $get['ajax'] == 'getAll') {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => [
                    'gatewayList' => $gatewayList, // 网关列表
                    'gatewayGroup' => $gatewayGroup, // 网关组
                    'workOrderState' => $workOrderData, // 工单状态
                    'gatewayState' => $gatewayState, // 网关状态
                    'hasChildGroup' => !empty($gatewayGroup)
                ],
            ]);
        }

        return $this->render('index', [
            'data' => [
                'gatewayList' => $gatewayList,
                'gatewayGroup' => $gatewayGroup,
                'workOrderState' => $workOrderData,
                'gatewayState' => $gatewayState,
            ],

        ]);
    }

    // 所有的ajax数据
    public function actionGet()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['ajax'])) {

            if ($get['ajax'] == 'gateway') {
                $gateway_id = $get['gateway_id'];

                // 网关基本信息
                $gateway = Gateway::find()->where(['gateway_id' => $gateway_id])->asArray()->one();
                // 设备信息
                // 获取网关下设备的实时数据
                $devices = Device::getRealTimeData($gateway_id);
                // 环境数据
                $statsEnv = StatsEnv::getDataByGateway($gateway_id);

                // 网口数据
                $portData = PortInfo::getDataByGateway($gateway_id);

                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => 'ok',
                    'data' => [
                        'gateway' => $gateway,
                        'devices' => $devices,
                        'statsEnv' => $statsEnv,
                        'portData' => $portData,
                    ]
                ]);
            }


        }
    }

    // 提交数据
    public function actionPost()
    {
        $post = \Yii::$app->request->post();

        if (isset($post['ajax'])) {
            // 校正位置，网关id，经纬度
            if ($post['ajax'] == 'correctLocation') {
                return $this->_correctLocation($post);
            }
        }

    }

    // 校正位置
    private function _correctLocation($post)
    {
        if (!isset($post['gateway_id'], $post['longitude'], $post['latitude'])) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '缺少参数'
            ]);
        }

        $gatewayModel = Gateway::findOne($post['gateway_id']);
        if (!$gatewayModel) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '此网关不存在'
            ]);
        }

        $gatewayModel->longitude = $post['longitude'];
        $gatewayModel->latitude = $post['latitude'];
        if ($gatewayModel->save()) {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => '操作成功',
            ]);
        } else {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '操作失败'.json_encode($gatewayModel->errors)
            ]);
        }
    }



}
