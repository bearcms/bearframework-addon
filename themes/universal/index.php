<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\DefaultThemes\Universal;

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('BearCMS\DefaultThemes\Universal', 'themes/universal/classes/Universal.php');

$app->bearCMS->themes
        ->add('bearcms/universal', function() {
            return [
                'version' => '1',
                'initialize' => function() {
                    Universal::initialize();
                },
                'apply' => function($response, $options) {
                    Universal::apply($response, $options);
                },
                'manifest' => function() {
                    return Universal::getManifest();
                },
                'options' => function() {
                    return Universal::getOptions();
                },
                'styles' => function() {
                    return Universal::getStyles();
                }
            ];
        });
