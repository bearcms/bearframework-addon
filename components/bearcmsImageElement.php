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

$lazyLoad = 'true';
if ($component->lazyLoad === 'false') {
    $lazyLoad = 'false';
}

$width = (string) $component->width;
$align = (string) $component->align;
if ($align !== 'left' && $align !== 'center' && $align !== 'right') {
    $align = 'left';
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

$content = '<div class="bearcms-image-element' . $classAttributeValue . '" style="font-size:0;">';
if (isset($innerContainerStyle{0})) {
    $content .= '<div style="' . $innerContainerStyle . '">';
}
if (isset($filename{0})) {
    $newFilename = $app->bearCMS->data->getRealFilename($filename);
    if ($newFilename !== null) {
        $filename = $newFilename;
    }
    $content .= '<component src="image-gallery" columnsCount="1"' . $attributes . ' internal-option-render-image-container="false" internal-option-render-container="false">';
    $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $filename . '"/>';
    $content .= '</component>';
}
if (isset($innerContainerStyle{0})) {
    $content .= '</div>';
}
$content .= '</div>';
?><html>
    <body><?= $content ?></body>
</html>