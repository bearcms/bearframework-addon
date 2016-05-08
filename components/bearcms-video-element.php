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
        $content = '<div class="bearcms-video-element"></div>';
    }
} elseif (strlen($component->filename) > 0) {

    $filename = $component->filename;
    if (substr($filename, 0, 5) === 'data:') {
        $filename = $app->data->getFilename(substr($filename, 5));
    } elseif (substr($filename, 0, 4) === 'app:') {
        $filename = $app->config->appDir . DIRECTORY_SEPARATOR . substr($filename, 4);
    } elseif (substr($filename, 0, 6) === 'addon:') {
        $temp = explode(':', $filename, 3);
        if (sizeof($temp) === 3) {
            $addonDir = \BearFramework\Addons::getDir($temp[1]);
            $filename = $addonDir . DIRECTORY_SEPARATOR . $temp[2];
        }
    }
    $content .= '<div class="bearcms-video-element" style="font-size:0;"><video style="width:100%" controls>';
    $content .= '<source src="' . $app->assets->getUrl($filename) . '" type="video/mp4">';
    $content .= '</video></div>';
}

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'video', $content);
?><html>
    <body><?= $content ?></body>
</html>