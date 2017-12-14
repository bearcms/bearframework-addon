<?php

use BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

return [
    [
        'name' => 'Стил 1',
        'media' => [
            [
                'filename' => $context->dir . '/themes/universal/assets/1.jpg',
                'width' => 1024,
                'height' => 768,
            ]
        ],
        'values' => require $context->dir . '/themes/universal/styles/1.php'
    ],
    [
        'name' => 'Стил 2',
        'media' => [
            [
                'filename' => $context->dir . '/themes/universal/assets/2.jpg',
                'width' => 1024,
                'height' => 768,
            ]
        ],
        'values' => require $context->dir . '/themes/universal/styles/2.php'
    ],
    [
        'name' => 'Стил 3',
        'media' => [
            [
                'filename' => $context->dir . '/themes/universal/assets/3.jpg',
                'width' => 1024,
                'height' => 768,
            ]
        ],
        'values' => require $context->dir . '/themes/universal/styles/3.php'
    ],
    [
        'name' => 'Стил 4',
        'media' => [
            [
                'filename' => $context->dir . '/themes/universal/assets/4.jpg',
                'width' => 1024,
                'height' => 768,
            ]
        ],
        'values' => require $context->dir . '/themes/universal/styles/4.php'
    ],
    [
        'name' => 'Стил 5',
        'media' => [
            [
                'filename' => $context->dir . '/themes/universal/assets/5.jpg',
                'width' => 1024,
                'height' => 768,
            ]
        ],
        'values' => require $context->dir . '/themes/universal/styles/5.php'
    ]
];
