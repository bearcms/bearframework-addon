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

$width = (string) $component->width;
$align = (string) $component->align;
if ($align !== 'left' && $align !== 'center' && $align !== 'right') {
    $align = 'left';
}

$innerContainerStyle = '';
if (strlen($width) === 0) {
    if ($align === 'center') {
        $innerContainerStyle = 'text-align:center;';
    } elseif ($align === 'right') {
        $innerContainerStyle = 'text-align:right;';
    }
} else {
    $innerContainerStyle = 'max-width:' . $width . ';';
    if ($align === 'center') {
        $innerContainerStyle .= 'margin:0 auto;';
    } elseif ($align === 'right') {
        $innerContainerStyle .= 'margin-left:auto;';
    }
}

$innerContainerStartTag = '';
$innerContainerEndTag = '';
if (isset($innerContainerStyle{0})) {
    $innerContainerStartTag = '<div style="' . $innerContainerStyle . '">';
    $innerContainerEndTag = '</div>';
}

$content = '';
if (strlen($component->url) > 0) {
    $html = '';
    $aspectRatio = 0.75;
    $cacheKey = 'bearcms-video-element-data-' . md5($component->url) . '-2';
    $cachedData = $app->cache->getValue($cacheKey);
    if (is_array($cachedData)) {
        $html = $cachedData['html'];
        $aspectRatio = $cachedData['aspectRatio'];
    } else {
        $tempDataKey = '.temp/bearcms/videoelementdata/' . md5($component->url);
        $tempData = $app->data->getValue($tempDataKey);
        if ($tempData !== null) {
            $tempData = json_decode($tempData, true);
        }
        if (is_array($tempData)) {
            $html = $tempData['html'];
            $aspectRatio = $tempData['aspectRatio'];
        } else {
            try {
                $embed = new IvoPetkov\VideoEmbed($component->url);
                if ($embed->width > 0 && $embed->height) {
                    $aspectRatio = $embed->width / $embed->height;
                }
                $embed->setSize('100%', '100%');
                $html = $embed->html;
            } catch (\Exception $e) {
                $html = '';
            }
            if ($html !== '') {
                $app->data->set($app->data->make($tempDataKey, json_encode(['html' => $html, 'aspectRatio' => $aspectRatio])));
            }
        }
        $cacheItem = $app->cache->make($cacheKey, ['html' => $html, 'aspectRatio' => $aspectRatio]);
        if ($html === '') {
            $cacheItem->ttl = 60;
        }
        $app->cache->set($cacheItem);
    }
    if ($html !== '') {
        $content = '<div style="position:absolute;top:0;left:0;width:100%;height:100%;">' . $html . '</div>';
        $content = '<div class="responsively-lazy" style="padding-bottom:' . (1 / $aspectRatio * 100) . '%;" data-lazycontent="' . htmlentities($content) . '"></div>';
        $content = '<div class="bearcms-video-element" style="font-size:0;">' . $innerContainerStartTag . $content . $innerContainerEndTag . '</div>';
    } else {
        $content = '';
    }
} elseif (strlen($component->filename) > 0) {
    $filename = $component->filename;
    $newFilename = $app->bearCMS->data->getRealFilename($filename);
    if ($newFilename !== null) {
        $filename = $newFilename;
    }
    $content = '<div class="bearcms-video-element" style="font-size:0;">' . $innerContainerStartTag . '<video style="width:100%" controls>';
    $content .= '<source src="' . $app->assets->getUrl($filename) . '" type="video/mp4">';
    $content .= '</video>' . $innerContainerEndTag . '</div>';
}
?><html>
    <head>
        <style id="responsively-lazy-style">.responsively-lazy:not(img){position:relative;height:0;}.responsively-lazy:not(img)>img{position:absolute;top:0;left:0;width:100%;height:100%}img.responsively-lazy{width:100%;}</style>
        <script id="responsively-lazy-script" src="<?= $context->assets->getUrl('assets/responsivelyLazy.min.js', ['cacheMaxAge' => 999999999, 'version' => 2]) ?>" async/>
    </head>
    <body><?= $content ?></body>
</html>