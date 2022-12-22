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
use IvoPetkov\HTML5DOMDocument;
use BearCMS\Internal\Assets as InternalAssets;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$filesElements = null;
if (strlen($component->innerHTML) > 0) {
    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
    $filesElements = $domDocument->querySelectorAll('file');
}

$filesData = [];
if ($filesElements !== null) {
    foreach ($filesElements as $file) {
        $filename = Internal\Data::getRealFilename((string)$file->getAttribute('filename'));
        if ($filename !== '') {
            $defaultAssetOptions = [
                'cacheMaxAge' => 999999999
            ];
            $filenameOptions = Internal\Data::getFilenameOptions($filename);
            $filename = Internal\Data::removeFilenameOptions($filename);
            if (!empty($filenameOptions)) {
                $defaultAssetOptions = array_merge($defaultAssetOptions, InternalAssets::convertFileOptionsToAssetOptions($filenameOptions));
            }
            $assetOptions = InternalAssets::getAssetOptionsFromHTMLAttributes($file->getAttributes(), $defaultAssetOptions);
            $filesData[] = [
                'filename' => $filename,
                'fileElement' => $file,
                'assetOptions' => $assetOptions,
            ];
        }
    }
}

$spacing = $component->spacing;

$content = '';
if ($isFullHtmlOutputType) {
    $content = '<div class="bearcms-image-gallery-element">';

    $attributes = '';
    if ($component->type !== null && strlen($component->type) > 0) {
        $attributes .= ' type="' . $component->type . '"';
    }
    if ($component->columnsCount !== null && strlen($component->columnsCount) > 0) {
        $attributes .= ' columns-count="' . $component->columnsCount . '"';
    }
    if ($component->imageSize !== null && strlen($component->imageSize) > 0) {
        $attributes .= ' image-size="' . $component->imageSize . '"';
    }
    if ($component->imageAspectRatio !== null && strlen($component->imageAspectRatio) > 0) {
        $attributes .= ' image-aspect-ratio="' . $component->imageAspectRatio . '"';
    }
    $imageLoadingBackground = (string)$component->imageLoadingBackground;
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

    if ($component->lazyLoadImages !== null && strlen($component->lazyLoadImages) > 0) {
        $attributes .= ' lazy-load="' . $component->lazyLoadImages . '"';
    } else {
        $attributes .= ' lazy-load="true"';
    }

    $content .= '<component src="image-gallery" spacing="' . $spacing . '"' . $attributes . '>';
    foreach ($filesData as $fileData) {
        $file = $fileData['fileElement'];
        $maxAssetWidth = (string)$file->getAttribute('max-asset-width');
        if (!isset($maxAssetWidth[0])) {
            $maxAssetWidth = 4000;
        }
        $maxAssetHeight = (string)$file->getAttribute('max-asset-height');
        if (!isset($maxAssetHeight[0])) {
            $maxAssetHeight = 4000;
        }
        $content .= '<file class="bearcms-image-gallery-element-image"'
            . ' filename="' . htmlentities($fileData['filename']) . '"'
            . ' min-asset-width="' . htmlentities($file->getAttribute('min-asset-width')) . '"'
            . ' min-asset-height="' . htmlentities($file->getAttribute('min-asset-height')) . '"'
            . ' max-asset-width="' . htmlentities($maxAssetWidth) . '"'
            . ' max-asset-height="' . htmlentities($maxAssetHeight) . '"'
            . ' file-width="' . htmlentities($file->getAttribute('file-width')) . '"'
            . ' file-height="' . htmlentities($file->getAttribute('file-height')) . '"'
            . ' title="' . htmlentities($file->getAttribute('title')) . '"'
            . ' alt="' . htmlentities($file->getAttribute('alt')) . '"'
            . InternalAssets::convertAssetOptionsToHTMLAttributes($fileData['assetOptions']) . '/>';
    }
    $content .= '</component>';
    $content .= '</div>';
} else {
    $content .= '<div>';
    foreach ($filesData as $fileData) {
        $content .= '<img src="' . htmlentities($app->assets->getURL($fileData['filename'], $fileData['assetOptions'])) . '" style="max-width:100%;">';
    }
    $content .= '</div>';
}
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>.bearcms-image-gallery-element, .bearcms-image-gallery-element *{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
