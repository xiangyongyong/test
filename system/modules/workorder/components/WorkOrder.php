<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/25
 * Time: 下午2:43
 */

namespace system\modules\workorder\components;


use system\modules\gateway\models\Gateway;
use yii\base\Component;
use yii\web\Application;

class WorkOrder extends Component
{
    // 保修网关
    public function gateway($data)
    {
        // 网关id和内容必须存在
        if (!isset($data['target_id'], $data['content'])) {
            return false;
        }

        // 责任人;
        $worker_id = 0;
        // 如果指定了责任人，那么直接使用
        if (isset($data['worker_id'])) {
            $worker_id = $data['worker_id'];
        }
        // 没有指定责任人，那么寻找负责人
        else {
            if ($user = Gateway::getUserByGateway($data['target_id'])) {
                $worker_id = $user;
            }
        }

        // 提交人用户id
        $user_id = 0;
        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        } else if (\Yii::$app instanceof Application) {
            if (!\Yii::$app->user->isGuest) {
                $user_id = \Yii::$app->user->identity->getId();
            }
        }

        $model = new \system\modules\workorder\models\WorkOrder();
        $model->type = 'gateway'; // 网关类型
        $model->target_id = $data['target_id'];
        $model->user_id = $user_id; // 提交人id
        $model->worker_id = $worker_id; // 责任人
        //$model->title = $data['title'];
        $model->content = isset($data['content']) ? $data['content'] : '';
        return $model->save();
    }
    
    //手动添加新的工单
    public function add($data) 
    {
        // 责任人;
        $worker_id = 0;
        // 如果指定了责任人，那么直接使用
        if (isset($data['worker_id'])) {
            $worker_id = $data['worker_id'];
        }
        // 没有指定责任人，那么寻找负责人
        else {
            if ($user = Gateway::getUserByGateway($data['target_id'])) {
                $worker_id = $user;
            }
        }

        // 提交人用户id
        $user_id = 0;
        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        } else if (\Yii::$app instanceof Application) {
            if (!\Yii::$app->user->isGuest) {
                $user_id = \Yii::$app->user->identity->getId();
            }
        }

        $model = new \system\modules\workorder\models\WorkOrder();
        $model->type = 'gateway'; // 网关类型
        $model->target_id = $data['target_id'];
        $model->user_id = $user_id; // 提交人id
        $model->worker_id = $worker_id; // 责任人
        $model->problem = $data['content_type'];
        //$model->title = $data['title'];
        $model->content = isset($data['content']) ? $data['content'] : '';
        return $model->save();
    }
}