<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$app->bearCMS->themes
        ->announce('bearcms/themeone', function(\BearCMS\Themes\Theme $theme) use ($app) {
            $context = $app->context->get(__FILE__);

            $app->localization
            ->addDictionary('en', function() use ($context) {
                return include $context->dir . '/themes/themeone/locales/en.php';
            })
            ->addDictionary('bg', function() use ($context) {
                return include $context->dir . '/themes/themeone/locales/bg.php';
            });

            $context->assets
            ->addDir('themes/themeone/assets');

            $theme->version = '1.3';

            $theme->initialize = function() use ($app) {
                $app->hooks->add('componentCreated', function($component) {
                    if ($component->src === 'bearcms-elements') {
                        $component->spacing = '1.5rem';
                    }
                });
            };

            $theme->get = function(\BearCMS\Themes\Options $options) use ($context) {
                $templateFilename = $context->dir . '/themes/themeone/components/defaultTemplate.php';
                return (static function($__filename, $options) { // used inside
                            ob_start();
                            include $__filename;
                            return ob_get_clean();
                        })($templateFilename, $options);
            };

            $theme->manifest = function() use ($context) {
                return [
                    'name' => __('bearcms.themes.themeone.name'),
                    'description' => __('bearcms.themes.themeone.description'),
                    'author' => [
                        'name' => 'Bear CMS Team',
                        'url' => 'https://bearcms.com/addons/',
                        'email' => 'addons@bearcms.com',
                    ],
                    'media' => [
                        [
                            'filename' => $context->dir . '/themes/themeone/assets/one.png',
                            'width' => 1442,
                            'height' => 1062,
                        ]
                    ]
                ];
            };

            $theme->optionsSchema = function() use ($context) {
                return include $context->dir . '/themes/themeone/options.php';
            };
        });
