<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/31
 * Time: 下午3:23
 */

namespace system\modules\user\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class InfoForm extends Model
{
    public $avatar;
    public $realname;
    public $phone;
    public $email;
    public $oldPassword; // 旧密码
    public $newPassword; // 新密码
    public $newPasswordRepeat; // 重复密码

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['realname', 'phone', 'email', 'avatar'], 'required', 'on' => 'update'],
            [['realname', 'phone', 'email'], 'trim', 'on' => 'update'],
            [['oldPassword', 'newPassword', 'newPasswordRepeat'], 'required', 'on' => 'password'],
            [['oldPassword', 'newPassword', 'newPasswordRepeat'], 'trim', 'on' => 'password'],
            ['email', 'email'],
            //['oldPassword', 'validatePassword', 'on' => 'password']
        ];
    }

    /**
     * 验证密码
     * @param $attribute
     * @param $params
     * @return string
     */
    /*public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, '原始密码不正确');
            }
        }
    }*/

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'avatar' => '头像',
            'realname' => '姓名',
            'phone' => '手机',
            'email' => '邮箱',
            'oldPassword' => '原始密码',
            'newPassword' => '新密码',
            'newPasswordRepeat' => '确认密码',
        ];
    }

    /**
     * @inheritDoc
     */
    public function scenarios()
    {
        return [
            'update' => ['avatar', 'realname', 'phone', 'email'],
            'password' => ['oldPassword', 'newPassword', 'newPasswordRepeat'],
        ];
    }

    private $_user;

    /**
     * 查找用户
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(\Yii::$app->user->identity->getId());
        }

        return $this->_user;
    }

    /**
     * 更改密码
     * @return bool|string
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            //print_r($this->errors);
            $errors = $this->getFirstErrors();
            foreach ($errors as $error) {
                return $error;
            }
        }

        if ($this->newPassword != $this->newPasswordRepeat) {
            return '确认密码输入不一致！';
        }

        if ($this->oldPassword == $this->newPassword) {
            return '输入的三个密码相同！';
        }

        $user = $this->getUser();

        if (!$user->validatePassword($this->oldPassword)) {
            return '原始密码不正确！';
        }

        $user->setPassword($this->newPassword);
        return $user->save();
    }

    /**
     * 更新资料
     * @return bool
     */
    public function updateInfo()
    {
        if (!$this->validate()) {
            $errors = $this->getFirstErrors();
            foreach ($errors as $error) {
                return $error;
            }
        }

        $user = $this->getUser();

        $user->avatar = $this->avatar;
        $user->realname = $this->realname;
        $user->phone = $this->phone;
        $user->email = $this->email;

        return $user->save();
    }

}