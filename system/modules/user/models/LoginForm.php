<?php
namespace system\modules\user\models;

use system\modules\user\components\UserIdentity;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $verifyCode;
    public $rememberMe = false;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            //['password', 'validatePassword'],
            //['verifyCode', 'captcha'],
            //['verifyCode', 'captcha','captchaAction'=>'/user/default/captcha','message'=>'验证码不正确！'],
            ['verifyCode', 'string'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '无效的用户名或者密码');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                // 记录错误日志
                UserLoginError::saveData('ip', Yii::$app->request->getUserIP());
                UserLoginError::saveData('username', $this->username);

                return '无效的用户名或者密码';
            }

            // 用户状态异常； @TODO 用户连续登录错误xx次，账号立即被锁定，不能再登录，加一个配置参数，是否锁定用户
            if ($user->status != User::STATUS_ACTIVE) {
                $user_status_list = Yii::$app->systemConfig->getValue('USER_STATUS_LIST', []);
                if (!$user_status_list[$user->status]) {
                    return '用户状态异常，不能登录，请联系管理员;';
                } else {
                    return "用户已被{$user_status_list[$user->status]}，不能登录，请联系管理员;";
                }
            }

            // 写登录日志
            Yii::$app->systemLog->write([
                'type' => 'login',
                'target_id' => $user->user_id,
                'user_id' => $user->user_id,
                'content' => "用户：{$user->realname} 登录成功",
            ]);

            return \Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = UserIdentity::findByUsername($this->username);
        }

        return $this->_user;
    }


}
