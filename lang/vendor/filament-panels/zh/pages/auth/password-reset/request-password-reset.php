<?php

return [
    'title' => '重置您的密码',

    'heading' => '忘记密码？',

    'actions' => [
        'login' => [
            'label' => '返回登录',
        ],
    ],

    'form' => [
        'email' => [
            'label' => '电子邮件地址',
        ],

        'actions' => [
            'request' => [
                'label' => '发送邮件',
            ],
        ],
    ],

    'notifications' => [
        'throttled' => [
            'title' => '请求过多',
            'body' => '请在 :seconds 秒后再试。',
        ],
    ],
];
