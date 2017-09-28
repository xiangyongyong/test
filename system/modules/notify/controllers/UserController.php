<?php

namespace system\modules\notify\controllers;

use system\modules\notify\models\UserNotify;


/**
 * 用户消息
 */
class UserController extends BaseController
{
    /**
     * 我的消息，当前登录用户
     * @return string
     */
    public function actionMy()
    {
        $user_id = \Yii::$app->user->identity->getId();

        $get = \Yii::$app->request->get();


        if (isset($get['ajax'])) {
            // 设置某一条为已读
            if ($get['ajax'] == 'changeToRead') {
                if (isset($get['id'])) {
                    $res = \Yii::$app->systemNotify->read($user_id, $get['id']);
                    if ($res) {
                        return $this->ajaxReturn([
                            'code' => 0,
                            'message' => '操作成功',
                        ]);
                    }
                }
            }
            // 设置全部已读
            else if ($get['ajax'] == 'readAll') {
                $res = \Yii::$app->systemNotify->readAll($user_id);
                if ($res) {
                    return $this->ajaxReturn([
                        'code' => 0,
                        'message' => '操作成功',
                    ]);
                }
            }

            return $this->ajaxReturn([
                'code' => 1,
                'message' => '操作失败，请重试',
            ]);
        }

        $state = \Yii::$app->request->get('state');

        $query = UserNotify::find();

        $query->andWhere(['user_id' => $user_id]);

        if (!$state) {
            $query->andWhere(['is_read' => UserNotify::NOT_READ]);
        };

        $notify = $query->with('notify')
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        //echo '<pre>';print_r($notify);exit;
        return $this->render('my', [
            'data' => $notify
        ]);
    }

    public function actionNotread()
    {
        $user_id = \Yii::$app->user->identity->getId();

        $query = UserNotify::find()->where(['user_id' => $user_id, 'is_read' => UserNotify::NOT_READ]);
        $count = $query->count();
        $data = $query->with('notify')->limit(5)->orderBy(['id' => SORT_DESC])->asArray()->all();
        $html = $this->renderPartial('notread', [
            'count' => $count, // 总数
            'data' => $data, //数据
        ]);

        return $this->ajaxReturn([
            'code' => 0,
            'message' => 'ok',
            'data' => [
                'html' => $html,
                'count' => $count, // 是否有新的未读信息
            ],
        ]);
    }
}