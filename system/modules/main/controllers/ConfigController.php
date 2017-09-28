<?php
/**
 * 系统配置项；公用
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午2:00
 */

namespace system\modules\main\controllers;


use system\modules\main\models\Config;

class ConfigController extends BaseController
{

    /**
     * 配置管理
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        $group = \Yii::$app->request->get('group'); // 分组
        $status = \Yii::$app->request->get('status', 0); // 状态，默认只显示status=0的数据

        $query = Config::find();

        // 状态
        if ($status == 0) {
            $query->andWhere(['status' => $status]);
        }

        // 分组
        if ($group) {
            $query->andWhere(['group' => $group]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'name', $keyword], ['like', 'title', $keyword], ['like', 'extra', $keyword], ['like', 'remark', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['sort' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
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
            if (Config::findOne(['name' => $get['name']])) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '标识已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '标识不存在'
                ]);
            }
        }

        $model = new Config();
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
     * 编辑配置
     * @return \yii\web\Response|string
     */
    public function actionEdit()
    {
        $get = \Yii::$app->request->get();

        if (isset($get['id'])) {
            $model = Config::findOne($get['id']);
        }
        else if (isset($get['name'])) {
            $model = Config::findOne(['name' => $get['name']]);
        }
        else {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (isset($get['action']) && $get['action'] == 'name-exit') {
            if (Config::find()->where(['and', ['!=', 'id', $model->id], ['name' => $get['name']]])->count()) {
                return $this->ajaxReturn([
                    'code' => 1,
                    'message' => '标识已经存在',
                ]);
            } else {
                return $this->ajaxReturn([
                    'code' => 0,
                    'message' => '标识不存在'
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
     * 删除status=0的数据
     * @param $id
     */
    public function actionDelete($id)
    {
        $model = Config::findOne(['status' => 0, 'id' => $id]);
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

    // 设置
    public function actionSetting()
    {
        
    }
}