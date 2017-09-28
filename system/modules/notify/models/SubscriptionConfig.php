<?php

namespace system\modules\notify\models;

use Yii;

/**
 * This is the model class for table "tab_subscription_config".
 *
 * @property integer $user_id
 * @property string $config
 */
class SubscriptionConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tab_subscription_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['config'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户id',
            'config' => '配置',
        ];
    }
}
