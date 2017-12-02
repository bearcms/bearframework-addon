<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Themes\Theme1;

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('BearCMS\Themes\Theme1', 'themes/theme1/classes/Theme1.php');

$app->bearCMS->themes
        ->add('bearcms/theme1', [
            'version' => '1',
            'initialize' => function() use ($app, $context) {
                Theme1::initialize($app, $context);
            },
            'apply' => function($response, $options) use ($app, $context) {
                Theme1::apply($app, $context, $response, $options);
            },
            'manifest' => function() use ($app, $context) {
                Theme1::initializeLocalization($app, $context);
                return [
                    'name' => __('bearcms.themes.theme1.name'),
                    'description' => 'This is the default starter theme for each Bear CMS powered website. Simple yet highly customizable it enables you to create websites that look great on desktops, tables and smartphones. You can change the colors and visibility of the different content blocks.',
                    'author' => [
                        'name' => 'Bear CMS Team',
                        'url' => 'https://bearcms.com/addons/',
                        'email' => 'addons@bearcms.com',
                    ],
                    'media' => [
                        [
                            'filename' => $context->dir . '/themes/theme1/assets/t1.jpg',
                            'width' => 1024,
                            'height' => 768,
                        ]
                    ]
                ];
            },
            'options' => function() use ($context) {
                return include $context->dir . '/themes/theme1/options.php';
            }
        ]);
