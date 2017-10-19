<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();

$files = null;
if (strlen($component->innerHTML) > 0) {
    $domDocument = new IvoPetkov\HTML5DOMDocument();
    $domDocument->loadHTML($component->innerHTML);
    $files = $domDocument->querySelectorAll('file');
}

$spacing = $component->spacing;

$content = '<div class="bearcms-image-gallery-element" style="font-size:0;">';

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
        $filename = $app->bearCMS->data->getRealFilename($file->getAttribute('filename'));
        $content .= '<file class="bearcms-image-gallery-element-image" filename="' . htmlentities($filename) . '"/>';
    }
}
$content .= '</component>';
$content .= '</div>';
?><html>
    <body><?= $content ?></body>
</html>