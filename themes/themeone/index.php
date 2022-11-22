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

        $theme->version = '1.12';

        $theme->get = function (\BearCMS\Themes\Theme\Customizations $customizations, array $cntx) use ($app, $context) {
            $language = isset($cntx['language']) ? $cntx['language'] : null;
            $languages = isset($cntx['languages']) ? $cntx['languages'] : [];
            $templateFilename = $context->dir . '/themes/themeone/components/defaultTemplate.php';
            $template = (static function ($__filename, $customizations, $language, $languages) use ($app) { // used inside
                ob_start();
                include $__filename;
                return $app->components->process(ob_get_clean());
            })($templateFilename, $customizations, $language, $languages);
            if ($app->bearCMS->hasEventListeners('internalBearCMSThemeOneThemeGet')) {
                $eventDetails = new stdClass();
                $eventDetails->template = $template;
                $app->bearCMS->dispatchEvent('internalBearCMSThemeOneThemeGet', $eventDetails);
                $template = $eventDetails->template;
            }
            return $template;
        };

        $theme->apply = function (\BearFramework\App\Response $response, \BearCMS\Themes\Theme\Customizations $customizations) use ($context) {
            $domDocument = new HTML5DOMDocument();
            $domDocument->loadHTML($response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            $elements = $domDocument->querySelectorAll('bearcms-elements');
            if ($elements->length > 0) {
                foreach ($elements as $element) {
                    $element->setAttribute('spacing', '20px');
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
                    'filename' => $context->dir . '/themes/themeone/assets/cover.jpg',
                    'width' => 1500,
                    'height' => 1125,
                ]
            ];
            return $manifest;
        };

        $theme->options = function () use ($context, $theme) {
            $options = $theme->makeOptions(); // used inside
            require $context->dir . '/themes/themeone/options.php';
            return $options;
        };

        $theme->updateValues = function (array $values = null) {
            if (is_array($values)) {
                if (isset($values['textColor']) || isset($values['textSize'])) {
                    $textCSS = [];
                    if (isset($values['textColor'])) {
                        $textCSS['color'] = $values['textColor'];
                        unset($values['textColor']);
                    } else {
                        $textCSS['color'] = '#000000';
                    }
                    if (isset($values['textSize'])) {
                        $textSize = (int)$values['textSize'];
                        if ($textSize === 3) {
                            $textCSS['font-size'] = '18px';
                        } elseif ($textSize === 1) {
                            $textCSS['font-size'] = '14px';
                        } else {
                            $textCSS['font-size'] = '16px';
                        }
                        unset($values['textSize']);
                    } else {
                        $textCSS['font-size'] = '16px';
                    }
                    $textCSS['font-family'] = 'Arial';
                    $textCSS['line-height'] = '180%';
                    $values['textCSS'] = json_encode($textCSS, JSON_THROW_ON_ERROR);
                }
                if (isset($values['accentColor'])) {
                    $defaultFontSize = '16px';
                    if (isset($values['textCSS'])) {
                        $textCSS = json_decode($values['textCSS'], true);
                        if (isset($textCSS['font-size'])) {
                            $defaultFontSize = $textCSS['font-size'];
                        }
                    }
                    $accentTextCSS = [];
                    $accentTextCSS['font-family'] = 'Arial';
                    $accentTextCSS['color'] = $values['accentColor'];
                    $accentTextCSS['font-size'] = $defaultFontSize;
                    $accentTextCSS['line-height'] = '170%';
                    $values['accentTextCSS'] = json_encode($accentTextCSS, JSON_THROW_ON_ERROR);
                    unset($values['accentColor']);
                }
                if (isset($values['headerLogoImage'])) {
                    $values['logoImage'] = $values['headerLogoImage'];
                    $values['logoImageWidth'] = '180px';
                    unset($values['headerLogoImage']);
                }
                if (isset($values['headerTitleVisibility'])) {
                    $values['logoTextVisibility'] = $values['headerTitleVisibility'];
                    unset($values['headerTitleVisibility']);
                }
                if (isset($values['backgroundColor'])) {
                    $values['backgroundCSS'] = json_encode(['background-color' => $values['backgroundColor']], JSON_THROW_ON_ERROR);
                    unset($values['backgroundColor']);
                }
            }
            return $values;
        };
    });
