<?php

namespace system\modules\operation\controllers;

use system\modules\main\models\Log;

/**
 * Description of LogController
 *
 * @author Administrator
 */
class LogController extends BaseController
{
    public function actionIndex()
    {
        $type = \Yii::$app->request->get('type'); // 分组

        $query = Log::find();

        // 分组
        if ($type) {
            $query->andWhere(['type' => $type]);
        }

        //分页
        $pagination = new \yii\data\Pagination([
            'pageSize' => \Yii::$app->systemConfig->getValue('LIST_ROWS', 20),
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
