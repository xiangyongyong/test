<?php

namespace system\modules\main\models;

use Yii;

/**
 * This is the model class for table "tab_comment_agree".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $user_id
 * @property integer $create_at
 */
class CommentAgree extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_comment_agree';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id', 'create_at'], 'required'],
            [['comment_id', 'user_id', 'create_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'comment_id' => '评论id',
            'user_id' => '用户id',
            'create_at' => '创建时间',
        ];
    }
}
