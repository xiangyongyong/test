<?php

namespace system\modules\workorder\models;

use system\modules\gateway\models\Gateway;
use system\modules\notify\models\Notify;
use system\modules\user\models\User;
use system\modules\notify\models\UserNotify;
use Yii;

/**
 * This is the model class for table "{{%tab_work_order}}".
 *
 * @property integer $order_id
 * @property string $type
 * @property integer $target_id
 * @property string $title
 * @property string $content
 * @property integer $state
 * @property integer $user_id
 * @property integer $worker_id
 * @property integer $promise_time
 * @property integer $urge_num
 * @property integer $order_time
 * @property integer $created_at
 * @property integer $finished_at
 * @property string $finished_remark
 * @property integer $update_at
 * @property integer $problem
 */
class WorkOrder extends \yii\db\ActiveRecord
{

    const STATE_SUSPENDING = 0; // 待处理
    const STATE_HANDLING = 1;   // 处理中
    const STATE_SOLVED = 2;     // 已解决
    const STATE_CLOSE = 3;     // 已关闭


    public static $STATE_NOT_FINISH = [0, 1]; // 工单未完成
    public static $STATE_FINISH = [2, 3]; // 工单已完成

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tab_work_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'state', 'user_id', 'worker_id', 'promise_time', 'urge_num', 'order_time', 'created_at', 'finished_at', 'update_at', 'problem'], 'integer'],
            [['content', 'finished_remark'], 'string'],
            [['type'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '流水id',
            'type' => '工单类型',
            'target_id' => '目标id',
            'title' => '工单标题',
            'content' => '工单详细内容',
            'state' => '状态',
            'user_id' => '报修人id',
            'worker_id' => '责任人id',
            'promise_time' => '承诺解决时间',
            'urge_num' => '催促次数',
            'order_time' => '工单时长',
            'created_at' => '创建时间',
            'finished_at' => '完成时间',
            'finished_remark' => '完成备注',
            'update_at' => '更新时间',
            'problem' => '初步设备故障原因:1.设备无响应，2.端口故障，3.流量异常，4.温度异常',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->created_at = time();
            } else {
                // 更新状态下 如果当前没有责任人，那么谁处理了谁就是责任人
                if (!$this->worker_id) {
                    $this->worker_id = Yii::$app->user->identity->getId();
                }
            }

            $this->update_at = time();

            // 完成时间
            if (in_array($this->state, self::$STATE_FINISH)) {
                $this->finished_at = time();
                $this->order_time = $this->finished_at - $this->created_at;
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

        // 写日志
        $content = '';

        // 当前登录用户
        if (Yii::$app instanceof \yii\console\Application) {
            $loginUser = '系统';
            $loginUserId = 0;
        }else {
            $loginUser = Yii::$app->user->identity->realname; //.'('.Yii::$app->user->identity->username.')';
            $loginUserId = Yii::$app->user->identity->getId();
        }

        // user_id对应的用户
        $addUser = '系统';
        if ($this->user_id != 0) {
            $userModel = User::findOne($this->user_id);
            if ($userModel) {
                $addUser = $userModel->realname; //.'('.$userModel->username.')';
            }
        }

        // 旧的worker_id对应的用户  新的worker_id对应的用户
        $oldWorker = '';
        $oldWorkerId = 0;
        $newWorker = '';
        $newWorkerId = 0;
        if (array_key_exists('worker_id', $changedAttributes)) {
            // 如果存在旧的责任人
            if ($changedAttributes['worker_id'] != 0) {
                $oldWorkerModel = User::findOne($changedAttributes['worker_id']);
                if ($oldWorkerModel) {
                    $oldWorker = $oldWorkerModel->realname; // . '(' . $oldWorkerModel->username . ')';
                    $oldWorkerId = $oldWorkerModel->user_id; // . '(' . $oldWorkerModel->username . ')';
                }
            }

            // 如果有新的worker
            if ($changedAttributes['worker_id'] != $this->worker_id) {
                $newWorkerModel = User::findOne($this->worker_id);
                if ($newWorkerModel) {
                    $newWorker = $newWorkerModel->realname; // . '(' . $newWorkerModel->username . ')';
                    $newWorkerId = $newWorkerModel->user_id;
                }
            }
        }

        // 增加
        if ($insert) {
            // xx创建了工单4，分配给了xx；
            $content = "{$addUser}创建了工单{$this->order_id}; ";
            if ($newWorker) {
                $content .= "，分配给了{$newWorker}; ";
            }
        }
        // 编辑
        else {
            // xx开始处理工单4；xx关闭了工单4；xx完成了工单4；xx将工单4从xx移交给xxx；xx将工单4分配给了xxx;
            if (isset($changedAttributes['worker_id'])) {
                if ($changedAttributes['worker_id'] == 0 && $this->worker_id) {
                    $content .= "{$loginUser}将工单{$this->order_id}分配给了{$newWorker}; ";
                } else if ($changedAttributes['worker_id'] != $this->worker_id) {
                    $content .= "{$loginUser}将工单{$this->order_id}从{$oldWorker}移交给{$newWorker}; ";
                }
            }

            if (isset($changedAttributes['state']) && $changedAttributes['state'] != $this->state) {
                $state_list = [
                    1 => '开始处理',
                    2 => '完成了',
                    3 => '关闭了',
                ];
                if (isset($state_list[$this->state])) {
                    $stateMsg = $state_list[$this->state];
                } else {
                    $stateMsg = '处理了';
                }
                $content .= "{$loginUser}{$stateMsg}工单{$this->order_id}; ";
            }
        }

        if ($content) {
            Yii::$app->systemLog->write([
                'type' => 'workorder',
                'target_id' => $this->order_id,
                'content' => $content,
            ]);
        }


        // @TODO 给责任人和发起人发送短信和email

        // @TODO 待优化，消息机制
        // 如果有新的责任人（比如新发起的，或者更改了责任人的），并且不是责任人自己操作的（自己分配给自己就没有必要发送站内信了）；
        if ($newWorkerId && $oldWorkerId != $newWorkerId && $newWorkerId != $loginUserId) {
            Yii::$app->systemNotify->createMessage('您有新工单，编号：'.$this->order_id, $loginUserId, $newWorkerId, 'workorder', $this->order_id);
        }

        // 更新状态
        if (!$insert) {
            // $content, $sender_id, $receiver_id, $target_type = '', $target = ''
            $notifyContent = '';
            if ($this->state == self::STATE_HANDLING) {
                $notifyContent = '工单'.$this->order_id.'有更新';
            }
            else if ($this->state == self::STATE_SOLVED) {
                $notifyContent = '工单'.$this->order_id.'已处理';
            }
            else if ($this->state == self::STATE_CLOSE) {
                $notifyContent = '工单'.$this->order_id.'已关闭';
            }

            // 如果有消息产生，给发起人发消息； 如果发起人存在，并且状态有更改，并且当前操作人不是发起人
            if ($notifyContent && $this->user_id && $loginUserId != $this->user_id) {
                Yii::$app->systemNotify->createMessage($notifyContent, $loginUserId, $this->user_id, 'workorder', $this->order_id);
            }

            // 给责任人发消息；如果当前操作人不是责任人，但是状态有更改，那么需要发送消息
            if ($notifyContent && $this->worker_id && $loginUserId != $this->worker_id) {
                Yii::$app->systemNotify->createMessage($notifyContent, $loginUserId, $this->worker_id, 'workorder', $this->order_id);
            }
        }

    }

    /**
     * 获取责任人
     * @return \yii\db\ActiveQuery
     */
    public function getWorker()
    {
        return $this->hasOne(User::className(), ['user_id' => 'worker_id']);
    }

    /**
     * 获取用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * 获取网关
     * @return \yii\db\ActiveQuery
     */
    public function getGateway()
    {
        return $this->hasOne(Gateway::className(), ['gateway_id' => 'target_id'])->with('device')->with('env')->with('portInfo');
    }

    /**
     * 获取消息
     * @return \yii\db\ActiveQuery
     */
    public function getNotify()
    {
        return $this->hasOne(Notify::className(), ['target' => 'order_id'])->with('userNotify')->where(['type'=>3]);
    }

    public function getUrge()
    {
        return $this->hasMany(Notify::className(), ['target' => 'order_id'])->with('userNotify')->where(['type'=>4]);
    }

}
