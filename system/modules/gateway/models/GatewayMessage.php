<?php

namespace system\modules\gateway\models;

use Yii;

/**
 * 网关消息
 *
 * @property integer $id
 * @property integer $gateway_id
 * @property integer $if_port
 * @property integer $type
 * @property string $content
 * @property integer $created_time
 * @property integer $is_read
 */
class GatewayMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_gateway_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gateway_id', 'if_port', 'type', 'created_time', 'is_read'], 'integer'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '流水id',
            'gateway_id' => '网关id',
            'if_port' => '网口号',
            'type' => '类型',
            'content' => '消息内容',
            'created_time' => '创建时间',
            'is_read' => '是否已读'
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 新增报警消息后加入工单中
        if ($insert) {
            $msg_type = Yii::$app->systemConfig->getValue('GATEWAY_MSG_TYPE_LIST', []);

            // 生成一个消息; 网关5-网关名称报警,机箱被打开；
            $gateway_name = Gateway::getName($this->gateway_id);

            $content = '网关'.$this->gateway_id;
            if ($gateway_name != '') {
                $content .= '-'.$gateway_name.'';
            }

            if ($this->if_port) {
                $content .= ', 网口'.$this->if_port;
            }

            $content .= '报警!';

            // 如果content不是整数，默认为1；
            if (isset($msg_type[$this->type])) {
                $content .= ' '.$msg_type[$this->type];
            }
            else if (isset($msg_type[$this->type.'-'.$this->content])) {
                $content .= ' '.$msg_type[$this->type.'-'.$this->content];
            }

            // 加入工单中，如果加入工单失败，如何处理？
            $res = Yii::$app->systemWorkorder->gateway([
                'target_id' => $this->gateway_id,
                'content' => $content
            ]);

            // 加入工单成功，标记为已读
            if ($res) {
                $this->is_read = 1;
                $this->save();
            }
        }
    }


}
