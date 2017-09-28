<?php

namespace system\modules\notify\models;

use Yii;

/**
 * This is the model class for table "tab_subscription".
 *
 * @property integer $id
 * @property integer $target
 * @property string $targetType
 * @property string $action
 * @property integer $user_id
 * @property integer $created_at
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_subscription';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target', 'user_id', 'created_at'], 'integer'],
            [['targetType', 'action'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '流水id',
            'target' => '目标的ID，比如文章id3',
            'targetType' => '目标的类型，比如文章类型article',
            'action' => '订阅动作,如: comment/like/post/update',
            'user_id' => '用户id',
            'created_at' => '创建时间',
        ];
    }
}
