<?php

namespace system\modules\operation\controllers;

use yii\web\Controller;
use system\modules\workorder\models\WorkOrder;
use system\modules\main\models\Comment;
use system\modules\main\models\Log;
use system\modules\user\models\User;

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
        echo 'operation';
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


//        return $this->render('view', [
//            'model' => $model,
//            'comment' => $comment,
//            'users' => $users,
//            'logs' => $logs,
//        ]);
    }
}
