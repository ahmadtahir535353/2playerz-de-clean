<?php

return [
    'title' => '注册',

    'heading' => '注册',

    'actions' => [
        'login' => [
            'before' => '或',
            'label' => '登录您的账户',
        ],
    ],

    'form' => [
        'email' => [
            'label' => '电子邮件地址',
        ],

        'name' => [
            'label' => '姓名',
        ],

        'password' => [
            'label' => '密码',
            'validation_attribute' => '密码',
        ],

        'password_confirmation' => [
            'label' => '确认密码',
        ],

        'actions' => [
            'register' => [
                'label' => '注册',
            ],
        ],
    ],

    'notifications' => [
        'throttled' => [
            'title' => '注册尝试次数过多',
            'body' => '请在 :seconds 秒后再试。',
        ],
    ],
];
