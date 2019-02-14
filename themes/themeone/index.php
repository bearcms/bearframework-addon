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
        ->register('bearcms/themeone', function(\BearCMS\Themes\Theme $theme) use ($app) {
            $context = $app->contexts->get(__FILE__);

            $app->localization
            ->addDictionary('en', function() use ($context) {
                return include $context->dir . '/themes/themeone/locales/en.php';
            })
            ->addDictionary('bg', function() use ($context) {
                return include $context->dir . '/themes/themeone/locales/bg.php';
            });

            $context->assets
            ->addDir('themes/themeone/assets');

            $theme->version = '1.6';

            $theme->initialize = function() use ($app) {
                $app->components
                ->addEventListener('makeComponent', function($details) {
                    $component = $details->component;
                    if ($component->src === 'bearcms-elements') {
                        $component->spacing = '1.5rem';
                    }
                });
            };

            $theme->get = function(\BearCMS\Themes\Theme\Customizations $customizations) use ($context) {
                $templateFilename = $context->dir . '/themes/themeone/components/defaultTemplate.php';
                return (static function($__filename, $customizations) { // used inside
                            ob_start();
                            include $__filename;
                            return ob_get_clean();
                        })($templateFilename, $customizations);
            };

            $theme->manifest = function() use ($context, $theme) {
                $manifest = $theme->makeManifest();
                $manifest->name = __('bearcms.themes.themeone.name');
                $manifest->description = __('bearcms.themes.themeone.description');
                $manifest->author = [
                    'name' => 'Bear CMS Team',
                    'url' => 'https://bearcms.com/addons/',
                    'email' => 'addons@bearcms.com',
                ];
                $manifest->media = [
                    [
                        'filename' => $context->dir . '/themes/themeone/assets/one.png',
                        'width' => 1442,
                        'height' => 1062,
                    ]
                ];
                return $manifest;
            };

            $theme->options = function() use ($context, $theme) {
                $options = $theme->makeOptions(); // used inside
                require $context->dir . '/themes/themeone/options.php';
                return $options;
            };
        });
