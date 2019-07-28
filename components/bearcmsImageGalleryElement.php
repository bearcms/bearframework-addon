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

$files = null;
if (strlen($component->innerHTML) > 0) {
    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
    $files = $domDocument->querySelectorAll('file');
}

$spacing = $component->spacing;

$fixFilename = function ($filename): ?string {
    if (isset($filename[0])) {
        $newFilename = Internal2::$data2->getRealFilename($filename);
        if ($newFilename !== null) {
            $filename = $newFilename;
        }
        return $filename;
    }
    return null;
};


$content = '';
if ($outputType === 'full-html') {
    $content = '<div class="bearcms-image-gallery-element">';

    $attributes = '';
    if (strlen($component->type) > 0) {
        $attributes .= ' type="' . $component->type . '"';
    }
    if (strlen($component->columnsCount) > 0) {
        $attributes .= ' columnsCount="' . $component->columnsCount . '"';
    }
    if (strlen($component->imageSize) > 0) {
        $attributes .= ' imageSize="' . $component->imageSize . '"';
    }
    if (strlen($component->imageAspectRatio) > 0) {
        $attributes .= ' imageAspectRatio="' . $component->imageAspectRatio . '"';
    }
    if (strlen($component->imageLoadingBackground) > 0) {
        $attributes .= ' imageLoadingBackground="' . $component->imageLoadingBackground . '"';
    }
    if (strlen($component->lazyLoadImages) > 0) {
        $attributes .= ' lazyLoadImages="' . $component->lazyLoadImages . '"';
    } else {
        $attributes .= ' lazyLoadImages="true"';
    }

    $content .= '<component src="image-gallery" spacing="' . $spacing . '"' . $attributes . '>';
    if ($files !== null) {
        foreach ($files as $file) {
            $fixedFilename = $fixFilename($file->getAttribute('filename'));
            if ($fixedFilename !== null) {
                $content .= '<file class="bearcms-image-gallery-element-image" filename="' . htmlentities($fixedFilename) . '"/>';
            }
        }
    }
    $content .= '</component>';
    $content .= '</div>';
} elseif ($outputType === 'simple-html') {
    if ($files !== null) {
        foreach ($files as $file) {
            $fixedFilename = $fixFilename($file->getAttribute('filename'));
            if ($fixedFilename !== null) {
                $content .= '<img src="' . htmlentities($app->assets->getURL($fixedFilename, ['cacheMaxAge' => 999999999])) . '">';
            }
        }
    }
}
echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>.bearcms-image-gallery-element, .bearcms-image-gallery-element *{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body>';
echo '</html>';
