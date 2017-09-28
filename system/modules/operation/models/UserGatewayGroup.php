<?php

namespace system\modules\operation\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tab_user_gateway_group".
 *
 * @property integer $user_id
 * @property integer $target_id
 * @property string $type
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
            [['type'], 'string', 'max' => 255],
            [['user_id', 'target_id'], 'unique', 'targetAttribute' => ['user_id', 'target_id'], 'message' => 'The combination of User ID and Target ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'target_id' => 'Target ID',
            'type' => 'Type',
        ];
    }
    
    /**
     * 根据组id获取所有的用户id数组
     * @param $group_id
     * @return array
     */
    public static function getUsersByGroup($group_id, $is_group = 0) {
        if($is_group == 1){
            $data = self::find()->where(['type' => 'gateway'])->where(['target_id' => $group_id])->asArray()->all();
        }else{
            $data = self::find()->where(['type' => 'group'])->where(['target_id' => $group_id])->asArray()->all();
        }
        
        if (!$data) {
            return [];
        }

        return ArrayHelper::getColumn($data, 'user_id');
    }
}
