<?php

namespace system\modules\main\models;

use system\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "tab_comment".
 *
 * @property integer $comment_id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $target_type
 * @property integer $agree
 * @property string $content
 * @property integer $state
 * @property integer $create_at
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'target_id', 'agree', 'state', 'create_at'], 'integer'],
            [['content'], 'string'],
            [['target_type'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => '评论id',
            'user_id' => '评论人id',
            'target_id' => '评论目标id',
            'target_type' => '评论目标类型',
            'agree' => '同意次数',
            'content' => '评论内容',
            'state' => '是否显示;0显示，1不显示',
            'create_at' => '评论时间',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // 用户id
            $this->user_id = Yii::$app->user->identity->getId();
            $this->create_at = time();

            return true;
        }

        return false;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id'])->select(['user_id', 'username', 'realname', 'avatar']);
    }


    /**
     * 根据target_type和id获取数据
     * @param $target_type
     * @param $target_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getData($target_type, $target_id)
    {
        return self::find()
            ->with('user')
            ->where(['target_type' => $target_type, 'target_id' => $target_id])
            ->asArray()
            ->all();
    }
}
