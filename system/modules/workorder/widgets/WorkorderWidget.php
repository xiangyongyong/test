<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/27
 * Time: 上午11:54
 */

namespace system\modules\workorder\widgets;


use yii\base\Widget;

class WorkorderWidget extends Widget
{

    public $targetName = '';
    public $targetId = 0;

    /**
     * @inheritDoc
     */
    public function run()
    {
        parent::run();

        return $this->render('workorder', [
            'data' => [
                'targetName' => $this->targetName,
                'targetId' => $this->targetId,
            ],
        ]);
    }

}