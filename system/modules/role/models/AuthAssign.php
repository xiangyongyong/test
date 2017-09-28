<?php

namespace system\modules\role\models;

use system\modules\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tab_auth_assign}}".
 *
 * @property integer $role_id
 * @property integer $user_id
 */
class AuthAssign extends \yii\db\ActiveRecord
{
    // 超管的角色id
    const SUPER_ROLE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_auth_assign}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'user_id'], 'required'],
            [['role_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => '角色id',
            'user_id' => '用户id',
        ];
    }

    /**
     * 关联user表
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id'])->select(['user_id', 'username', 'realname']);
    }

    /**
     * 关联role表
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AuthRole::className(), ['role_id' => 'role_id']);
    }

    /**
     * 根据角色id获取数据
     * @param $role_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUseridByRole($role_id)
    {
        $data = self::find()
            ->where(['role_id' => $role_id])
            ->asArray()
            ->all();
        return ArrayHelper::getColumn($data, 'user_id');
    }

    /**
     * 根据role_id获取所有用户
     * @param $role_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUserByRole($role_id)
    {
        return self::find()
            ->where(['role_id' => $role_id])
            ->with('user')
            //->with('role')
            ->asArray()
            ->all();
    }

    /**
     * 获取所有用户
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllUser()
    {
        return User::getAllUser();
    }

    /**
     * 保存数据
     * @param $role_id int
     * @param $user_ids array
     * @return bool
     */
    public static function saveData($role_id, $user_ids)
    {
        // 删除所有存在的数据
        self::deleteRole($role_id);
        foreach ($user_ids as $user_id) {
            $model = new self();
            $model->role_id = $role_id;
            $model->user_id = $user_id;
            $model->save();
        }

        return true;
    }

    /**
     * 按照组对组下面对所有用户进行分组
     * @return array
     */
    public static function getUserGroupByRole()
    {
        $sql = "SELECT u.user_id, u.username, u.realname, u.avatar, r.role_id 
                FROM tab_auth_role r 
                LEFT JOIN tab_auth_assign a ON r.role_id=a.role_id 
                LEFT JOIN `tab_user` u ON u.user_id=a.user_id 
                WHERE u.user_id>0";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return ArrayHelper::index($data, null, 'role_id');
    }

    /**
     * 删除角色id
     * @param $role_id int
     * @return int
     */
    public static function deleteRole($role_id)
    {
        return self::deleteAll(['role_id' => $role_id]);
    }

    /**
     * 删除用户id
     * @param $user_id int
     * @return int
     */
    public static function deleteUser($user_id)
    {
        return self::deleteAll(['user_id' => $user_id]);
    }

    /**
     * 根据用户id获取此用户的所有权限数组
     * @param $user_id
     * @return array
     */
    public static function getPermissionByUser($user_id)
    {
        // 根据用户id获取用户的角色，然后根据角色获取权限
        $sql = "SELECT r.permission 
                FROM `tab_auth_assign` as a 
                LEFT JOIN tab_auth_role as r ON a.role_id=r.role_id 
                WHERE a.user_id=".$user_id;
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        //print_r($data);
        $allPermission = [];
        foreach ($data as $item) {
            if ($item['permission']) {
                $permission = explode(',', $item['permission']);
                $allPermission = array_merge($allPermission, $permission);
            }
        }
        //print_r($allPermission);
        return $allPermission;
    }

    /**
     * 判断用户是否是超级管理员
     * @param $user_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function isSuper($user_id)
    {
        return self::find()->where(['user_id' => $user_id, 'role_id' => self::SUPER_ROLE])->one();
    }


}
