<?php

return [
    'label' => '分页导航',

    'overview' => '{1} 显示 1 个结果|[2,*] 显示第 :first 到 :last 个结果，共 :total 个结果',

    'fields' => [
        'records_per_page' => [
            'label' => '每页记录数',
            'options' => [
                'all' => '全部',
            ],
        ],
    ],

    'actions' => [
        'first' => [
            'label' => '首页',
        ],

        'go_to_page' => [
            'label' => '转到第 :page 页',
        ],

        'last' => [
            'label' => '末页',
        ],

        'next' => [
            'label' => '下一页',
        ],

        'previous' => [
            'label' => '上一页',
        ],
    ],
];
