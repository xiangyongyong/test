<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/16
 * Time: 上午11:11
 */

namespace system\modules\user\controllers;

use yii;
use system\modules\user\models\User;
use system\modules\user\models\UserGatewayGroup;

class ManageController extends BaseController
{

    /**
     * 用户列表
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        $status = \Yii::$app->request->get('status'); // 状态，默认只显示status=0的数据

        $query = User::find();

        // 状态
        if ($status != '') {
            $query->andWhere(['status' => $status]);
        } else {
            $query->andWhere(['!=', 'status', User::STATUS_DELETE]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'username', $keyword], ['like', 'realname', $keyword], ['like', 'phone', $keyword], ['like', 'email', $keyword]]);
        }

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

    /**
     * 绑定网关组
     * @param $id
     * @return string|yii\web\Response
     */
    public function actionBindgroup($id)
    {
        $model = User::findOne(['status' => User::STATUS_ACTIVE, 'user_id' => $id]);
        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        // 提交数据
        if (\Yii::$app->request->isPost) {
            $group_id = \Yii::$app->request->post('group_id');
            $groups = $group_id ? explode(',', $group_id) : [];
            $res = UserGatewayGroup::saveData($id, $groups);
            if ($res) {
                $this->flashMsg('ok', '操作成功');
            } else {
                $this->flashMsg('error', '操作失败，请重试!');
            }
        }

        // 查找用户已经绑定的组id
        $groups = UserGatewayGroup::getGroupsByUser($id);

        return $this->render('bindgroup', [
            'model' => $model,
            'groups' => $groups,
        ]);
    }

}