<?php

return [
    'title' => '登录',

    'heading' => '登录',

    'actions' => [
        'register' => [
            'before' => '或',
            'label' => '注册账户',
        ],

        'request_password_reset' => [
            'label' => '忘记密码？',
        ],
    ],

    'form' => [
        'email' => [
            'label' => '电子邮件地址',
        ],

        'password' => [
            'label' => '密码',
        ],

        'remember' => [
            'label' => '记住我',
        ],

        'actions' => [
            'authenticate' => [
                'label' => '登录',
            ],
        ],
    ],

    'messages' => [
        'failed' => '这些凭据与我们的记录不匹配。',
    ],

    'notifications' => [
        'throttled' => [
            'title' => '登录尝试次数过多',
            'body' => '请在 :seconds 秒后再试。',
        ],
    ],
];
