<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;

$app = App::get();
$context = $app->contexts->get(__FILE__);

$context->classes
        ->add('BearCMS', 'classes/BearCMS.php')
        ->add('BearCMS\*', 'classes/BearCMS/*.php');

$app->addons
        ->add('ivopetkov/users-bearframework-addon', [
            'useDataCache' => Config::$useDataCache
        ]);

$context->assets
        ->addDir('assets')
        ->addDir('components/bearcmsBlogPostsElement/assets')
        ->addDir('components/bearcmsCommentsElement/assets');
$app->assets
        ->addDir('appdata://bearcms/files/themeimage/')
        ->addDir('appdata://bearcms/files/blog/')
        ->addDir('appdata://bearcms/files/video/')
        ->addDir('appdata://bearcms/files/image/')
        ->addDir('appdata://bearcms/files/imagegallery/')
        ->addDir('appdata://bearcms/files/icon/')
        ->addDir('appdata://.temp/bearcms/files/themeimage/')
        ->addDir('appdata://.temp/bearcms/themeexport/');

$app->localization
        ->addDictionary('en', function() use ($context) {
            return include $context->dir . '/locales/en.php';
        })
        ->addDictionary('bg', function() use ($context) {
            return include $context->dir . '/locales/bg.php';
        })
        ->addDictionary('ru', function() use ($context) {
            return include $context->dir . '/locales/ru.php';
        });

$app->shortcuts
        ->add('bearCMS', function() {
            return new BearCMS();
        });

BearCMS\Internal2::initialize();

if ($app->request->method === 'GET') {
    if (strpos($app->request->path, $app->assets->pathPrefix) !== 0) {
        $cacheBundlePath = $app->request->path->get();
        Internal\Data::loadCacheBundle($cacheBundlePath);
        $app
                ->addEventListener('sendResponse', function() use ($cacheBundlePath) {
                    Internal\Data::saveCacheBundle($cacheBundlePath);
                });
    }
}

$app->data
        ->addEventListener('itemChange', function(\BearFramework\App\Data\ItemChangeEventDetails $details) use (&$app) {
            $key = $details->key;
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
            if (strpos($key, '.temp/bearcms/userthemeoptions/') === 0 || strpos($key, 'bearcms/themes/theme/') === 0) {
                $currentThemeID = Internal\CurrentTheme::getID();
                if ($app->bearCMS->currentUser->exists()) {
                    $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID, $app->bearCMS->currentUser->getID());
                    if ($cacheItemKey !== null) {
                        $app->cache->delete($cacheItemKey);
                    }
                }
                $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID);
                if ($cacheItemKey !== null) {
                    $app->cache->delete($cacheItemKey);
                }
            }
        });

$app
        ->addEventListener('sendResponse', function() use ($app) {
            if (Internal\Data::$hasContentChange) {
                $app->bearCMS->dispatchEvent('internalChangeData');
            }
        });
