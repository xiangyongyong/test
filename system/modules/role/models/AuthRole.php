<?php

namespace system\modules\role\models;

use system\modules\user\models\User;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%tab_auth_role}}".
 *
 * @property integer $role_id
 * @property string $name
 * @property string $description
 * @property string $permission
 */
class AuthRole extends \yii\db\ActiveRecord
{
    // 成员
    public $users;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_auth_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['permission', 'users'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => '角色id',
            'name' => '角色名称',
            'description' => '描述',
            'permission' => '权限',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // 权限
            if (is_array($this->permission)) {
                $this->permission = implode(',', $this->permission);
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $userMsg = '';
        if ($this->users) {
            if (!AuthAssign::saveData($this->role_id, $this->users)) {
                Yii::$app->getSession()->setFlash('error', '用户绑定失败；请检查');
            } else {
                // 写日志
                $userMsg = '绑定了用户id：' . Json::encode($this->users) . ';';
            }
        }

        // 写日志
        if ($insert) {
            $data = [
                //'名称' => $this->name,
                '描述' => $this->description,
                '权限' => $this->permission,
            ];
            $content = '新增了角色：' . $this->name . '; 详细数据：' . Json::encode($data) . ';';
        } else {
            $content = '编辑了角色：' . $this->name.'; ';
            foreach ($changedAttributes as $key => $oldValue) {
                if($this->$key == $oldValue) {
                    continue;
                }
                $content .= $this->attributeLabels()[$key] . ':' . $oldValue . '=>' . $this->$key . '; ';
            }
        }

        Yii::$app->systemLog->write([
            'type' => 'role', // 类型
            'target_id' => $this->role_id, // 目标
            'content' => $content . ' ' . $userMsg, // 内容
        ]);
    }

    /**
     * @inheritDoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        // 删除此角色和用户的所有关联
        AuthAssign::deleteRole($this->role_id);

        // 写日志
        Yii::$app->systemLog->write([
            'type' => 'role', // 类型
            'target_id' => $this->role_id, // 目标
            'content' => '删除了角色：' . $this->name . ';', // 内容
        ]);
    }


    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if (is_string($this->permission)) {
            $this->permission = explode(',', $this->permission);
        }
    }


}
