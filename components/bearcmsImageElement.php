<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();

$onClick = 'none';
if ($component->onClick === 'fullscreen') {
    $onClick = 'fullscreen';
} elseif ($component->onClick === 'openUrl') {
    $onClick = 'url';
}

$lazyLoad = 'false';
if ($component->lazyLoad === 'true') {
    $lazyLoad = 'true';
}

$attributes = '';

$attributes .= ' onClick="' . $onClick . '"';

$class = (string) $component->class;
$classAttributeValue = isset($class{0}) ? ' ' . htmlentities($class) : '';

if (strlen($component->loadingBackground) > 0) {
    $attributes .= ' imageLoadingBackground="' . $component->loadingBackground . '"';
}

$attributes .= ' lazyLoadImages="' . $lazyLoad . '"';

$filename = (string) $component->filename;
$content = '<div class="bearcms-image-element' . $classAttributeValue . '" style="font-size:0;">';
if (isset($filename{0})) {
    $filename = $app->bearCMS->data->getRealFilename($filename);
    $content .= '<component src="image-gallery" columnsCount="1"' . $attributes . ' internal-option-render-image-container="false" internal-option-render-container="false">';
    $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $filename . '"/>';
    $content .= '</component>';
}
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'image', $content);
?><html>
    <body><?= $content ?></body>
</html>