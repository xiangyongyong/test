<?php
namespace system\modules\user\controllers;

use system\modules\user\models\UserLoginError;
use Yii;
use system\modules\user\models\LoginForm;
use yii\captcha\CaptchaValidator;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * 默认控制器,登录等操作，不受权限系统控制
 */
class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * 处理验证码
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fontFile' => '@webroot/theme/default/fonts/adele-light-webfont.ttf',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'width' => 109,
                'height' => 38,
                'transparent' => false,  // 背景是否透明
                'backColor' => 0x009688, // 黑色背景，默认白色
                'foreColor' => 0xFFFFFF, // 字体颜色，白色
                'maxLength' => 6,
                'minLength' => 4,
                'offset' => 3,
            ],
        ];
    }

    /**
     * 用户登录
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionLogin()
    {
        // 如果已经登录，那么直接跳转到首页
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // 判断ip和用户名是否可以登录
        if (!$this->_canLogin()) {
            throw new ForbiddenHttpException('您的ip已经被锁定，不允许登录！请稍后再试！');
        }

        // 是否需要验证码
        $showCaptcha = $this->_showCaptcha();

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post(), '')) {
            // 验证用户名
            if (!UserLoginError::canLogin('username', $model->username)) {
                Yii::$app->getSession()->setFlash('error', '此用户已被锁定，不允许登录！请稍后再试！');
                return $this->refresh();
            }

            // 验证码
            if ($showCaptcha) {
                $validator = new CaptchaValidator([
                    'captchaAction' => '/user/default/captcha'
                ]);
                if (!$validator->validate($model->verifyCode, $error)) {
                    Yii::$app->getSession()->setFlash('error', '验证码不正确！');
                    return $this->refresh();
                }
            }

            $res = $model->login();
            if ($res === true) {
                Yii::$app->getSession()->setFlash('ok', '登录成功');
                return $this->goHome();
            } else {
                $message = '';
                if ($res) {
                    $message = $res;
                } else {
                    if ($model->errors) {
                        foreach ($model->errors as $error) {
                            if (is_array($error)) {
                                foreach ($error as $item) {
                                    $message .= $item.' ';
                                }
                            } else {
                                $message .= $error;
                            }
                        }
                    }
                }
                // 登录失败
                Yii::$app->getSession()->setFlash('error', '登录失败; '.$message);
                return $this->refresh();
            }
        }

        return $this->renderPartial('login', [
            'showCaptcha' => $showCaptcha, // 是否显示验证码
        ]);
    }

    /**
     * 是否允许登录
     * @return bool
     */
    private function _canLogin()
    {
        return UserLoginError::canLogin('ip', Yii::$app->request->getUserIP());
    }

    /**
     * 是否显示验证码
     * @return bool
     */
    private function _showCaptcha()
    {
        // 先判断是否要显示验证码
        $show_captcha = Yii::$app->systemConfig->getValue('USER_LOGIN_SHOW_CAPTCHA', 0);
        if ($show_captcha == 1) {
            return true;
        }

        return UserLoginError::showCaptcha();
    }

    /**
     * 登出系统
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        if (Yii::$app->user->logout()) {
            Yii::$app->getSession()->setFlash('ok', '已成功登出');
        }

        return $this->goHome();
    }


}
