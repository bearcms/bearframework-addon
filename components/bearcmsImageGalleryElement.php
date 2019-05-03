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

$files = null;
if (strlen($component->innerHTML) > 0) {
    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
    $files = $domDocument->querySelectorAll('file');
}

$spacing = $component->spacing;

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
        $filename = $file->getAttribute('filename');
        $newFilename = Internal2::$data2->getRealFilename($filename);
        if ($newFilename !== null) {
            $filename = $newFilename;
        }
        $content .= '<file class="bearcms-image-gallery-element-image" filename="' . htmlentities($filename) . '"/>';
    }
}
$content .= '</component>';
$content .= '</div>';
?><html>
    <head><style>.bearcms-image-gallery-element{font-size:0;}</style></head>
    <body><?= $content ?></body>
</html>