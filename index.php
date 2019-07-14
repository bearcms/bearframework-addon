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
    ->addEventListener('sendResponse', function () use ($app) {
        if (Internal\Data::$hasContentChange) {
            $app->bearCMS->dispatchEvent('internalChangeData');
        }
    });

$app->clientPackages
    ->add('-bearcms-responsively-lazy', function ($package) use ($context) {
        $package->addCSSCode('.responsively-lazy:not(img){position:relative;height:0;}.responsively-lazy:not(img)>img{position:absolute;top:0;left:0;width:100%;height:100%}img.responsively-lazy{width:100%;}');
        $package->addJSFile($context->assets->getURL('assets/responsivelyLazy.min.js', ['cacheMaxAge' => 999999999, 'version' => 2]), ['async' => true]);
    })
    ->add('-bearcms-responsive-attributes', function ($package) {
        $package->addJSCode('responsiveAttributes=function(){var u=[],g=function(){for(var g=document.querySelectorAll("[data-responsive-attributes]"),t=g.length,v=0;v<t;v++){var l=g[v],r=l.getBoundingClientRect();r={width:r.width,height:r.height};var f=l.getAttribute("data-responsive-attributes");if("undefined"===typeof u[f]){for(var c=f.split(","),m=c.length,h=[],e=0;e<m;e++){var b=c[e].split("=>");if("undefined"!==typeof b[0]&&"undefined"!==typeof b[1]){var n=b[0].trim();if(0<n.length){var a=b[1].split("=");"undefined"!==typeof a[0]&&"undefined"!==typeof a[1]&&(b=a[0].trim(),0<b.length&&(a=a[1].trim(),0<a.length&&("undefined"===typeof h[b]&&(h[b]=[]),h[b].push([n,a]))))}}}u[f]=h}f=u[f];for(var w in f){c=l.getAttribute(w);null===c&&(c="");c=0<c.length?c.split(" "):[];m=f[w];h=m.length;for(e=0;e<h;e++){n=m[e][1];b=l;a=m[e][0];for(var p=r,d=[],k=0;100>k;k++){var q="f"+d.length,x=a.match(/f\((.*?)\)/);if(null===x)break;a=a.replace(x[0],q);d.push([q,x[1]])}a=a.split("w").join(p.width).split("h").join(p.height);for(k=d.length-1;0<=k;k--)q=d[k],a=a.replace(q[0],q[1]+"(element,details)");b=(new Function("element","details","return "+a))(b,p);a=!1;p=c.length;for(d=0;d<p;d++)if(c[d]===n){b?a=!0:c.splice(d,1);break}b&&!a&&c.push(n)}l.setAttribute(w,c.join(" "))}}},t=function(){window.addEventListener("resize",g);window.addEventListener("load",g);"undefined"!==typeof MutationObserver&&(new MutationObserver(function(){g()})).observe(document.querySelector("body"),{childList:!0,subtree:!0})};"loading"===document.readyState?document.addEventListener("DOMContentLoaded",t):t();return{run:g}}();');
        $package->get = 'return responsiveAttributes;';
    })
    ->add('-bearcms-html5domdocument', function ($package) use ($context) {
        $package->addJSFile($context->assets->getURL('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]));
        $package->get = 'return html5DOMDocument;';
    });
