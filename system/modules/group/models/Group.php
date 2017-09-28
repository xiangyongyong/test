<?php

namespace system\modules\group\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tab_group}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property integer $pid
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '组id',
            'name' => '组织结构名称',
            'path' => '结构路径',
            'pid' => '父ID',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // 根节点
            /*if ($this->id == 1) {
                $this->path = '0-';
                $this->pid = 0;
                return true;
            }*/

            if ($this->pid == 0) {
                $this->path = '0-';
                //$this->pid = 0;
                return true;
            }

            // 非根节点
            if ($this->pid != '') {
                $parentModel = self::findOne($this->pid);
                //父节点不存在
                if (!$parentModel) {
                    return false;
                }
                $this->path = $parentModel->path . $parentModel->id . '-';
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            //判断是否存在子节点
            if (self::find()->where(['pid' => $this->id])->count()) {
                return false;
            }

            return true;
        }

        return false;
    }

    private static $_allData;

    /**
     * 获取所有记录
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllData()
    {
        if(!self::$_allData){
            self::$_allData = static::find()->select(['id', 'name', 'path', 'pid'])->asArray()->all();
        }
        return self::$_allData;
    }

    /**
     * 根据id获取name
     * @param $group_id
     * @return string
     */
    public static function getNameById($group_id)
    {
        $model = self::findOne($group_id);
        if (!$model) {
            return '';
        }
        return $model->name;
    }

    /**
     * 根据身份获取节点json数据
     * @param bool $isJson
     * @return array|string
     */
    public static function getNodesByIdentity($isJson = false)
    {
        $data = self::getAllData();
        if ($isJson) {
            return json_encode($data);
        }
        return $data;
    }

    /**
     * 拖拽组到某个位置
     * @param $item array 配置数组 ['id' => '当前组id', 'target_id' => '拖拽目标组id']
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function dragGroup($item)
    {
        //当前组id
        $model = self::findOne($item['id']);
        if (!$model) {
            return false;
        }
        //目标组id
        $targetModel = self::findOne($item['target_id']);
        if (!$targetModel) {
            return false;
        }
        //如果当前组的pid和目标组id相同，则无需更改
        if ($model->pid == $targetModel->id) {
            return false;
        }

        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //更新只能一个个的更新，先把所有的组拿出来，然后从上往下一级一级的更新
            $searchPath = $model->path . $model->id . '-';

            // 1，先更新当前组的pid
            $model->pid = $targetModel->id;
            $res = $model->save();
            if (!$res) {
                return false;
            }

            // 2，再按照之前的path取出来其下的所有的子节点，进行更新
            $childGroup = Group::find()
                ->select(['id', 'name', 'path', 'pid'])
                ->where(['like', 'path', $searchPath.'%', false])
                ->orderBy(['path' => SORT_ASC])
                ->asArray()
                ->all();

            foreach ($childGroup as $key => $item) {
                $model = self::findOne($item['id']);
                if (!$model) {
                    return false;
                }
                $res = $model->save();
                if (!$res) {
                    return false;
                }
            }

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }
    }

    /**
     * 获取name的path
     * @param $id int 网关id
     * @param string $sep 分隔符
     * @return string
     */
    public static function getNamePath($id, $sep = '/')
    {
        $model = self::findOne($id);
        if (!$model) {
            return '--';
        }

        $data = self::namePath($model);
        return implode($sep, $data);
    }

    /**
     * 根据model查找对应对name path
     * @param $model
     * @return array
     */
    public static function namePath($model)
    {
        // 按照path查找所有父级组
        $path = explode('-', trim($model->path, '-'));
        $data = self::find()->select(['id', 'name'])->where(['id' => $path])->asArray()->all();
        $data = ArrayHelper::map($data, 'id', 'name');

        // 按照path来对数组进行排序，否则顺序可能会乱
        $newData = [];
        //print_r($path);exit;
        foreach ($path as $id) {
            // 默认不不显示根组
            if (!$id || $id==1) {
                continue;
            }
            $newData[$id] = $data[$id];
        }

        array_push($newData, $model->name);
        return $newData;
    }

    /**
     * 批量获取namepath
     * @param $groups
     * @param string $sep
     * @return string
     */
    public static function getNamePathByGroups($groups, $sep = '/')
    {
        $data = self::find()->where(['id' => $groups])->all();
        //echo '<pre>';print_r($data);
        $newData = [];
        foreach ($data as $model) {
            $one = self::namePath($model);
            $newData[] = implode($sep, $one);
        }
        return implode(', ', $newData);
    }

    /**
     * 根据给定的数据递归创建组，并返回最后一个创建的组
     * @param $data array 组的名称数组，比如['湖北省', '武汉市', '洪山区', '卓刀泉街道'];
     * @return bool|int
     */
    public static function createGroup($data)
    {
        //print_r($data);exit;
        if (empty($data) || !is_array($data)) {
            return false;
        }

        $pid = 0;
        $group = null;
        // 创建层次关系的组
        foreach ($data as $key => $item) {
            $group = self::find()->where(['name' => $item, 'pid' => $pid])->one();
            if (!$group) {
                $group = new self();
                $group->name = $item;
                $group->pid = $pid;
                if (!$group->save()) {
                    return false;
                }
            }
            // 保存成功以后，父id 更改为当前id
            $pid = $group->id;
        }

        // 将最后一个组的id返回
        return $group ? $group->id : false;
    }

    /**
     * 根据id获取此id的所有子节点
     * @param $id int 节点id
     * @param bool $includeSelf 是否包含本节点
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getChildById($id, $includeSelf=true)
    {
        //当前结点
        $node = static::getOneById($id);
        if(!$node){
            return [];
        }
        $childNodes = [];
        if ($node['path'] != '') {
            $childNodes = self::find()
                ->andWhere("path LIKE :con", [":con"=>$node['path'].$id.'-%'])
                ->asArray()
                ->all();
        }
        //是否包含自身节点
        if($includeSelf){
            array_unshift($childNodes, $node);
            return $childNodes;
        }
        return $childNodes;
    }

    /**
     * 根据组id获取
     * @param $id
     * @param bool $includeSelf
     * @return array
     */
    public static function getChildIdsById($id, $includeSelf=true)
    {
        $data = self::getChildById($id, $includeSelf);
        return ArrayHelper::getColumn($data, 'id');
    }

    /**
     * 根据id获取一个结点
     * @param int $id 结点id
     * @param bool $asArray 是否返回数组，否则返回对象
     * @return array|null|self
     */
    public static function getOneById($id, $asArray = true)
    {
        $query = static::find()->where(['id'=>$id]);
        if($asArray){
            $query->asArray();
        }
        return $query->one();
    }

    /**
     * 获取某个id的第一级子id
     * @param int $id 组id
     * @return array
     */
    public static function getFirstChildsById($id = 0)
    {
        $data = self::find()
            ->select(['id', 'name', 'pid'])
            ->where(['pid' => $id])
            ->asArray()
            ->all();

        return $data;
    }
}
