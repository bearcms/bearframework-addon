<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$text = (string) $component->text;
$size = 'large';
$componentSize = (string)$component->size;
if (strlen($componentSize) > 0 && array_search($componentSize, ['large', 'medium', 'small']) !== false) {
    $size = $componentSize;
}

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

if ($size === 'large') {
    $tagName = 'h1';
    $classNames = 'bearcms-heading-element bearcms-heading-element-large';
} elseif ($size === 'medium') {
    $tagName = 'h2';
    $classNames = 'bearcms-heading-element bearcms-heading-element-medium';
} else {
    $tagName = 'h3';
    $classNames = 'bearcms-heading-element bearcms-heading-element-small';
}

$attributes = $isFullHtmlOutputType ? ' class="' . $classNames . '"' : '';
if ($isFullHtmlOutputType) {
    $componentLinkTargetID = (string)$component->linkTargetID;
    if (strlen($componentLinkTargetID) > 0) {
        $attributes .= ' id="' . htmlentities($componentLinkTargetID) . '"';
    }
}

$content = '<' . $tagName . $attributes . '>' . htmlspecialchars($text) . '</' . $tagName . '>';
echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>.bearcms-heading-element{word-break:break-word;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
