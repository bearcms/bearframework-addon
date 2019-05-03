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
$context = $app->contexts->get(__FILE__);

$context->classes
        ->add('BearCMS', 'classes/BearCMS.php')
        ->add('BearCMS\*', 'classes/BearCMS/*.php');

$context->assets
        ->addDir('assets');

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

            Internal\Data::setChanged($key);

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

$app->clientShortcuts
        ->add('-bearcms-responsively-lazy', function($shortcut) use ($context) {
            $shortcut->requirements[] = [
                'type' => 'text',
                'value' => '.responsively-lazy:not(img){position:relative;height:0;}.responsively-lazy:not(img)>img{position:absolute;top:0;left:0;width:100%;height:100%}img.responsively-lazy{width:100%;}',
                'mimeType' => 'text/css'
            ];
            $shortcut->requirements[] = [
                'type' => 'file',
                'url' => $context->assets->getURL('assets/responsivelyLazy.min.js', ['cacheMaxAge' => 999999999, 'version' => 2]),
                'async' => true,
                'mimeType' => 'text/javascript'
            ];
        })
        ->add('-bearcms-responsive-attributes', function($shortcut) {
            $shortcut->requirements[] = [// taken from dev/responsiveAttributes.min.js
                'type' => 'text',
                'value' => 'responsiveAttributes=function(){var q=[],f=function(){for(var f=document.querySelectorAll("[data-responsive-attributes]"),p=f.length,r=0;r<p;r++){var g=f[r],c=g.getBoundingClientRect();g.responsiveAttributesCache=[Math.round(c.width),Math.round(c.height)];c=g.getAttribute("data-responsive-attributes");if("undefined"===typeof q[c]){for(var b=c.split(","),k=b.length,h=[],e=0;e<k;e++){var a=b[e].split("=>");if("undefined"!==typeof a[0]&&"undefined"!==typeof a[1]){var l=a[0].trim();if(0<l.length){var d=a[1].split("=");"undefined"!==typeof d[0]&&"undefined"!==typeof d[1]&&(a=d[0].trim(),0<a.length&&(d=d[1].trim(),0<d.length&&("undefined"===typeof h[a]&&(h[a]=[]),h[a].push([l,d]))))}}}q[c]=h}var c=q[c],m;for(m in c){b=g.getAttribute(m);null===b&&(b="");b=0<b.length?b.split(" "):[];k=c[m];h=k.length;for(e=0;e<h;e++){for(var l=k[e][1],a=g,a=(new Function("return "+k[e][0].split("w").join(a.responsiveAttributesCache[0]).split("h").join(a.responsiveAttributesCache[1])))(),d=!1,t=b.length,n=0;n<t;n++)if(b[n]===l){a?d=!0:b.splice(n,1);break}a&&!d&&b.push(l)}g.setAttribute(m,b.join(" "))}}},p=function(){window.addEventListener("resize",f);window.addEventListener("load",f);"undefined"!==typeof MutationObserver&&(new MutationObserver(function(){f()})).observe(document.querySelector("body"),{childList:!0,subtree:!0})};"loading"===document.readyState?document.addEventListener("DOMContentLoaded",p):p();return{run:f}}();',
                'mimeType' => 'text/javascript'
            ];
            $shortcut->get = 'return responsiveAttributes;';
        })
        ->add('-bearcms-html5domdocument', function($shortcut) use ($context) {
            $shortcut->requirements[] = [
                'type' => 'file',
                'url' => $context->assets->getURL('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]),
                'mimeType' => 'text/javascript'
            ];
            $shortcut->get = 'return html5DOMDocument;';
        });

