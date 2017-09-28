<?php

return [
    'navBar' => [
        // 控制台
        'dash' => [
            'title' => '控制台', // url标题
            'icon' => 'fa-tachometer', // url图标
            'href' => 'statistics/dashboard/index', // url
            'spread' => false, // 是否展开，如果有二级菜单，可以展开
        ],
        'gateway' => [
            'title' => '网关/设备',
            'icon' => 'fa-laptop',
            'href' => 'gateway',
            'spread' => false,
            'children' => [
                // 网关管理
                [
                    'title' => '网关管理',
                    'icon' => 'fa-laptop',
                    'href' => 'gateway/gateway/index',
                    'spread' => false,
                    'childItem' => [
                        'gateway/gateway/view' => '网关详情',
                        'gateway/gateway/edit' => '修改网关',
                    ],
                ],
                // 设备管理
                [
                    'title' => '设备管理',
                    'icon' => 'fa-server',
                    'href' => 'gateway/device/index',
                    'spread' => false,
                    'childItem' => [
                        'gateway/device/edit' => '修改设备',
                    ],
                ],
                // 环境数据
                [
                    'title' => '环境信息',
                    'icon' => 'fa-envira',
                    'href' => 'gateway/env/list',
                    'spread' => false,
                ],
                // 端口信息
                [
                    'title' => '端口信息',
                    'icon' => 'fa-dot-circle-o',
                    'href' => 'gateway/port/list',
                    'spread' => false,
                ],
                // 设备厂商
                [
                    'title' => '设备厂商',
                    'icon' => 'fa-truck',
                    'href' => 'gateway/factory/index',
                    'spread' => false,
                    'childItem' => [
                        'gateway/factory/add' => '添加厂商',
                        'gateway/factory/edit' => '添加厂商',
                        'gateway/factory/delete' => '添加厂商',
                    ],
                ],
            ],
        ],
        // 用户管理
        'users' => [
            'title' => '用户管理',
            'icon' => 'fa-users',
            'href' => 'users',
            'spread' => false,
            'children' => [
                // 管理员
                [
                    'title' => '用户管理',
                    'icon' => 'fa-user',
                    'href' => 'user/manage/index',
                    'spread' => false,
                    'childItem' => [
                        'user/manage/add' => '添加用户',
                        'user/manage/edit' => '编辑用户',
                        'user/manage/delete' => '删除用户',
                        'user/manage/bindgroup' => '绑定网关组',
                    ],
                ],
                // 角色
                [
                    'title' => '角色管理',
                    'icon' => 'fa-address-card-o',
                    'href' => 'role/default/index',
                    'spread' => false,
                    'childItem' => [
                        'role/default/add' => '添加角色',
                        'role/default/edit' => '编辑角色',
                        'role/default/delete' => '删除角色',
                    ],
                ],
            ]
        ],
        // 工单管理
        'workorder' => [
            'title' => '工单管理',
            'icon' => 'fa-calendar-check-o',
            'spread' => false,
            'href' => 'workorder',
            'children' => [
                // 全部工单
                [
                    'title' => '全部工单',
                    'icon' => 'fa-wrench',
                    'href' => 'workorder/default/index',
                    'spread' => false,
                    'childItem' => [
                        'workorder/default/my' => '查看工单',
                        'workorder/default/edit' => '编辑工单'
                    ],
                ],
                // 我的工单
                [
                    'title' => '我的工单',
                    'icon' => 'fa-tasks',
                    'href' => 'workorder/default/my',
                    'spread' => false,
                    'childItem' => [
                        'workorder/default/my' => '查看工单',
                        'workorder/default/edit' => '编辑工单'
                    ],
                ],
            ],
        ],
        // 统计分析
        'stats' => [
            'title' => '统计分析',
            'icon' => 'fa-line-chart',
            'spread' => false,
            'href' => 'stats',
            'children' => [
                // 网关统计
                [
                    'title' => '网关统计',
                    'icon' => 'fa-pie-chart',
                    'href' => 'stats/gateway/index',
                    'spread' => false,
                ],
                // 环境统计
                [
                    'title' => '环境信息统计',
                    'icon' => 'fa-area-chart',
                    'href' => 'stats/gateway/env',
                    'spread' => false,
                ],
                // 端口统计
                [
                    'title' => '端口信息统计',
                    'icon' => 'fa-bar-chart',
                    'href' => 'stats/gateway/port',
                    'spread' => false,
                ],
                // 日志管理
                [
                    'title' => '日志管理',
                    'icon' => 'fa-file-text-o',
                    'href' => 'main/log/index',
                    'spread' => false,
                ],
            ],
        ],
        // 系统设置
        'setting' => [
            'title' => '系统设置',
            'icon' => 'fa-cogs',
            'href' => 'setting',
            'spread' => false,
            'children' => [
                // 管理员
                [
                    'title' => '用户管理',
                    'icon' => 'fa-user',
                    'href' => 'user/manage/index',
                    'spread' => false,
                    'childItem' => [
                        'user/manage/add' => '添加用户',
                        'user/manage/edit' => '编辑用户',
                        'user/manage/delete' => '删除用户',
                        'user/manage/bindgroup' => '绑定网关组',
                    ],
                ],
                // 角色
                [
                    'title' => '角色管理',
                    'icon' => 'fa-address-card-o',
                    'href' => 'role/default/index',
                    'spread' => false,
                    'childItem' => [
                        'role/default/add' => '添加角色',
                        'role/default/edit' => '编辑角色',
                        'role/default/delete' => '删除角色',
                    ],
                ],
                // 系统设置
                [
                    'title' => '系统设置',
                    'icon' => 'fa-cog',
                    'href' => '/main/config/setting',
                    'spread' => false,
                ],
                // 组信息
                [
                    'title' => '网关组管理',
                    'icon' => 'fa-server',
                    'href' => 'group/default/index',
                    'spread' => false,
                    'childItem' => [
                        'role/default/update' => '管理组',
                    ],
                ],
                // 系统设置
                [
                    'title' => '配置管理',
                    'icon' => 'fa-cog',
                    'href' => 'main/config/index',
                    'spread' => false,
                    'childItem' => [
                        'main/config/add' => '添加配置',
                        'main/config/edit' => '编辑配置',
                        'main/config/delete' => '删除配置',
                    ],
                ],
            ],

        ],
    ],

];