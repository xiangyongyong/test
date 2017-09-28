<?php
/**
 * 网关控制器
 */
namespace system\modules\gateway\controllers;

use system\modules\gateway\models\Device;
use system\modules\gateway\models\Gateway;
use system\modules\group\models\Group;
use system\modules\main\models\Log;
use system\modules\stats\models\StatsEnv;
use system\modules\stats\models\StatsPort;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 网关控制器
 */
class GatewayController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword');

        $query = Gateway::find();

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'gateway_name', $keyword], ['like', 'gateway_desc', $keyword], ['like', 'mac', $keyword], ['like', 'ip', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['gateway_id' => SORT_DESC])
            ->all();

        // 所有组
        $groups = ArrayHelper::map(Group::getAllData(), 'id', 'name');


        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
            'groups' => $groups,
        ]);
    }

    /**
     * 修改数据
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {

        $get = \Yii::$app->request->get();


        $model = Gateway::findOne($id);

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }


        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (Gateway::find()->where(['and', ['!=', 'gateway_id', $id], ['gateway_name' => $get['gateway_name']]])->count()) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '名称已存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '名称可用'
                ]);
            }
        }

        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
                $this->flashMsg('ok', '修改成功');
                return $this->redirect('index');
            } else {
                $this->flashMsg('error', '修改失败，请重试');
            }
        }

        return $this->render('edit', [
            'model' => $model
        ]);
    }

    /**
     * 查看网关详情
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $get = \Yii::$app->request->get();

        $model = Gateway::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('此网关不存在');
        }

        // 开始／关闭 设备扫描 @TODO 写日志
        if (isset($get['ajax']) && $get['ajax'] == 'study') {
            if (isset($get['state'])) {
                $state = $get['state'];
                $arr = [];
                if ($state == 2 || $state == 0) {
                    $arr = ['gateway_id'=>$id,'type'=>81, 'command'=>$state];
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

        // 端口操作 @TODO 写日志
        if (isset($get['ajax']) && $get['ajax'] == 'changePortState') {
            if (isset($get['state'], $get['port'])) {
                $state = $get['state']; // 动作
                $port = $get['port']; // 网口
                $stateMap = [
                    1 => '绑定设备',
                    2 => '开放端口',
                    3 => '关闭端口'
                ];
                if (array_key_exists($state, $stateMap)) {
                    $arr = ['gateway_id'=>$id,'type'=>80, 'command'=>$port.','.$state];
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

        // 获取网关下设备的实时数据
        $devices = Device::getRealTimeData($id);

        // 网关实时环境数据
        $gateway = Gateway::getRealTimeData($id);

        // 处理环境数据图表 今天的环境数据
        $envData = StatsEnv::getDataByGateway($id);

        // 处理网口数据，今天的网口数据
        $portData = StatsPort::getDataByGateway($id);

        // ajax获取所有实时数据
        if (isset($get['ajax']) && $get['ajax'] == 'all') {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => 'ok',
                'data' => [
                    'device' => $devices,
                    'gateway' => $gateway,
                    'envData' => $envData,
                    'portData' => $portData,
                ],
            ]);
        }

        // 日志管理
        $logs = Log::getDataByTypeAndId('gateway', $id, SORT_DESC);

        return $this->render('view', [
            'model' => $model,
            'devices' => $devices, // 设备列表
            'logs' => $logs, // 日志列表
            'gateway' => $gateway, // redis中实时数据
            'envData' => $envData,
            'portData' => $portData,
        ]);
    }
}
