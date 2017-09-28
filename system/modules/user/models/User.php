<?php

namespace system\modules\user\models;

use system\modules\role\models\AuthAssign;
use system\modules\user\components\UserIdentity;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%tab_user}}".
 *
 * @property integer $user_id
 * @property string $username
 * @property string $realname
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $phone
 * @property string $email
 * @property integer $role_id
 * @property integer $status
 */
class User extends \yii\db\ActiveRecord
{

    public $password;
    //public $avatarFile;
    public $cccc='chen';

    const STATUS_ACTIVE = 0; // 正常
    const STATUS_DISABLED = 1; // 禁用
    const STATUS_LOCK = 2; // 锁定，再认证需要使用验证码，登录成功后改为正常
    const STATUS_DELETE = 3; // 删除，不能再使用

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['username'], 'required'],
            [['role_id', 'status'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'password', 'avatar'], 'string', 'max' => 255],
            [['realname'], 'string', 'max' => 64],
            [['auth_key', 'phone'], 'string', 'max' => 32],
            [['username'], 'unique'],
            ['email', 'email'],
            //['avatarFile', 'file', 'extensions' => 'jpg, jpeg, png, gif', 'maxSize'=>'1024000'],
            //['tempAvatar', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户id',
            'username' => '用户名',
            'realname' => '姓名',
            'avatar' => '头像',
            'password_hash' => '密码',
            'password_reset_token' => '密码重设令牌',
            'phone' => '手机号',
            'email' => '邮箱',
            'role_id' => '角色',
            'status' => '状态',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->password) {
                // 如果有设置密码
                $this->setPassword($this->password);
            }

            return true;
        }

        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * 获取所有有效用户
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllUser()
    {
        $data = self::find()
            ->select(['user_id', 'username', 'realname', 'phone', 'email', 'status', 'avatar'])
            ->where(['status' => self::STATUS_ACTIVE])
            ->asArray()
            ->all();
        return ArrayHelper::index($data, 'user_id');
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 用户状态列表
        $user_status_list = Yii::$app->systemConfig->getValue('USER_STATUS_LIST', []);

        // 写日志
        if ($insert) {
            $data = [
                '用户名' => $this->username,
                '姓名' => $this->realname,
                '手机' => $this->phone,
                'email' => $this->email,
                //'状态' => $user_status_list[$this->status],
            ];
            $content = '新增了用户：' . $this->username . '; 详细数据：' . Json::encode($data);
        } else {
            // 记录日志的字段
            $dataLog = ['username', 'realname', 'phone', 'email', 'status'];

            // 如果删除用户，那么更新权限 @todo 此处最好改成事件方式进行解耦合
            if ($this->status == self::STATUS_DELETE) {
                //$this->trigger('USER_DELETE');
                AuthAssign::deleteUser($this->user_id);
            }

            $content = '编辑了用户：' . $this->username.'; ';
            foreach ($changedAttributes as $key => $oldValue) {
                if($this->$key == $oldValue) {
                    continue;
                }

                if ($key == 'password_hash') {
                    $content .= '密码修改；';
                    continue;
                }

                if ($key == 'avatar') {
                    $content .= '修改头像; ';
                    continue;
                }

                if (!in_array($key, $dataLog)) {
                    continue;
                }

                if ($key == 'status' && !empty($user_status_list)) {
                    $content .= '状态:' . $user_status_list[$oldValue] . '=>' . $user_status_list[$this->status] . ';';
                } else {
                    $content .= $this->attributeLabels()[$key] . ':' . $oldValue . '=>' . $this->$key . '; ';
                }
            }
        }

        Yii::$app->systemLog->write([
            'type' => 'user', // 类型
            'target_id' => $this->user_id, // 目标
            'content' => $content, // 内容
        ]);
    }

    /**
     * @inheritDoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        // 写日志
        Yii::$app->systemLog->write([
            'type' => 'user', // 类型
            'target_id' => $this->user_id, // 目标
            'content' => '彻底删除了用户：'.$this->username.';', // 内容
        ]);
    }

    /**
     * 获取用户绑定的组
     * @return \yii\db\ActiveQuery
     */
    public function getGatewaygroup()
    {
        return $this->hasMany(UserGatewayGroup::className(), ['user_id' => 'user_id']);
    }

    /**
     * 锁定用户，当用户是正常状态时锁定用户
     * @param $condition
     * @return bool
     */
    public static function lockUser($condition)
    {
        $model = self::findOne($condition);
        if ($model) {
            if ($model->status == self::STATUS_ACTIVE) {
                $model->status = self::STATUS_LOCK;
                return $model->save();
            }
        }

        return false;
    }

}
