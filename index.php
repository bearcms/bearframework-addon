<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

$app = App::get();
$context = $app->contexts->get(__DIR__);

$context->classes
    ->add('BearCMS', 'classes/BearCMS.php')
    ->add('BearCMS\*', 'classes/BearCMS/*.php');

$context->assets
    ->addDir('assets');

$app->assets
    ->addDir('appdata://bearcms/files/themeimage/')
    ->addDir('appdata://bearcms/files/elementstyleimage/')
    ->addDir('appdata://bearcms/files/canvasstyleimage/')
    ->addDir('appdata://bearcms/files/blog/')
    ->addDir('appdata://bearcms/files/video/')
    ->addDir('appdata://bearcms/files/image/')
    ->addDir('appdata://bearcms/files/imagegallery/')
    ->addDir('appdata://bearcms/files/icon/')
    ->addDir('appdata://bearcms/files/page/')
    ->addDir('appdata://.temp/bearcms/files/themeimage/')
    ->addDir('appdata://.temp/bearcms/files/elementstyleimage/')
    ->addDir('appdata://.temp/bearcms/files/canvasstyleimage/')
    ->addDir('appdata://.temp/bearcms/export/');

$app->localization
    ->addDictionary('en', function () use ($context) {
        return include $context->dir . '/locales/en.php';
    })
    ->addDictionary('bg', function () use ($context) {
        return include $context->dir . '/locales/bg.php';
    })
    ->addDictionary('ru', function () use ($context) {
        return include $context->dir . '/locales/ru.php';
    });

$app->shortcuts
    ->add('bearCMS', function () {
        return new BearCMS();
    });

BearCMS\Internal2::initialize();

if ($app->request->method === 'GET') {
    if (strpos($app->request->path, $app->assets->pathPrefix) !== 0) {
        $cacheBundlePath = $app->request->path->get();
        Internal\Data::loadCacheBundle($cacheBundlePath);
        $app
            ->addEventListener('sendResponse', function () use ($cacheBundlePath) {
                Internal\Data::saveCacheBundle($cacheBundlePath);
            });
    }
}

$app->data
    ->addEventListener('itemChange', function (\BearFramework\App\Data\ItemChangeEventDetails $details) use (&$app) {
        $key = $details->key;

        Internal\Data::onDataChanged($key);

        $prefixes = [
            'bearcms/pages/page/',
            'bearcms/blog/post/'
        ];
        foreach ($prefixes as $prefix) {
            if (strpos($key, $prefix) === 0) {
                $dataBundleID = 'bearcmsdataprefix-' . $prefix;
                if ($app->data->exists($key)) {
                    $app->dataBundle->addItem($dataBundleID, $key);
                } else {
                    $app->dataBundle->removeItem($dataBundleID, $key);
                }
                break;
            }
        }
    });

$app
    ->addEventListener('sendResponse', function () use ($app) {
        if (Internal\Data::$hasContentChange) {
            $app->bearCMS->dispatchEvent('internalChangeData');
        }
    });
