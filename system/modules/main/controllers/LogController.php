<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/9
 * Time: 下午1:45
 */

namespace system\modules\main\controllers;


use system\modules\main\models\Log;

class LogController extends BaseController
{
    public function actionIndex()
    {
        $keyword = \Yii::$app->request->get('keyword'); // 搜索关键字
        $type = \Yii::$app->request->get('type'); // 分组

        $query = Log::find();

        // 分组
        if ($type) {
            $query->andWhere(['type' => $type]);
        }

        // 搜索关键字
        if (trim($keyword)) {
            $query->andWhere(['or', ['like', 'content', $keyword], ['like', 'ip', $keyword], ['like', 'user_id', $keyword]]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
            'totalCount' => $query->count(),
        ]);

        $data = $query
            ->with('user')
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['log_id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'logs' => $data,
            'pagination' => $pagination,
        ]);
    }
}