<?php

namespace system\modules\workorder\controllers;

use system\modules\main\models\Comment;
use system\modules\main\models\Log;
use system\modules\user\models\User;
use system\modules\workorder\models\WorkOrder;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Default controller for the `workorder` module
 */
class DefaultController extends BaseController
{
    public $disableCsrfAction = ['add'];

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        //$state = \Yii::$app->request->get('state'); // 状态，默认显示
        $stategroup = \Yii::$app->request->get('stategroup', 1); // 状态，默认显示

        $query = WorkOrder::find();

        // 状态
        /*if ($state) {
            $query->andWhere(['state' => $state]);
        }*/

        if ($stategroup == 1) {
            $query->andWhere(['state' => WorkOrder::$STATE_NOT_FINISH]);
        }
        else if ($stategroup == 2) {
            $query->andWhere(['state' => WorkOrder::$STATE_FINISH]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'content', $keyword], ['like', 'order_id', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            ->with('worker')
            ->with('user')
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['order_id' => SORT_DESC, 'state' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }

    /**
     * 我的工单
     * @return string
     */
    public function actionMy()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        //$state = \Yii::$app->request->get('state'); // 状态，默认显示
        $stategroup = \Yii::$app->request->get('stategroup', 1); // 状态，默认显示

        $query = WorkOrder::find();

        // 状态
        /*if ($state) {
            $query->andWhere(['state' => $state]);
        }*/

        // 只查询我的工单，由我发起和分配给我的
        $user_id = \Yii::$app->user->identity->getId();
        $query->andWhere(['or', ['user_id' => $user_id], ['worker_id' => $user_id]]);

        if ($stategroup == 1) {
            $query->andWhere(['state' => WorkOrder::$STATE_NOT_FINISH]);
        }
        else if ($stategroup == 2) {
            $query->andWhere(['state' => WorkOrder::$STATE_FINISH]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'content', $keyword], ['like', 'order_id', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'pageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            ->with('worker')
            ->with('user')
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['order_id' => SORT_DESC, 'state' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }

    /**
     * 查看工单
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /* @var $model WorkOrder*/
        $model = WorkOrder::find()
            ->with('notify')
            ->with('urge')
            ->with('user')
            ->with('worker')
            ->with('gateway')
            ->where(['order_id' => $id])
            //->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('数据不存在');
        }

        // 更改状态和责任人
        if (\Yii::$app->request->isPost) {

            $post = \Yii::$app->request->post();

            if (isset($post['action'])) {
                $action = $post['action'];
                // 开始处理
                if ($action == 'handler') {
                    $model->state = WorkOrder::STATE_HANDLING;
                }
                // 关闭
                else if ($action == 'close') {
                    $model->finished_remark = $post['finish_remark'];
                    $model->state = WorkOrder::STATE_CLOSE;
                }
                // 已解决
                else if ($action == 'resolve') {
                    $model->finished_remark = $post['finish_remark'];
                        $model->state = WorkOrder::STATE_SOLVED;
                }
                // 更改责任人
                else if ($action == 'changeWorker') {
                    // 由权限才能执行
                    if (\Yii::$app->user->can('workorder/default/edit')) {
                        $model->worker_id = $post['worker_id'];
                    }
                }
                //故障原因预估
                else if($action == 'setProblem'){
                    $model->problem = $post['problem'];
                }
                //预计完成日期
                else if($action == 'setPromise_time'){
                    $model->promise_time = $post['promise_time'];
                }
                $res = $model->save();
                if ($res) {
                    $this->flashMsg('ok', '操作成功');
                } else {
                    $this->flashMsg('error', '操作失败');
                }

                // 数据有更新，刷新页面
                return $this->refresh();
            }
        }

        // 沟通记录
        $comment = Comment::getData('workorder', $id);

        // 获取所有有效用户
        $users = User::getAllUser();

        // 操作日志
        $logs = Log::getDataByTypeAndId('workorder', $id);

        //print_r($model);exit;
        return $this->render('view', [
            'model' => $model,
            'comment' => $comment,
            'users' => $users,
            'logs' => $logs,
        ]);
    }

    /**
     * 保修工单
     */
    public function actionAdd()
    {
        $post = \Yii::$app->request->post();

        if (!isset($post['target_id'], $post['content'])) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '数据不全',
            ]);
        }

        $res = \Yii::$app->systemWorkorder->gateway([
            'target_id' => $post['target_id'],
            'content' => $post['content']
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

    //催单
    public function actionUrge($order_id, $gateway_desc, $worker_id)
    {
        $loginUser = Yii::$app->user->identity->realname;
        $loginUserId = Yii::$app->user->identity->getId();
        $notifyContent = $loginUser.'提醒您尽快处理'.$gateway_desc.'异常';
        $result = Yii::$app->systemNotify->createMessage($notifyContent, $loginUserId, $worker_id, 'workorder', $order_id, 'urge');
        if($result){
            //催单次数
            $model = WorkOrder::find()->where(['order_id' => $order_id])->one();
            $model->urge_num += 1;
            $model->save();

            $content = $loginUser.'对工单'.$order_id.'进行了催单';
            Yii::$app->systemLog->write([
                'type' => 'workorder',
                'target_id' => $order_id,
                'content' => $content,
            ]);
            $this->flashMsg('ok', '提交成功');
        }else{
            echo 'no:'.$result;
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}
