<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$content = '';
if (strlen($component->url) > 0) {
    $aspectRatio = 0.75;
    $cacheKey = 'bearcms-video-element-data-' . $component->url;
    $cachedData = $app->cache->get($cacheKey);
    if ($cachedData === false) {
        try {
            $embed = new IvoPetkov\VideoEmbed($component->url);
            $aspectRatio = $embed->width / $embed->height;
            $embed->setSize('100%', '100%');
            $content = $embed->html;
            $app->cache->set($cacheKey, ['html' => $embed->html, 'aspectRatio' => $aspectRatio]);
        } catch (\Exception $e) {
            $content = '';
            $app->cache->set($cacheKey, '', 60);
        }
    } else {
        if (is_array($cachedData)) {
            $content = $cachedData['html'];
            $aspectRatio = $cachedData['aspectRatio'];
        }
    }
    if ($content !== '') {
        $content = '<div class="bearcms-video-element" style="position:relative;height:0;padding-bottom:' . (1 / $aspectRatio * 100) . '%;"><div style="position: absolute;top:0;left:0;width:100%;height:100%;">' . $content . '</div></div>';
    } else {
        $content = '';
    }
} elseif (strlen($component->filename) > 0) {
    $filename = $app->bearCMS->data->getRealFilename($component->filename);
    $content .= '<div class="bearcms-video-element"><video style="width:100%" controls>';
    $content .= '<source src="' . $app->assets->getUrl($filename) . '" type="video/mp4">';
    $content .= '</video></div>';
}

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'video', $content);
?><html>
    <head>
        <style>
            .bearcms-video-element{
                font-size:0;
            }
        </style>
    </head>
    <body><?= $content ?></body>
</html>