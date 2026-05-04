<?php

return [
    'title' => '验证您的电子邮件地址',

    'heading' => '验证您的电子邮件地址',

    'actions' => [
        'resend_notification' => [
            'label' => '重新发送',
        ],
    ],

    'messages' => [
        'notification_not_received' => '没有收到我们发送的邮件？',
        'notification_sent' => '我们已经发送了一封包含验证您电子邮件地址的说明的邮件到 :email。',
    ],

    'notifications' => [
        'notification_resent' => [
            'title' => '我们已经重新发送了邮件。',
        ],

        'notification_resend_throttled' => [
            'title' => '尝试发送次数过多',
            'body' => '请在 :seconds 秒后再试。',
        ],
    ],
];
