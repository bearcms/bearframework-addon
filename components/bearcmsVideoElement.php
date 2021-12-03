<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;
use BearCMS\Internal\Config;

$app = App::get();
$context = $app->contexts->get(__DIR__);

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

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
if (isset($innerContainerStyle[0])) {
    $innerContainerStartTag = '<div style="' . $innerContainerStyle . '">';
    $innerContainerEndTag = '</div>';
}

$addResponsivelyLazy = false;
$addPriveteEmbedStyles = false;
$content = '';

$componentURL = (string)$component->url;
if (strlen($componentURL) > 0) {
    $videoExists = false;
    $videoTitle = null;
    $videoUrl = null;
    $videoImage = null;
    $videoAuthor = null;
    $videoProvider = null;
    $videoAspectRatio = 0.75;
    $videoHTML = null;
    $setData = function ($data) use (&$videoExists, &$videoTitle, &$videoUrl, &$videoImage, &$videoAuthor, &$videoProvider, &$videoAspectRatio, &$videoHTML) {
        $videoExists = (int) $data['exists'] > 0;
        if ($videoExists) {
            $videoTitle = $data['title'];
            $videoUrl = $data['url'];
            $videoImage = $data['image'];
            $videoAuthor = $data['author'];
            $videoProvider = $data['provider'];
            $videoAspectRatio = $data['aspectRatio'];
            $videoHTML = $data['html'];
        }
    };
    $getData = function () use (&$videoExists, &$videoTitle, &$videoUrl, &$videoImage, &$videoAuthor, &$videoProvider, &$videoAspectRatio, &$videoHTML) {
        if ($videoExists) {
            return [
                'exists' => true,
                'title' => $videoTitle,
                'url' => $videoUrl,
                'image' => $videoImage,
                'author' => $videoAuthor,
                'provider' => $videoProvider,
                'aspectRatio' => $videoAspectRatio,
                'html' => $videoHTML
            ];
        } else {
            return ['exists' => false];
        }
    };
    $cacheKey = 'bearcms-video-element-data-' . md5($componentURL) . '-3';
    $cachedData = $app->cache->getValue($cacheKey);
    if (is_array($cachedData)) { // && false
        $setData($cachedData);
    } else {
        $tempDataKey = '.temp/bearcms/videoelementdata/' . md5($componentURL . '-3');
        $tempData = $app->data->getValue($tempDataKey);
        if ($tempData !== null) {
            $tempData = json_decode($tempData, true);
        }
        if (is_array($tempData)) { // && false
            $setData($tempData);
        } else {
            try {
                $embed = new IvoPetkov\VideoEmbed($componentURL, Config::$videoEmbedConfig);
                $videoExists = true;
                $videoTitle = $embed->title;
                $videoUrl = $embed->url;
                $videoImage = isset($embed->thumbnail['url']) ? $embed->thumbnail['url'] : null;
                $videoAuthor = isset($embed->author['name']) ? $embed->author['name'] : null;
                $videoProvider = isset($embed->provider['name']) ? $embed->provider['name'] : null;
                if ($embed->width > 0 && $embed->height) {
                    $videoAspectRatio = $embed->width / $embed->height;
                }
                $embed->setSize('100%', '100%');
                $videoHTML = $embed->html;
            } catch (\Exception $e) {
                $videoExists = false;
            }
            if ($videoExists) {
                $app->data->set($app->data->make($tempDataKey, json_encode($getData())));
            }
        }

        $cacheItem = $app->cache->make($cacheKey, $getData());
        if (!$videoExists) {
            $cacheItem->ttl = 60;
        }
        $app->cache->set($cacheItem);
    }

    if ($videoExists) {
        if ($outputType === 'full-html') {
            if (Config::$videoPrivateEmbed) {
                $addPriveteEmbedStyles = true;
                $hasImage = $videoImage !== null && strlen($videoImage) > 0;
                $html = '<div style="width:100%;height:100%;' . ($hasImage ? 'background-image:url(' . $context->assets->getURL('assets/p/' . str_replace('://', '/', $videoImage), ['cacheMaxAge' => 86400 * 30]) . ');background-size:cover;background-position:center center;' : '') . '">' .
                    '<div class="bearcms-video-element-overlay" style="background-color:' . ($hasImage ? 'rgba(0,0,0,0.7)' : '#111') . ';">' .
                    '<div class="bearcms-video-element-title">' . htmlspecialchars($videoTitle) . '</div>' .
                    '<div class="bearcms-video-element-author">' . htmlspecialchars(sprintf(__('bearcms.elements.video.by %s'), $videoAuthor)) . '</div>' .
                    '<a class="bearcms-video-element-link" href="' . htmlentities($videoUrl) . '" rel="nofollow noopener" target="_blank">' . htmlspecialchars(sprintf(__('bearcms.elements.video.Play on %s'), $videoProvider)) . '</a>' .
                    '</div>' .
                    '</div>';
            } else {
                $html = $videoHTML;
            }
            $addResponsivelyLazy = true;
            $content = '<div style="position:absolute;top:0;left:0;width:100%;height:100%;">' . $html . '</div>';
            $content = '<div data-responsively-lazy-type="html" data-responsively-lazy="' . htmlentities($content) . '" style="position:relative;height:0;padding-bottom:' . (1 / $videoAspectRatio * 100) . '%"></div>';
            $content = '<div class="bearcms-video-element">' . $innerContainerStartTag . $content . $innerContainerEndTag . '</div>';
        } else {
            // todo update video width
            $content .= $videoHTML;
        }
    } else {
        $content = '';
    }
} elseif ($component->filename !== null && strlen($component->filename) > 0) {
    $filename = Internal2::$data2->fixFilename($component->filename);
    if ($outputType === 'full-html') {
        $content = '<div class="bearcms-video-element" style="">' . $innerContainerStartTag;
    }
    $content .= '<video style="width:100%;" controls>';
    $content .= '<source src="' . $app->assets->getURL($filename) . '" type="video/mp4">';
    $content .= '</video>';
    if ($outputType === 'full-html') {
        $content .= $innerContainerEndTag . '</div>';
    }
}
echo '<html>';
if ($outputType === 'full-html') {
    echo '<style>';
    echo '.bearcms-video-element{font-size:0;}';
    if ($addPriveteEmbedStyles) {
        echo '.bearcms-video-element-overlay{padding:20px;box-sizing:border-box;width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column;}';
        echo '.bearcms-video-element-title{text-align:center;color:#fff;font-size:16px;line-height:150%;font-family:Arial,Helvetica,sans-serif;}';
        echo '.bearcms-video-element-author{text-align:center;color:#fff;font-size:13px;line-height:150%;font-family:Arial,Helvetica,sans-serif;padding-top:15px;}';
        echo '.bearcms-video-element-link{text-decoration:none;margin-top:25px;font-size:14px;line-height:120%;font-family:Arial,Helvetica,sans-serif;display:inline-block;border-radius:2px;background-color:#fff;color:#111;padding:15px 20px;}';
    }
    echo '</style>';
    if ($addResponsivelyLazy) {
        echo '<head><link rel="client-packages-embed" name="responsivelyLazy"></head>';
    }
}
echo '<body>';
echo $content;
echo '</body></html>';
