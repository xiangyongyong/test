<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 上午10:05
 */

namespace system\modules\gateway\controllers;


use system\modules\gateway\models\Device;
use system\modules\gateway\models\Factory;

class DeviceController extends BaseController
{
    /**
     * 设备列表
     * @return string
     */
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        $device_type = \Yii::$app->request->get('device_type'); // 设备状态

        $query = Device::find();

        // 分组
        if ($device_type) {
            $query->andWhere(['dev_type' => $device_type]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'mac', $keyword], ['like', 'ip', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            ->with('gateway')
            ->with('factory')
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['add_time' => SORT_DESC])
            ->all();

        //echo '<pre>';print_r($data);exit;
        return $this->render('index', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }

    /**
     * 设备编辑
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $get = \Yii::$app->request->get();

        $model = Device::findOne($id);

        if (!$model) {
            $this->flashMsg('error', '数据不存在');
            return $this->redirect('index');
        }

        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
                $this->flashMsg('ok', '修改成功');
                return $this->redirect('index');
            } else {
                $this->flashMsg('error', '修改失败，请重试');
            }
        }

        // 厂商列表
        $factory = Factory::getListMap();

        return $this->render('edit', [
            'model' => $model,
            'factory' => $factory,
        ]);
    }
}