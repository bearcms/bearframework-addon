<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;
use IvoPetkov\HTML5DOMDocument;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$files = null;
if (strlen($component->innerHTML) > 0) {
    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
    $files = $domDocument->querySelectorAll('file');
}

$spacing = $component->spacing;

$fixFilename = function ($filename): ?string {
    if (isset($filename[0])) {
        return Internal2::$data2->fixFilename($filename);
    }
    return null;
};
$content = '';
if ($isFullHtmlOutputType) {
    $content = '<div class="bearcms-image-gallery-element">';

    $attributes = '';
    if ($component->type !== null && strlen($component->type) > 0) {
        $attributes .= ' type="' . $component->type . '"';
    }
    if ($component->columnsCount !== null && strlen($component->columnsCount) > 0) {
        $attributes .= ' columnsCount="' . $component->columnsCount . '"';
    }
    if ($component->imageSize !== null && strlen($component->imageSize) > 0) {
        $attributes .= ' imageSize="' . $component->imageSize . '"';
    }
    if ($component->imageAspectRatio !== null && strlen($component->imageAspectRatio) > 0) {
        $attributes .= ' imageAspectRatio="' . $component->imageAspectRatio . '"';
    }
    if ($component->imageLoadingBackground !== null && strlen($component->imageLoadingBackground) > 0) {
        $attributes .= ' imageLoadingBackground="' . $component->imageLoadingBackground . '"';
    }
    if ($component->lazyLoadImages !== null && strlen($component->lazyLoadImages) > 0) {
        $attributes .= ' lazyLoadImages="' . $component->lazyLoadImages . '"';
    } else {
        $attributes .= ' lazyLoadImages="true"';
    }

    $content .= '<component src="image-gallery" spacing="' . $spacing . '"' . $attributes . '>';
    if ($files !== null) {
        foreach ($files as $file) {
            $fixedFilename = $fixFilename($file->getAttribute('filename'));
            if ($fixedFilename !== null) {
                $maxImageWidth = (string)$file->getAttribute('maximagewidth');
                if (!isset($maxImageWidth[0])) {
                    $maxImageWidth = 4000;
                }
                $maxImageHeight = (string)$file->getAttribute('maximageheight');
                if (!isset($maxImageHeight[0])) {
                    $maxImageHeight = 4000;
                }
                $content .= '<file class="bearcms-image-gallery-element-image"'
                    . ' filename="' . htmlentities($fixedFilename) . '"'
                    . ' quality="' . htmlentities($file->getAttribute('quality')) . '"'
                    . ' minImageWidth="' . htmlentities($file->getAttribute('minimagewidth')) . '"'
                    . ' minImageHeight="' . htmlentities($file->getAttribute('minimageheight')) . '"'
                    . ' maxImageWidth="' . htmlentities($maxImageWidth) . '"'
                    . ' maxImageHeight="' . htmlentities($maxImageHeight) . '"'
                    . ' fileWidth="' . htmlentities($file->getAttribute('filewidth')) . '"'
                    . ' fileHeight="' . htmlentities($file->getAttribute('fileheight')) . '"/>';
            }
        }
    }
    $content .= '</component>';
    $content .= '</div>';
} else {
    echo '<div>';
    if ($files !== null) {
        foreach ($files as $file) {
            $fixedFilename = $fixFilename($file->getAttribute('filename'));
            if ($fixedFilename !== null) {
                $assetOptions = ['cacheMaxAge' => 999999999];
                $quality = (string)$file->getAttribute('quality');
                if (strlen($quality) > 0) {
                    $assetOptions['quality'] = (int)$quality;
                }
                $content .= '<img src="' . htmlentities($app->assets->getURL($fixedFilename, $assetOptions)) . '" style="max-width:100%;">';
            }
        }
    }
    echo '</div>';
}
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>.bearcms-image-gallery-element, .bearcms-image-gallery-element *{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
