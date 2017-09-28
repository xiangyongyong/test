<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/7
 * Time: 下午3:17
 */

namespace system\modules\gateway\controllers;

use system\modules\gateway\models\PortInfo;

class PortController extends BaseController
{
    // 端口信息列表
    public function actionList()
    {
        $query = PortInfo::find();

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['add_time' => SORT_DESC])
            ->all();

        return $this->render('list', [
            'data' => $data,
            'pagination' => $pagination,
        ]);
    }
}