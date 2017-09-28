<?php

namespace system\modules\gateway\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tab_factory}}".
 *
 * @property integer $factory_id
 * @property string $factory_name
 * @property string $name
 * @property string $telephone
 * @property integer $add_time
 * @property string $create_by
 */
class Factory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_factory}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['factory_name'], 'required'],
            [['add_time'], 'integer'],
            [['factory_name', 'telephone', 'create_by'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'factory_id' => '厂商ID',
            'factory_name' => '厂商名称',
            'name' => '联系人',
            'telephone' => '电话',
            'add_time' => '添加时间',
            'create_by' => '创建人',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->add_time = time();
                $this->create_by = Yii::$app->user->identity->username;
            }

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
        if ($insert) {
            $content = '新增了厂商：'.$this->factory_name.'; 联系人:'.$this->name.'; 电话:'.$this->telephone;
        } else {
            $content = '编辑了厂商：'.$this->factory_name.'; ';
            foreach ($changedAttributes as $key => $oldValue) {
                if($this->$key == $oldValue) {
                    continue;
                }
                $content .= $this->attributeLabels()[$key] . ':' . $oldValue . '=>' . $this->$key;
            }
        }
        // 写日志
        Yii::$app->systemLog->write([
            'type' => 'factory', // 类型
            'target_id' => $this->factory_id, // 目标
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
            'type' => 'factory', // 类型
            'target_id' => $this->factory_id, // 目标
            'content' => '删除了厂商：'.$this->factory_name.'; ', // 内容
        ]);
    }

    /**
     * 获取厂商名称
     * @param $id
     * @return string
     */
    public static function getName($id)
    {
        $model = self::findOne($id);
        if (!$model)  {
            return '--';
        }

        return $model->factory_name;
    }

    /**
     * 获取列表，格式：['id' => 'name', 'id' => 'name']
     * @return array
     */
    public static function getListMap()
    {
        $data = self::find()->select(['factory_id', 'factory_name'])->asArray()->all();
        return ArrayHelper::map($data, 'factory_id', 'factory_name');
    }


}
