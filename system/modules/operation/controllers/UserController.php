<?php

namespace system\modules\operation\controllers;

use yii;
use system\modules\user\models\User;
use system\modules\operation\models\UserGatewayGroup;

/**
 * Description of UserController
 *
 * @author Administrator
 */
class UserController extends BaseController
{
    /**
     * 用户列表
     * @return string
     */
    public function actionIndex()
    {
        $query = User::find();

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            ->with('gatewaygroup')
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            //->orderBy(['sort' => SORT_DESC])
            ->all();

        //echo '<pre>';print_r($data);exit;

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }
    
    /**
     * 增加用户
     * @return string|\yii\web\Response
     */
    public function actionAdd()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (User::findOne(['username' => $get['username']])) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '用户名已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '用户名不存在'
                ]);
            }
        }

        $model = new User();
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
                $this->flashMsg('ok', '添加完成');
                return $this->redirect('index');
            } else {
                $this->flashMsg('error', '添加失败，请重试');
            }
        }

        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑用户
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $get = \Yii::$app->request->get();

        $model = User::findOne($id);

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (User::find()->where(['and', ['!=', 'user_id', $id], ['username' => $get['username']]])->count()) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '用户名已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '用户名不存在'
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
     * 将用户的状态置为删除状态
     * @param $id
     */
    public function actionDelete($id)
    {
        $model = User::findOne(['status' => User::STATUS_ACTIVE, 'user_id' => $id]);
        if (!$model) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '数据不存在',
            ]);
        }

        // 更改状态
        $model->status = User::STATUS_DELETE;

        if ($model->save()) {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => '删除成功',
            ]);
        } else {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '删除失败，请重试',
            ]);
        }
    }
    
    
    
}
