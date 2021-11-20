<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

$onClick = 'none';
if ($component->onClick === 'fullscreen') {
    $onClick = 'fullscreen';
} elseif ($component->onClick === 'openUrl') {
    $onClick = 'url';
}

$lazyLoad = 'true';
if ($component->lazyLoad === 'false') {
    $lazyLoad = 'false';
}

$width = (string) $component->width; // Deprecated on 14 August 2021
$align = (string) $component->align; // Deprecated on 14 August 2021
if ($align !== 'left' && $align !== 'center' && $align !== 'right') {
    $align = 'left';
}

$minImageWidth = (int)$component->minImageWidth;
if ($minImageWidth === 0) {
    $minImageWidth = null;
}
$minImageHeight = (int)$component->minImageHeight;
if ($minImageHeight === 0) {
    $minImageHeight = null;
}
$maxImageWidth = (int)$component->maxImageWidth;
if ($maxImageWidth === 0) {
    $maxImageWidth = 4000;
}
$maxImageHeight = (int)$component->maxImageHeight;
if ($maxImageHeight === 0) {
    $maxImageHeight = 4000;
}

$attributes = '';

$attributes .= ' onClick="' . $onClick . '"';

$class = (string) $component->class;
$classAttributeValue = isset($class[0]) ? ' ' . htmlentities($class) : '';

if (strlen($component->loadingBackground) > 0) {
    $attributes .= ' imageLoadingBackground="' . $component->loadingBackground . '"';
}

$attributes .= ' lazyLoadImages="' . $lazyLoad . '"';

$filename = (string) $component->filename;

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

$fixFilename = function ($filename): ?string {
    if (isset($filename[0])) {
        return Internal2::$data2->fixFilename($filename);
    }
    return null;
};

$content = '';
if ($outputType === 'full-html') {
    $content = '<div class="bearcms-image-element' . $classAttributeValue . '">';
    if (isset($innerContainerStyle[0])) {
        $content .= '<div style="' . $innerContainerStyle . '">';
    }
    $fixedFilename = $fixFilename($filename);
    if ($fixedFilename !== null) {
        $content .= '<component src="image-gallery" columnsCount="1"' . $attributes . ' internal-option-render-image-container="false" internal-option-render-container="false">';
        $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $fixedFilename . '" quality="' . $component->quality . '" fileWidth="' . $component->fileWidth . '" fileHeight="' . $component->fileHeight . '" minImageWidth="' . $minImageWidth . '" minImageHeight="' . $minImageHeight . '" maxImageWidth="' . $maxImageWidth . '" maxImageHeight="' . $maxImageHeight . '"/>';
        $content .= '</component>';
    }
    if (isset($innerContainerStyle[0])) {
        $content .= '</div>';
    }
    $content .= '</div>';
} elseif ($outputType === 'simple-html') {
    $fixedFilename = $fixFilename($filename);
    if ($fixedFilename !== null) {
        $content = '<img src="' . htmlentities($app->assets->getURL($fixedFilename, ['cacheMaxAge' => 999999999])) . '">';
    }
}
echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>.bearcms-image-element, .bearcms-image-element *{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
