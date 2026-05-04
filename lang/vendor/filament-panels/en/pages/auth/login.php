<?php

return [

    'title' => 'Login',

    'heading' => 'Sign in',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'Sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgot Password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email Address',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Sign in',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'These credentials do not match our records.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many login attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
