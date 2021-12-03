<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$size = 'large';
$componentSize = (string)$component->size;
if (array_search($componentSize, ['large', 'medium', 'small']) !== false) {
    $size = $componentSize;
}

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

if ($size === 'large') {
    $className = 'bearcms-separator-element-large';
} elseif ($size === 'medium') {
    $className = 'bearcms-separator-element-medium';
} else {
    $className = 'bearcms-separator-element-small';
}

$attributes = $outputType === 'full-html' ? ' class="' . $className . '"' : '';

$content = '<div' . $attributes . '></div>';
echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>.' . $className . '{font-size:0;line-height:0;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
