<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // 写日志组件，所有的日志通过此组件写入数据库
        'systemLog' => [
            'class' => 'system\modules\main\components\Log',
        ],
        // 系统配置组件
        'systemConfig' => [
            'class' => 'system\modules\main\components\Config',
        ],
        // 工单组件
        'systemWorkorder' => [
            'class' => 'system\modules\workorder\components\WorkOrder',
        ],
        // 消息组件
        'systemNotify' => [
            'class' => 'system\modules\notify\components\notify',
        ],
        // 高德地图组件
        'systemMap' => [
            'class' => 'system\modules\visual\components\Map',
        ],
    ],
];
