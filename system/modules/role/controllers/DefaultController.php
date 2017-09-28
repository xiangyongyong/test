<?php
namespace system\modules\role\controllers;


use system\modules\role\models\AuthAssign;
use system\modules\role\models\AuthRole;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;


/**
 * 角色管理控制器
 */
class DefaultController extends BaseController
{
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        $status = \Yii::$app->request->get('status'); // 状态，默认只显示status=0的数据

        $query = AuthRole::find();

        // 状态
        if ($status != '') {
            $query->andWhere(['status' => $status]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'name', $keyword], ['like', 'description', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            //->with('assign')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            //->orderBy(['sort' => SORT_DESC])
            ->asArray()
            ->all();

        $users = AuthAssign::getUserGroupByRole();

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
            'users' => $users,
        ]);
    }

    /**
     * 增加配置
     * @return string|\yii\web\Response
     */
    public function actionAdd()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (AuthRole::findOne(['name' => $get['name']])) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '角色名已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '角色名不存在'
                ]);
            }
        }

        $model = new AuthRole();
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
                $this->flashMsg('ok', '添加完成');
                return $this->redirect('index');
            } else {
                $this->flashMsg('error', '添加失败，请重试');
            }
        }

        // 获取角色对应的所有用户
        $user = [];

        // 获取所有用户
        $allUser = ArrayHelper::index(AuthAssign::getAllUser(), 'user_id');

        return $this->render('add', [
            'model' => $model,
            'user' => [],
            'allUser' => $allUser,
        ]);
    }

    /**
     * 编辑配置
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $get = \Yii::$app->request->get();

        $model = AuthRole::findOne($id);

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (AuthRole::find()->where(['and', ['!=', 'role_id', $id], ['name' => $get['name']]])->count()) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '角色名已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '角色名不存在'
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

        // 获取角色对应的所有用户
        $user = AuthAssign::getUseridByRole($id);

        // 获取所有用户
        $allUser = ArrayHelper::index(AuthAssign::getAllUser(), 'user_id');

        return $this->render('edit', [
            'model' => $model,
            'user' => $user,
            'allUser' => $allUser,
        ]);
    }

    /**
     * 删除status=0的数据
     * @param $id
     */
    public function actionDelete($id)
    {
        $model = AuthRole::findOne(['role_id' => $id]);
        if (!$model) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '数据不存在',
            ]);
        }

        // 同步删除关联的用户id
        if ($model->delete()) {
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
