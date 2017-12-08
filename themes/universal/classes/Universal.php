<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\DefaultThemes;

use BearFramework\App;
use BearFramework\App\Context;

class Universal
{

    static function initialize()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        $context->assets
                ->addDir('themes/universal/assets');

        self::initializeLocalization();
    }

    static function getManifest()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        self::initializeLocalization();
        return [
            'name' => __('bearcms.themes.universal.name'),
            'description' => 'This is the default starter theme for each Bear CMS powered website. Simple yet highly customizable it enables you to create websites that look great on desktops, tables and smartphones. You can change the colors and visibility of the different content blocks.',
            'author' => [
                'name' => 'Bear CMS Team',
                'url' => 'https://bearcms.com/addons/',
                'email' => 'addons@bearcms.com',
            ],
            'media' => [
                [
                    'filename' => $context->dir . '/themes/universal/assets/t1.jpg',
                    'width' => 1024,
                    'height' => 768,
                ]
            ]
        ];
    }

    static function getOptions()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        return include $context->dir . '/themes/universal/options.php';
    }

    static function getStyles()
    {
        return [];
        $app = App::get();
        $context = $app->context->get(__FILE__);
        return include $context->dir . '/themes/universal/styles.php';
    }

    static function initializeLocalization()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        $app->localization
                ->addDictionary('en', function() use ($context) {
                    return include $context->dir . '/themes/universal/locales/en.php';
                })
                ->addDictionary('bg', function() use ($context) {
                    return include $context->dir . '/themes/universal//locales/bg.php';
                });
    }

    static function apply(App\Response $response, \BearCMS\Themes\Options $options)
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        if ($response instanceof App\Response\HTML) {
            $templateFilename = $context->dir . '/themes/universal/components/defaultTemplate.php';
            $hookName = 'bearCMSUniversalThemeDefaultTemplateCreated';
        } elseif ($response instanceof App\Response\NotFound) {
            $templateFilename = $context->dir . '/themes/universal/components/unavailableTemplate.php';
            $hookName = 'bearCMSUniversalThemeNotFoundTemplateCreated';
        } elseif ($response instanceof App\Response\TemporaryUnavailable) {
            $templateFilename = $context->dir . '/themes/universal/components/unavailableTemplate.php';
            $hookName = 'bearCMSUniversalThemeTemporaryUnavailableTemplateCreated';
        } else {
            return;
        }

        ob_start();
        // $options is used inside
        include $templateFilename;
        $templateContent = ob_get_clean();

        $template = new \BearFramework\HTMLTemplate($templateContent);
        $template->insert($options->toHTML());

        if ($app->hooks->exists($hookName)) {
            $templateContent = $template->get();
            $originalTemplateContent = $templateContent;
            $app->hooks->execute($hookName, $templateContent);
            if ($templateContent !== $originalTemplateContent) {
                $template->set($templateContent);
            }
        }

        $template->insert($response->content, 'body');
        $response->content = $template->get();
    }

}
