<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$app->bearCMS->themes
        ->add('bearcms/themeone', function() use ($app) {
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

            $version = '1.2';
            $version .= '.' . (int) $app->bearCMS->isWhitelabel();

            return [
                'version' => $version,
                'initialize' => function() use ($app) {
                    $app->hooks->add('componentCreated', function($component) {
                                if ($component->src === 'bearcms-elements') {
                                    $component->spacing = '1.5rem';
                                }
                            });
                },
                'apply' => function($response, $options) use ($app, $context) {
                    if ($response instanceof App\Response\HTML) {
                        $templateFilename = $context->dir . '/themes/themeone/components/defaultTemplate.php';
                    } elseif ($response instanceof App\Response\NotFound) {
                        $templateFilename = $context->dir . '/themes/themeone/components/unavailableTemplate.php';
                    } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                        $templateFilename = $context->dir . '/themes/themeone/components/unavailableTemplate.php';
                    } else {
                        return;
                    }

                    $isWhitelabel = $app->bearCMS->isWhitelabel();
                    $templateContent = (function($filename, $isWhitelabel, $options) { // used inside
                                        ob_start();
                                        include $filename;
                                        return ob_get_clean();
                                    })($templateFilename, $isWhitelabel, $options);

                    $template = new \BearFramework\HTMLTemplate($templateContent);
                    $template->insert($options->toHTML());
                    $template->insert($response->content, 'body');
                    $response->content = $app->components->process($template->get());
                },
                'manifest' => function() use ($context) {
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
                },
                'options' => function() use ($context) {
                    return include $context->dir . '/themes/themeone/options.php';
                }
            ];
        });
