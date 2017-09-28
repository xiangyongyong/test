<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:05
 */

namespace system\modules\gateway\controllers;

use system\modules\gateway\models\Factory;

class FactoryController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword');

        $query = Factory::find();

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'factory_name', $keyword], ['like', 'name', $keyword], ['like', 'telephone', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['factory_id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }

    /**
     * 添加数据
     */
    public function actionAdd()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (Factory::findOne(['factory_name' => $get['factory_name']])) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '厂商名称已存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '厂商名称可用'
                ]);
            }
        }

        $model = new Factory();

        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
                $this->flashMsg('ok', '添加成功');
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
     * 修改数据
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $get = \Yii::$app->request->get();

        $model = Factory::findOne($id);

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (Factory::find()->where(['and', ['!=', 'factory_id', $id], ['factory_name' => $get['factory_name']]])->count()) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '厂商名称已存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '厂商名称可用'
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
     * 删除厂商
     * @param $id
     */
    public function actionDelete($id)
    {
        $model = Factory::findOne(['factory_id' => $id]);
        if (!$model) {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => '数据不存在',
            ]);
        }

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