<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;

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

$content = '<div class="bearcms-image-element' . $classAttributeValue . '">';
if (isset($innerContainerStyle{0})) {
    $content .= '<div style="' . $innerContainerStyle . '">';
}
if (isset($filename{0})) {
    $newFilename = Internal2::$data2->getRealFilename($filename);
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
    <head><style>.bearcms-image-element{font-size:0;}</style></head>
    <body><?= $content ?></body>
</html>