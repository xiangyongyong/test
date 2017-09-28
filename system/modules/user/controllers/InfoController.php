<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/16
 * Time: 上午11:46
 */

namespace system\modules\user\controllers;


use system\modules\user\models\InfoForm;
use system\modules\user\models\User;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class InfoController extends BaseController
{
    /**
     * 修改个人密码
     * @return string|\yii\web\Response
     */
    public function actionPassword()
    {
        if (\Yii::$app->request->isPost) {
            $model = new InfoForm();
            $model->scenario = 'password';
            if ($model->load(\Yii::$app->request->post(), '')) {
                $res = $model->changePassword();
                if ($res === true) {
                    $this->flashMsg('ok', '密码修改成功！');
                } else {
                    $message = $res ?: '';
                    $this->flashMsg('error', '密码修改失败；'.$message);
                }
            }

            return $this->refresh();
        }

        $model = $this->getUser();

        return $this->render('password', [
            'model' => $model
        ]);
    }

    /**
     * 修改个人信息
     * @return string|\yii\web\Response
     */
    public function actionUpdate()
    {
        if (\Yii::$app->request->isPost) {
            $model = new InfoForm();
            $model->scenario = 'update';

            if ($model->load(\Yii::$app->request->post(), '')) {
                $res = $model->updateInfo();
                if ($res === true) {
                    $this->flashMsg('ok', '资料修改成功！');
                } else {
                    $message = $res ?: '';
                    $this->flashMsg('error', '资料修改失败；'.$message);
                }
            }

            return $this->refresh();
        }

        $model = $this->getUser();

        return $this->render('update', [
            'model' => $model
        ]);

    }

    /**
     * 获取当前用户model
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    private function getUser()
    {
        $model = User::findOne(\Yii::$app->user->identity->getId());
        if (!$model) {
            throw new NotFoundHttpException('数据不存在！');
        }

        return $model;
    }
}