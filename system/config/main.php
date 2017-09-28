<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php'),
    require(__DIR__ . '/nav-bar.php') // 加载侧边栏
);

return [
    'id' => 'app-system',
    'name' => '安全运维系统',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'sourceLanguage' => 'en-US',
    'language' => 'zh-CN',
    'defaultRoute' => 'main/default/index',
    'controllerNamespace' => 'system\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        // 主模块
        'main' => [
            'class' => 'system\modules\main\Module',
        ],
        // 网关模块
        'gateway' => [
            'class' => 'system\modules\gateway\Module',
        ],
        // 角色，权限
        'role' => [
            'class' => 'system\modules\role\Module',
        ],
        // 统计模块
        'stats' => [
            'class' => 'system\modules\stats\Module',
        ],
        // 用户模块
        'user' => [
            'class' => 'system\modules\user\Module',
        ],
        // 组模块，负责维护所有的组
        'group' => [
            'class' => 'system\modules\group\Module',
        ],
        // 工单模块
        'workorder' => [
            'class' => 'system\modules\workorder\Module',
        ],
        // 新增操作模块
        'operation' => [
            'class' => 'system\modules\operation\Module',
        ],
        // 通知模块
        'notify' => [
            'class' => 'system\modules\notify\Module',
        ],
        // 可视化模块
        'visual' => [
            'class' => 'system\modules\visual\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-system',
        ],
        // 用户组件
        'user' => [
            'class' => 'system\modules\user\components\User',
            'loginUrl' => ['/user/default/login'],
            'identityClass' => 'system\modules\user\components\UserIdentity',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-system', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the system
            'name' => 'advanced-system',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => '/site/error',
        ],
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//            'rules' => [
//            ],
//        ],
        //添加权限认证类
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'auth_item',
            'assignmentTable' => 'auth_assignment',
            'itemChildTable' => 'auth_item_child',
        ],
        'assetManager'=>[
            'assetMap' => [
                // 替换掉默认的jquery库
                'jquery.js' => '@web/theme/default/lib/jquery/jquery-1.9.1.min.js',
            ],
            /*'bundles'=>[
                //'yii\web\JqueryAsset' => false
                'yii\web\JqueryAsset' => [
                    'js' => [
                        '/theme/default/lib/jquery/jquery-1.9.1.min.js'
                    ]
                ]
            ],*/
        ],
    ],
    'params' => $params,
];
