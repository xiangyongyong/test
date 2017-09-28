<?php
/**
 * 通知组件
 * User: ligang
 * Date: 2017/4/2
 * Time: 下午3:08
 */

namespace system\modules\notify\components;


use system\modules\notify\models\UserNotify;
use yii\base\Component;

class Notify extends Component
{


    //往Notify表中插入一条公告记录
    public function createAnnounce($content, $sender)
    {

    }

    //往Notify表中插入一条提醒记录
    public function createRemind($target, $targetType, $action, $sender, $content)
    {

    }

    /**
     * 创建一条新消息
     * 操作步骤：1,往Notify表中插入一条信息记录; 2,往UserNotify表中插入一条记录，并关联新建的Notify;
     * @param $content
     * @param $sender_id
     * @param $receiver_id
     * @param $target_type
     * @param $target
     * @return bool
     */
    public function createMessage($content, $sender_id, $receiver_id, $target_type = '', $target = '', $type = 'message')
    {
        // 向notify表中插入一条信息
        $notifyModel = new \system\modules\notify\models\Notify();
        $notifyModel->content = $content;
        $notifyModel->type = $type == 'urge'?\system\modules\notify\models\Notify::TYPE_URGE:\system\modules\notify\models\Notify::TYPE_MESSAGE;
        $notifyModel->sender_id = $sender_id;
        $notifyModel->action = '';
        $notifyModel->target = $target;
        $notifyModel->target_type = $target_type;

        // 向UserNotify表中插入一条记录
        if ($notifyModel->save()) {
            $userNotifyModel = new UserNotify();
            $userNotifyModel->notify_id = $notifyModel->notify_id;
            $userNotifyModel->user_id = $receiver_id;
            if ($userNotifyModel->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 从UserNotify中获取最近的一条公告信息的创建时间: lastTime
     * 用lastTime作为过滤条件，查询Notify的公告信息
     * 新建UserNotify并关联查询出来的公告信息
     * @param $user
     */
    public function pullAnnounce($user)
    {

    }

    /**
     * 查询用户的订阅表，得到用户的一系列订阅记录
     * 通过每一条的订阅记录的target、targetType、action、createdAt去查询Notify表，获取订阅的Notify记录。（注意订阅时间必须早于提醒创建时间）
     * 查询用户的配置文件SubscriptionConfig，如果没有则使用默认的配置DefaultSubscriptionConfig
     * 使用订阅配置，过滤查询出来的Notify
     * 使用过滤好的Notify作为关联新建UserNotify
     * @param $user
     */
    public function pullRemind($user)
    {

    }

    /**
     * 通过reason，查询NotifyConfig，获取对应的动作组:actions
     * 遍历动作组，每一个动作新建一则Subscription记录
     * @param $user
     * @param $target
     * @param $targetType
     * @param $reason
     */
    public function subscribe($user, $target, $targetType, $reason)
    {

    }

    /**
     * 删除user、target、targetType对应的一则或多则记录
     * @param $user
     * @param $target
     * @param $targetType
     */
    public function cancelSubscription($user, $target ,$targetType)
    {

    }

    /**
     * 查询SubscriptionConfig表，获取用户的订阅配置
     * @param $userID
     */
    public function getSubscriptionConfig($userID)
    {

    }

    /**
     * 更新用户的SubscriptionConfig记录
     * @param $userID
     */
    public function updateSubscriptionConfig($userID)
    {

    }

    /**
     * 获取用户的消息列表
     * @param $userID
     */
    public function getUserNotify($userID)
    {

    }

    /**
     * 把某条消息设置为已读
     * @param $user_id
     * @param $notifyIDs
     * @return int
     */
    public function read($user_id, $notifyIDs)
    {
        return UserNotify::read($user_id, $notifyIDs);
    }

    /**
     * 全部标记为已读
     * @param $user_id
     * @return int
     */
    public function readAll($user_id)
    {
        return UserNotify::readAll($user_id);
    }


}