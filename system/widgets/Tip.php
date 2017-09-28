<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/13
 * Time: 下午11:45
 */

namespace system\widgets;


use yii\base\Widget;

class Tip extends Widget
{
    public $value;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
        return $this->render('tip', [

        ]);
    }


}