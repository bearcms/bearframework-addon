<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$domDocument = new IvoPetkov\HTML5DOMDocument();
$domDocument->loadHTML($component->innerHTML);
$files = $domDocument->querySelectorAll('file');

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

$content .= '<component src="image-gallery" spacing="' . $spacing . '"' . $attributes . '>';
foreach ($files as $file) {
    $filename = $file->getAttribute('filename');
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
    $content .= '<file class="bearcms-image-gallery-element-image" filename="' . htmlentities($filename) . '"/>';
}
$content .= '</component>';
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'imageGallery', $content);
?><html>
    <body><?= $content ?></body>
</html>