<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use IvoPetkov\HTML5DOMDocument;

$app = App::get();

$app->bearCMS->themes
    ->register('bearcms/themeone', function (\BearCMS\Themes\Theme $theme) use ($app) {
        $context = $app->contexts->get(__DIR__);

        $app->localization
            ->addDictionary('en', function () use ($context) {
                return include $context->dir . '/themes/themeone/locales/en.php';
            })
            ->addDictionary('bg', function () use ($context) {
                return include $context->dir . '/themes/themeone/locales/bg.php';
            });

        $context->assets
            ->addDir('themes/themeone/assets');

        $theme->version = '1.9';

        $theme->get = function (\BearCMS\Themes\Theme\Customizations $customizations, array $cntx) use ($app, $context) {
            $language = isset($cntx['language']) ? $cntx['language'] : null;
            $languages = isset($cntx['languages']) ? $cntx['languages'] : [];
            $templateFilename = $context->dir . '/themes/themeone/components/defaultTemplate.php';
            return (static function ($__filename, $customizations, $language, $languages) use ($app) { // used inside
                ob_start();
                include $__filename;
                return $app->components->process(ob_get_clean());
            })($templateFilename, $customizations, $language, $languages);
        };

        $theme->apply = function (\BearFramework\App\Response $response, \BearCMS\Themes\Theme\Customizations $customizations) use ($context) {
            $domDocument = new HTML5DOMDocument();
            $domDocument->loadHTML($response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            $elements = $domDocument->querySelectorAll('bearcms-elements');
            if ($elements->length > 0) {
                foreach ($elements as $element) {
                    $element->setAttribute('spacing', '1.5rem');
                }
                $response->content = $domDocument->saveHTML();
            }
        };

        $theme->manifest = function () use ($context, $theme) {
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

        $theme->options = function () use ($context, $theme) {
            $options = $theme->makeOptions(); // used inside
            require $context->dir . '/themes/themeone/options.php';
            return $options;
        };
    });
