<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/4/1
 * Time: 下午8:58
 */

namespace system\modules\main\controllers;

use yii;
use system\core\utils\Attach;

class AttachController extends BaseController
{
    // 禁用csrf
    public $disableCsrfAction = ['upload-avatar'];

    /**
     * 上传头像，上传成功后将url返回给客户端
     */
    public function actionUploadAvatar()
    {
        $dir = 'avatar/'.date('Y').'/'.date('m');

        $avatarFile = yii\web\UploadedFile::getInstanceByName('avatarFile');

        $res = Attach::saveUpload($avatarFile, ['dir' => $dir, 'resetSize' => 400, 'ext' => ['jpeg', 'jpg', 'png', 'gif']]);

        if ($res) {
            return $this->ajaxReturn([
                'code' => 0,
                'message' => '上传成功',
                'data' => [
                    'src' => Attach::$relativePath
                ],
            ]);
        } else {
            return $this->ajaxReturn([
                'code' => 1,
                'message' => Attach::$error,
            ]);
        }
    }
}