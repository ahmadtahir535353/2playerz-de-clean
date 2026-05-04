<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Forgot Password?',

    'actions' => [

        'login' => [
            'label' => 'Back to login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email Address',
        ],

        'actions' => [

            'request' => [
                'label' => 'Send Email',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many requests',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
