<?php
/**
 * 用户组件类
 */
namespace system\modules\user\components;

use system\modules\role\models\AuthAssign;

/**
 * User组件扩展版.
 * User: ligang
 * Date: 2017/3/15
 * Time: 下午2:23
 */
class User extends \yii\web\User
{
    //当前用户的权限列表
    private $_access = null;

    /**
     * 重写can方法
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        //超级管理员不判断权限
        if(AuthAssign::isSuper(\Yii::$app->user->identity->getId())){
            return true;
        }

        if($this->_access==null){
            //获取当前管理员的所有权限
            $userPermission = AuthAssign::getPermissionByUser(\Yii::$app->user->identity->getId());
            $this->_access = $userPermission;
        }

        if(in_array($permissionName, $this->_access)){
            return true;
        }

        return false;
    }

}