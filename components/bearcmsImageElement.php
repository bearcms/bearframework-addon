<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Config;
use BearCMS\Internal;
use BearCMS\Internal\Assets as InternalAssets;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$defaultAssetOptions = [
    'cacheMaxAge' => 999999999
];
$filename = Internal\Data::getRealFilename((string) $component->filename);
$filenameOptions = Internal\Data::getFilenameOptions($filename);
$filename = Internal\Data::removeFilenameOptions($filename);
if (!empty($filenameOptions)) {
    $defaultAssetOptions = array_merge($defaultAssetOptions, InternalAssets::convertFileOptionsToAssetOptions($filenameOptions));
}
$assetOptions = InternalAssets::getAssetOptionsFromHTMLAttributes($component->getAttributes(), $defaultAssetOptions);

$onClick = 'none';
if ($component->onClick === 'fullscreen') {
    $onClick = 'fullscreen';
} elseif ($component->onClick === 'openUrl') {
    $onClick = 'url';
}
$onClickURL = $component->url;

$lazyLoad = 'true';
if ($component->lazyLoad === 'false') {
    $lazyLoad = 'false';
}

$width = (string) $component->width; // Deprecated on 14 August 2021
$align = (string) $component->align; // Deprecated on 14 August 2021
if ($align !== 'left' && $align !== 'center' && $align !== 'right') {
    $align = 'left';
}

$minAssetWidth = (int)$component->minImageWidth;
if ($minAssetWidth === 0) {
    $minAssetWidth = null;
}
$minAssetHeight = (int)$component->minImageHeight;
if ($minAssetHeight === 0) {
    $minAssetHeight = null;
}
$maxAssetWidth = (int)$component->maxImageWidth;
if ($maxAssetWidth === 0) {
    $maxAssetWidth = 4000;
}
$maxAssetHeight = (int)$component->maxImageHeight;
if ($maxAssetHeight === 0) {
    $maxAssetHeight = 4000;
}

$attributes = '';

$onClickHTML = null;
if ($onClick === 'url') {
    list($onClickURL, $onClickValue, $onClickHTML) = \BearCMS\Internal\Links::updateURL($onClickURL);
    if ($onClickValue !== null) {
        $onClick = 'script';
        $onClickScript = $onClickValue;
    }
}
$attributes .= ' onclick="' . $onClick . '"';

$class = (string) $component->class;
$classAttributeValue = isset($class[0]) ? ' ' . htmlentities($class) : '';

$imageLoadingBackground = (string)$component->loadingBackground;
if ($imageLoadingBackground === '') {
    $lazyImageLoadingBackground = Config::getVariable('lazyImageLoadingBackground');
    if ($lazyImageLoadingBackground !== null) {
        $imageLoadingBackground = (string)$lazyImageLoadingBackground;
    }
}
if ($imageLoadingBackground !== '') {
    $attributes .= ' image-loading-background="' . htmlentities($imageLoadingBackground) . '"';
}

$previewImageLoadingBackground = (string)Config::getVariable('lazyImagePreviewLoadingBackground');
if ($previewImageLoadingBackground !== '') {
    $attributes .= ' preview-image-loading-background="' . htmlentities($previewImageLoadingBackground) . '"';
}

$attributes .= ' lazy-load="' . $lazyLoad . '"';

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

$content = '';
if ($isFullHtmlOutputType) {
    $content = '<div class="bearcms-image-element' . $classAttributeValue . '">';
    if (isset($innerContainerStyle[0])) {
        $content .= '<div style="' . $innerContainerStyle . '">';
    }
    if ($filename !== '') {
        $content .= '<component src="image-gallery" columns-count="1"' . $attributes . ' internal-option-render-image-container="false" internal-option-render-container="false">';
        $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($onClickURL) . '"' : '') . '' . ($onClick === 'script' ? ' script="' . htmlentities($onClickScript) . '"' : '') . ' title="' . htmlentities((string)$component->title) . '" alt="' . htmlentities((string)$component->alt) . '" filename="' . $filename . '" file-width="' . $component->fileWidth . '" file-height="' . $component->fileHeight . '" min-asset-width="' . $minAssetWidth . '" min-asset-height="' . $minAssetHeight . '" max-asset-width="' . $maxAssetWidth . '" max-asset-height="' . $maxAssetHeight . '"' . InternalAssets::convertAssetOptionsToHTMLAttributes($assetOptions) . '/>';
        $content .= '</component>';
    }
    if (isset($innerContainerStyle[0])) {
        $content .= '</div>';
    }
    $content .= '</div>';
} else {
    $content .= '<div>';
    if ($filename !== '') {
        $content .= '<img src="' . htmlentities($app->assets->getURL($filename, $assetOptions)) . '" style="max-width:100%;">';
    }
    $content .= '</div>';
}
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>.bearcms-image-element, .bearcms-image-element *{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
if ($onClickHTML !== null) {
    echo $onClickHTML;
}
echo '</body></html>';
