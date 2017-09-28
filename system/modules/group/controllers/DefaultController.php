<?php

namespace system\modules\group\controllers;

use Yii;
use system\modules\group\models\Group;
use yii\helpers\Json;

/**
 * Default controller for the `group` module
 */
class DefaultController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * AJAX 请求一下数据.
     * @return string
     */
    public function actionAjax()
    {
        return Group::getNodesByIdentity(true);
    }

    /**
     * 更新组织架构，包括新增，编辑，拖拽和删除
     * @throws \Exception
     */
    public function actionUpdate()
    {
        $data = Yii::$app->request->getRawBody();
        $data = json_decode($data, true);
        $model = null;
        //print_r($data);exit;
        if (!isset($data['type'])) {
            echo '缺少参数';exit;
        }
        if (!in_array($data['type'], ['add', 'edit', 'delete', 'drag'])) {
            echo '参数错误';exit;
        }

        $res = false;
        $content = ''; // 日志

        //增加节点
        if ($data['type'] == 'add') {
            if (!isset($data['name'], $data['pid'])){
                echo '缺少参数';exit;
            }
            $model = new Group();
            $model->name = $data['name'];
            $model->pid = $data['pid'];
            $res = $model->save();
            if ($res) {
                $pName = Group::getNameById($model->pid);
                $content = '新增了组：'.$model->name . '(' . $model->id . '); 父节点：' . $pName . '(' . $model->pid . ');';
            }
        }
        //编辑节点，智能编辑名称
        else if ($data['type'] == 'edit') {
            // 根节点时pid=null
            if (!isset($data['id'], $data['name'])) {
                echo '缺少参数';exit;
            }
            $model = Group::findOne($data['id']);
            // 记录原始数据
            $oldName = $model->name;
            if (!$model) {
                echo '参数错误';exit;
            }
            if ($model->name == $data['name']) {
                echo '名称一致，无需编辑';exit;
            }
            $model->name = $data['name'];
            $res = $model->save();
            if ($res && $model->name != $oldName) {
                $content = '编辑了组：'. $oldName . '(' . $model->id . '); 新名称: ' . $model->name . ';';
            }
        }
        //删除节点
        else if ($data['type'] == 'delete') {
            if (!isset($data['id'])) {
                echo '缺少参数';exit;
            }
            $model = Group::findOne($data['id']);
            if (!$model) {
                echo '参数错误';exit;
            }
            $res = $model->delete();
            if (!$res) {
                echo '删除失败！请检查该组下是否有其他组';exit;
            }
            $pName = Group::getNameById($model->pid);
            $content = '删除了组：'.$model->name . '(' . $model->id . '); 父节点：' . $pName . '('. $model->pid . ');';
        }
        //拖拽节点
        else if ($data['type'] == 'drag') {
            if (!isset($data['id'], $data['target_id'])) {
                echo '缺少参数';exit;
            }
            $model = Group::findOne($data['id']);
            if (!$model) {
                echo '参数错误';exit;
            }
            if ($model->pid == $data['target_id']) {
                echo '无需拖拽';exit;
            }

            $res = Group::dragGroup($data);
            if ($res) {
                $oldPName = Group::getNameById($model->pid);
                $newPName = Group::getNameById($data['target_id']);
                $content = '拖拽了组：'. $model->name . '(' . $model->id . '); 父组：' . $oldPName . '(' . $model->pid . ') =>' . $newPName . '(' . $data['target_id'] . ');';
            }
        }

        if ($res) {
            Yii::$app->systemLog->write([
                'type' => 'group',
                'target_id' => $model ? $model->id : $data['id'],
                'content' => $content
            ]);
            echo '操作成功';
        } else {
            echo '操作失败'.Json::encode($model->errors);
        }
        exit;
    }

}
