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
    ->addDir('appdata://bearcms/files/blog/')
    ->addDir('appdata://bearcms/files/video/')
    ->addDir('appdata://bearcms/files/image/')
    ->addDir('appdata://bearcms/files/imagegallery/')
    ->addDir('appdata://bearcms/files/icon/')
    ->addDir('appdata://bearcms/files/page/')
    ->addDir('appdata://.temp/bearcms/files/themeimage/')
    ->addDir('appdata://.temp/bearcms/files/elementstyleimage/')
    ->addDir('appdata://.temp/bearcms/themeexport/');

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

$changedDataKeys = [];
$app->data
    ->addEventListener('itemChange', function (\BearFramework\App\Data\ItemChangeEventDetails $details) use (&$app, &$changedDataKeys) {
        $key = $details->key;

        Internal\Data::onDataChanged($key);

        $changedDataKeys[] = $key;

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
    ->addEventListener('sendResponse', function () use ($app, &$changedDataKeys) {
        if (Internal\Data::$hasContentChange) {
            $app->bearCMS->dispatchEvent('internalChangeData');
            \BearCMS\Internal\Sitemap::addCheckSitemapForChangesTask();
        }
        if (!empty($changedDataKeys)) {
            Internal\Sitemap::onDataChanged($changedDataKeys);
        }
    });

$app->clientPackages
    ->add('-bearcms-responsively-lazy', function ($package) use ($context) {
        $package->addCSSCode('.responsively-lazy:not(img){position:relative;height:0;}.responsively-lazy:not(img)>img{position:absolute;top:0;left:0;width:100%;height:100%}img.responsively-lazy{width:100%;}');
        $package->addJSFile($context->assets->getURL('assets/responsivelyLazy.min.js', ['cacheMaxAge' => 999999999, 'version' => 2]), ['async' => true]);
    })
    ->add('-bearcms-responsive-attributes', function ($package) {
        $package->addJSCode('responsiveAttributes=function(){var v=[],w=!1,g=function(){if(!w){w=!0;for(var g=document.querySelectorAll("[data-responsive-attributes]"),u=g.length,x=0;x<u;x++){var h=g[x],r=h.getBoundingClientRect();r={width:r.width,height:r.height};var f=h.getAttribute("data-responsive-attributes");if("undefined"===typeof v[f]){for(var b=f.split(","),m=b.length,k=[],e=0;e<m;e++){var c=b[e].split("=>");if("undefined"!==typeof c[0]&&"undefined"!==typeof c[1]){var n=c[0].trim();if(0<n.length){var a=c[1].split("=");"undefined"!==typeof a[0]&&"undefined"!==typeof a[1]&&(c=a[0].trim(),0<c.length&&(a=a[1].trim(),0<a.length&&("undefined"===typeof k[c]&&(k[c]=[]),k[c].push([n,a]))))}}}v[f]=k}f=v[f];for(var t in f){b=h.getAttribute(t);null===b&&(b="");b=0<b.length?b.split(" "):[];m=f[t];k=m.length;for(e=0;e<k;e++){n=m[e][1];c=h;a=m[e][0];for(var p=r,d=[],l=0;100>l;l++){var q="f"+d.length,y=a.match(/f\((.*?)\)/);if(null===y)break;a=a.replace(y[0],q);d.push([q,y[1]])}a=a.split("vw").join(window.innerWidth).split("w").join(p.width).split("vh").join(window.innerHeight).split("h").join(p.height);for(l=d.length-1;0<=l;l--)q=d[l],a=a.replace(q[0],q[1]+"(element,details)");c=(new Function("element","details","return "+a))(c,p);a=!1;p=b.length;for(d=0;d<p;d++)if(b[d]===n){c?a=!0:b.splice(d,1);break}c&&!a&&b.push(n)}b=b.join(" ");h.getAttribute(t)!==b&&h.setAttribute(t,b)}}w=!1}},u=function(){window.addEventListener("resize",g);window.addEventListener("load",g);"undefined"!==typeof MutationObserver&&(new MutationObserver(function(){g()})).observe(document.querySelector("body"),{childList:!0,subtree:!0})};"loading"===document.readyState?document.addEventListener("DOMContentLoaded",u):u();return{run:g}}();');
        $package->get = 'return responsiveAttributes;';
    })
    ->add('-bearcms-html5domdocument', function ($package) use ($context) {
        $package->addJSFile($context->assets->getURL('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]));
        $package->get = 'return html5DOMDocument;';
    });
