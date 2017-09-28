<?php

namespace system\modules\notify\models;

use system\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "tab_notify".
 *
 * @property integer $notify_id
 * @property string $content
 * @property integer $type
 * @property integer $target
 * @property string $target_type
 * @property string $action
 * @property integer $sender_id
 * @property integer $created_at
 */
class Notify extends \yii\db\ActiveRecord
{
    const TYPE_ANNOUNCE = 1; // 公告
    const TYPE_REMIND = 2; // 提醒
    const TYPE_MESSAGE = 3; // 消息
    const TYPE_URGE = 4; //催单

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_notify';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['type', 'target', 'sender_id', 'created_at'], 'integer'],
            [['target_type', 'action'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notify_id' => '流水id',
            'content' => '消息的内容,公告和消息用到',
            'type' => '消息的类型,1: 公告Announce,2: 提醒Remind,3:信息Message',
            'target' => '目标的ID,比如文章id',
            'target_type' => '目标的类型,比如文章article',
            'action' => '提醒信息的动作类型,比如评论文章comment',
            'sender_id' => '发送者的ID,比如user_id',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->created_at = time();
            }

            return true;
        }

        return false;
    }

    /**
     * 关联用户表
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['user_id' => 'sender_id']);
    }

    public function getUserNotify()
    {
        return $this->hasOne(UserNotify::className(), ['notify_id' => 'notify_id']);
    }


}
