<?php

namespace system\modules\user\models;

use system\modules\gateway\models\Gateway;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tab_user_gateway_group".
 *
 * @property integer $user_id
 * @property integer $group_id
 */
class UserGatewayGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_user_gateway_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'target_id', 'type'], 'required'],
            [['user_id', 'target_id'], 'integer'],
            [['type'], 'string'],
            [['user_id', 'target_id', 'type'], 'unique', 'targetAttribute' => ['user_id', 'target_id', 'type'], 'message' => 'The combination of 用户id, 目标id，通过type字段确定 and 关联类型，用户到组，用户到网关 has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户id',
            'group_id' => '网关组id',
            'type' => '关联类型，用户到组，用户到网关',
        ];
    }

    /**
     * 获取用户获取所有的网关组id数组
     * @param $user_id
     * @return array
     */
    public static function getGroupsByUser($user_id)
    {
        $data = self::find()->where(['user_id' => $user_id,'type' => 'group'])->asArray()->all();
        if (!$data) {
            return [];
        }
        return ArrayHelper::getColumn($data, 'target_id');
    }

    /**
     * 保存数据
     * @param $user_id int 用户id
     * @param $groups array 网关组id数组
     * @return bool|int
     */
    public static function saveData($user_id, $groups)
    {
        // 删除所有原来的记录
        self::deleteAll(['user_id' => $user_id]);
        if (empty($groups)) {
            return true;
        }

        $newGroup = [];
        foreach ($groups as $group_id) {
            if (!$group_id) {
                continue;
            }
            $newGroup[] = [$user_id, $group_id,'group'];
        }

        return Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['user_id', 'target_id','type'], $newGroup)->execute();
    }

    /**
     * 根据组id获取所有的用户id数组
     * @param $group_id
     * @return array
     */
    public static function getUsersByGroup($group_id)
    {
        $data = self::find()->where(['target_id' => $group_id,'type'=>'group'])->asArray()->all();
        if (!$data) {
            return [];
        }

        return ArrayHelper::getColumn($data, 'user_id');
    }

    public static function getUsersByGateway($target_id){
        $data = self::find()->where(['target_id' => $target_id,'type'=>'gateway'])->asArray()->one();
        if (!$data) {
            return [];
        }

        return $data;
    }

}
