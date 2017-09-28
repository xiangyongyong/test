<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/27
 * Time: 上午11:20
 */

namespace system\modules\main\controllers;


use system\modules\main\models\Comment;

class CommentController extends BaseController
{
    // 提交评论，然后返回原始页面
    public function actionAdd()
    {
        $data = \Yii::$app->request->post();
        $model = new Comment();
        if ($model->load($data, '') && $model->save()) {
            $this->flashMsg('ok', '提交成功');
        } else {
            $this->flashMsg('error', '提交失败，请重试');
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}