<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../common/config/bootstrap.php');
require(__DIR__ . '/../system/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../common/config/main.php'),
    require(__DIR__ . '/../common/config/main-local.php'),
    require(__DIR__ . '/../system/config/main.php'),
    require(__DIR__ . '/../system/config/main-local.php')
);

$app = (new yii\web\Application($config));

// 定义web根目录的url
define('WEB', ($web = dirname(Yii::$app->request->getScriptUrl())) != '/' ? $web : '');
                                                                    //三目结果若为$web则可用路径进行访问
define('VERSION', '1.0.0');
//echo Yii::$app->request->getScriptUrl();
$app->run();