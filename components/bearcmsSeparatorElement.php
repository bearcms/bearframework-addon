<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\ComponentUtilities;

$size = 'large';
$componentSize = (string)$component->size;
if (array_search($componentSize, ['large', 'medium', 'small']) !== false) {
    $size = $componentSize;
}

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

if ($size === 'large') {
    $classNames = 'bearcms-separator-element bearcms-separator-element-large';
} elseif ($size === 'medium') {
    $classNames = 'bearcms-separator-element bearcms-separator-element-medium';
} else {
    $classNames = 'bearcms-separator-element bearcms-separator-element-small';
}

$attributes = $isFullHtmlOutputType ? ' class="' . $classNames . '"' : '';

$content = '<div' . $attributes . '></div>';
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head>';
    $styleHTML = '<style>.bearcms-separator-element{font-size:0;line-height:0;}</style>';
    echo ComponentUtilities::createComponentFragment('bse', $styleHTML);
    echo '</head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
