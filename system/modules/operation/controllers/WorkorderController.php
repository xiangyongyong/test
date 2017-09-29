<?php

namespace system\modules\operation\controllers;

use system\modules\workorder\models\WorkOrder;
use system\modules\main\models\Comment;
use system\modules\main\models\Log;
use system\modules\user\models\User;
use yii\data\Pagination;

/**
 * Default controller for the `operation` module
 */
class WorkorderController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $stategroup = \Yii::$app->request->get('stategroup', 0); // 状态，默认显示

        $query = WorkOrder::find();       
        
        if ($stategroup == 0) {
            $query->andWhere(['state' => WorkOrder::STATE_SUSPENDING]);
        }
        else if ($stategroup == 1) {
            $query->andWhere(['state' => WorkOrder::STATE_HANDLING]);
        }
        else if ($stategroup == 2) {
            $query->andWhere(['state' => WorkOrder::$STATE_FINISH]);
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
        
        $suspendingCounts = $query->where(['state' => WorkOrder::STATE_SUSPENDING])->count();
        $handlingCounts = $query->where(['state' => WorkOrder::STATE_HANDLING])->count();
        $finishCounts = $query->where(['state' => WorkOrder::$STATE_FINISH])->count();
        //print_r($data);print_r($suspendingCounts." ");print_r($handlingCounts." ");print_r($finishCounts." ");exit;
        return $this->render('index', [
            'suspendingCounts' => $suspendingCounts,
            'handlingCounts' => $handlingCounts,
            'finishCounts' => $finishCounts,
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionMy()
    {
        $stategroup = \Yii::$app->request->get('stategroup', 0); // 状态，默认显示

        $query = WorkOrder::find();

        // 只查询我的工单，由我发起和分配给我的
        $user_id = \Yii::$app->user->identity->getId();
        $query->andWhere(['or', ['user_id' => $user_id], ['worker_id' => $user_id]]);        
        
        if ($stategroup == 0) {
            $query->andWhere(['state' => WorkOrder::STATE_SUSPENDING]);
        }
        else if ($stategroup == 1) {
            $query->andWhere(['state' => WorkOrder::STATE_HANDLING]);
        }
        else if ($stategroup == 2) {
            $query->andWhere(['state' => WorkOrder::$STATE_FINISH]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 10),
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
        
        $suspendingCounts = $query->where(['or', ['user_id' => $user_id], ['worker_id' => $user_id]])->andWhere(['state' => WorkOrder::STATE_SUSPENDING])->count();
        $handlingCounts = $query->where(['or', ['user_id' => $user_id], ['worker_id' => $user_id]])->andWhere(['state' => WorkOrder::STATE_HANDLING])->count();
        $finishCounts = $query->where(['or', ['user_id' => $user_id], ['worker_id' => $user_id]])->andWhere(['state' => WorkOrder::$STATE_FINISH])->count();

        //var_dump($finishCounts);exit;
        return $this->render('index', [
            'suspendingCounts' => $suspendingCounts,
            'handlingCounts' => $handlingCounts,
            'finishCounts' => $finishCounts,
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }

    public function actionView($id)
    {
        /* @var $model WorkOrder*/
        $model = WorkOrder::find()
            ->with('user')
            ->with('worker')
            ->with('gateway')
            ->where(['order_id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('数据不存在');
        }

        // 更改状态和责任人
        if (\Yii::$app->request->isPost) {
            //print_r(\Yii::$app->request->post());exit;
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

        print_r($model);exit;
        //echo $model->createCommand()->getRawSql();

        return $this->render('view', [
            'model' => $model,
            'comment' => $comment,
            'users' => $users,
            'logs' => $logs,
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
