<?php

namespace system\modules\main\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%tab_config}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $title
 * @property integer $group
 * @property string $extra
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $status
 * @property string $value
 * @property integer $sort
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'group', 'create_time', 'update_time', 'status', 'sort'], 'integer'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 50],
            [['extra'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 100],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '配置ID',
            'name' => '标识',
            'title' => '标题',
            'sort' => '排序',
            'type' => '类型',
            'group' => '分组',
            'value' => '值',
            'extra' => '配置项',
            'remark' => '配置说明',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'status' => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->create_time = time();
            }

            $this->update_time = time();

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

        // 写日志
        if ($insert) {
            $data = [
                '标题' => $this->title,
                '组' => $this->group,
                '类型' => $this->type,
                '值' => $this->value,
                '选项' => $this->extra,
                '排序' => $this->sort,
                '说明' => $this->remark,
            ];
            $content = '新增了配置：' . $this->name . '; 详细数据：' . Json::encode($data);
        } else {
            $content = '编辑了配置：' . $this->name.'; ';
            foreach ($changedAttributes as $key => $oldValue) {
                if($this->$key == $oldValue) {
                    continue;
                }
                $content .= $this->attributeLabels()[$key] . ':' . $oldValue . '=>' . $this->$key . '; ';
            }
        }

        Yii::$app->systemLog->write([
            'type' => 'config', // 类型
            'target_id' => $this->id, // 目标
            'content' => $content, // 内容
        ]);
    }

    /**
     * @inheritDoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        // 写日志
        Yii::$app->systemLog->write([
            'type' => 'config', // 类型
            'target_id' => $this->id, // 目标
            'content' => '删除了配置：'.$this->name.';', // 内容
        ]);
    }


}
