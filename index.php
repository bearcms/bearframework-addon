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
    ->add('-bearcms-responsive-attributes', function ($package) {
        $package->addJSCode('responsiveAttributes=function(){var u=[],v=!1,t=function(){if(!v){v=!0;for(var z=document.querySelectorAll("[data-responsive-attributes]"),B=z.length,w=0;w<B;w++){var g=z[w],p=g.getBoundingClientRect();p={width:p.width,height:p.height};var f=g.getAttribute("data-responsive-attributes");if("undefined"===typeof u[f]){for(var b=f.split(","),l=b.length,h=[],d=0;d<l;d++){var c=b[d].split("=>");if("undefined"!==typeof c[0]&&"undefined"!==typeof c[1]){var m=c[0].trim();if(0<m.length){var a=c[1].split("=");"undefined"!==typeof a[0]&&"undefined"!==typeof a[1]&&(c=a[0].trim(),0<c.length&&(a=a[1].trim(),0<a.length&&("undefined"===typeof h[c]&&(h[c]=[]),h[c].push([m,a]))))}}}u[f]=h}f=u[f];for(var q in f){b=g.getAttribute(q);null===b&&(b="");b=0<b.length?b.split(" "):[];l=f[q];h=l.length;for(d=0;d<h;d++){m=l[d][1];c=g;a=l[d][0];for(var e=p,r=[],k=0;100>k;k++){var n="f"+r.length,x=a.match(/f\((.*?)\)/);if(null===x)break;a=a.replace(x[0],n);r.push([n,x[1]])}a=a.split("vw").join(window.innerWidth).split("w").join(e.width).split("vh").join(window.innerHeight).split("h").join(e.height);for(k=r.length-1;0<=k;k--)n=r[k],a=a.replace(n[0],n[1]+"(element,details)");try{var y=(new Function("element","details","return "+a))(c,e)}catch(C){y=!1}c=!1;a=b.length;for(e=0;e<a;e++)if(b[e]===m){y?c=!0:b.splice(e,1);break}y&&!c&&b.push(m)}b=b.join(" ");g.getAttribute(q)!==b&&g.setAttribute(q,b)}}v=!1}},A=function(){window.addEventListener("resize",t);window.addEventListener("load",t);"undefined"!==typeof MutationObserver&&(new MutationObserver(function(){t()})).observe(document.querySelector("body"),{childList:!0,subtree:!0})};"loading"===document.readyState?document.addEventListener("DOMContentLoaded",A):A();return{run:t}}();');
        $package->get = 'return responsiveAttributes;';
    })
    ->add('-bearcms-html5domdocument', function ($package) use ($context) {
        $package->addJSFile($context->assets->getURL('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]));
        $package->get = 'return html5DOMDocument;';
    });
