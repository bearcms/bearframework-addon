<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();

$domDocument = new IvoPetkov\HTML5DOMDocument();
$domDocument->loadHTML($component->innerHTML);
$files = $domDocument->querySelectorAll('file');

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

$content .= '<component src="image-gallery" spacing="' . $spacing . '"' . $attributes . '>';
foreach ($files as $file) {
    $filename = $app->bearCMS->data->getRealFilename($file->getAttribute('filename'));
    $content .= '<file class="bearcms-image-gallery-element-image" filename="' . htmlentities($filename) . '"/>';
}
$content .= '</component>';
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'imageGallery', $content);
?><html>
    <body><?= $content ?></body>
</html>