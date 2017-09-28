<?php

namespace system\modules\notify\models;

use Yii;
use yii\web\Application;

/**
 * This is the model class for table "tab_user_notify".
 *
 * @property integer $id
 * @property integer $is_read
 * @property integer $user_id
 * @property integer $notify_id
 * @property integer $created_at
 */
class UserNotify extends \yii\db\ActiveRecord
{

    const NOT_READ = 0; // 未读
    const HAS_READ = 1; // 已读

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_user_notify';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_read', 'user_id', 'notify_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '流水id',
            'is_read' => '是否已读',
            'user_id' => '用户id',
            'notify_id' => '关联notify',
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
     * 关联notify表
     * @return \yii\db\ActiveQuery
     */
    public function getNotify()
    {
        return $this->hasOne(Notify::className(), ['notify_id' => 'notify_id'])->with('sender');
    }

    /**
     * 获取用户的消息列表
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getData($user_id)
    {
        return self::find()
            ->with('notify')
            ->where(['user_id' => $user_id])
            ->andWhere(['is_read' => self::NOT_READ])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public static function getNotRead($user_id, $limit = '')
    {

    }

    /**
     * 设置为已读
     * @param $user_id
     * @param $notifyIds
     * @return int
     */
    public static function read($user_id, $notifyIds)
    {
        if (is_array($notifyIds)) {
            $notifyIds = implode(',', $notifyIds);
        }
        $sql = "update ".self::tableName()." set is_read = 1 where user_id={$user_id} AND notify_id IN ({$notifyIds})";
        return Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 全部设置为已读
     * @param $user_id
     * @return int
     */
    public static function readAll($user_id)
    {
        $sql = "update ".self::tableName()." set is_read = 1 where user_id={$user_id}";
        return Yii::$app->db->createCommand($sql)->execute();
    }




}
