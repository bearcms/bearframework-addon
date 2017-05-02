<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

$content = '';
if (strlen($component->url) > 0) {
    $aspectRatio = 0.75;
    $cacheKey = 'bearcms-video-element-data-' . $component->url;
    $cachedData = $app->cache->getValue($cacheKey);
    if ($cachedData === null) {
        try {
            $embed = new IvoPetkov\VideoEmbed($component->url);
            $aspectRatio = $embed->width / $embed->height;
            $embed->setSize('100%', '100%');
            $content = $embed->html;
            $app->cache->set($app->cache->make($cacheKey, ['html' => $embed->html, 'aspectRatio' => $aspectRatio]));
        } catch (\Exception $e) {
            $content = '';
            $cacheItem = $app->cache->make($cacheKey, '');
            $cacheItem->ttl = 60;
            $app->cache->set($cacheItem);
        }
    } else {
        if (is_array($cachedData)) {
            $content = $cachedData['html'];
            $aspectRatio = $cachedData['aspectRatio'];
        }
    }
    if ($content !== '') {
        $content = '<div style="position:absolute;top:0;left:0;width:100%;height:100%;">' . $content . '</div>';
        $content = '<div class="responsively-lazy bearcms-video-element" style="font-size:0;padding-bottom:' . (1 / $aspectRatio * 100) . '%;" data-lazycontent="' . htmlentities($content) . '"></div>';
    } else {
        $content = '';
    }
} elseif (strlen($component->filename) > 0) {
    $filename = $app->bearCMS->data->getRealFilename($component->filename);
    $content .= '<div class="bearcms-video-element" style="font-size:0;"><video style="width:100%" controls>';
    $content .= '<source src="' . $app->assets->getUrl($filename) . '" type="video/mp4">';
    $content .= '</video></div>';
}
?><html>
    <head>
        <style id="responsively-lazy-style">.responsively-lazy:not(img){position:relative;height:0;}.responsively-lazy:not(img)>img{position:absolute;top:0;left:0;width:100%;height:100%}img.responsively-lazy{width:100%;}</style>
        <script id="responsively-lazy-script" src="<?= $context->assets->getUrl('assets/responsivelyLazy.min.js', ['cacheMaxAge' => 999999, 'version' => 1]) ?>" async/>
    </head>
    <body><?= $content ?></body>
</html>