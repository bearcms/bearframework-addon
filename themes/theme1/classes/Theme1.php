<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

use BearFramework\App;
use BearFramework\App\Context;

class Theme1
{

    static function initialize(App $app, Context $context)
    {
        $context->assets
                ->addDir('themes/theme1/assets');

        self::initializeLocalization($app, $context);
    }

    static function initializeLocalization(App $app, Context $context)
    {
        $app->localization
                ->addDictionary('en', function() use ($context) {
                    return include $context->dir . '/themes/theme1/locales/en.php';
                })
                ->addDictionary('bg', function() use ($context) {
                    return include $context->dir . '/themes/theme1//locales/bg.php';
                });
    }

    static function apply(App $app, Context $context, App\Response $response, \BearCMS\Themes\Options $options)
    {
        $component = $app->components->create();
        if ($response instanceof App\Response\HTML) {
            $component->src = 'file:' . $context->dir . '/themes/theme1/components/defaultTemplate.php';
            $hookName = 'bearCMSTheme1DefaultTemplateCreated';
        } elseif ($response instanceof App\Response\NotFound) {
            $component->src = 'file:' . $context->dir . '/themes/theme1/components/unavailableTemplate.php';
            $hookName = 'bearCMSTheme1NotFoundTemplateCreated';
        } elseif ($response instanceof App\Response\TemporaryUnavailable) {
            $component->src = 'file:' . $context->dir . '/themes/theme1/components/unavailableTemplate.php';
            $hookName = 'bearCMSTheme1TemporaryUnavailableTemplateCreated';
        } else {
            return;
        }
        $templateContent = $app->components->process($component, [
            'variables' => [
                'options' => $options
            ],
            'recursive' => false
        ]);
        $domDocument = new \IvoPetkov\HTML5DOMDocument();
        $domDocument->loadHTML($templateContent);
        $domDocument->insertHTML($options->toHTML());
        $templateContent = $domDocument->saveHTML();

        $object = new \ArrayObject();
        $object->content = $templateContent;
        $app->hooks->execute($hookName, $object);
        $templateContent = $app->components->process($object->content);
        $template = new \BearFramework\HTMLTemplate($templateContent);
        $template->insert('body', $response->content);
        $response->content = $template->getResult();
    }

}
