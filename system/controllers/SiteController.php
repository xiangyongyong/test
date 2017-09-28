<?php
namespace system\controllers;

use system\modules\gateway\models\Gateway;
use system\modules\group\models\Group;
use system\modules\role\models\AuthAssign;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $layout = false;

    public function actionTest()
    {
        $this->layout = 'main';
        $data = Gateway::find()->select(['state', 'count(*) as count'])->groupBy(['state'])->asArray()->all();
        print_r($data);//exit;
        return $this->render('test');
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

}