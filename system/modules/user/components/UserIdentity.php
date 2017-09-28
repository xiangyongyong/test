<?php
namespace system\modules\user\components;

use Yii;
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/15
 * Time: 下午2:23
 */
class UserIdentity extends \system\modules\user\models\User implements \yii\web\IdentityInterface
{

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * 根据用户名查找用户
     * @param $username
     * @return static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }


    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }



}