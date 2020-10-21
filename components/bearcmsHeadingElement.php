<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$text = (string) $component->text;
$size = 'large';
if (strlen($component->size) > 0) {
    if (array_search($component->size, ['large', 'medium', 'small']) !== false) {
        $size = $component->size;
    }
}

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

if ($size === 'large') {
    $tagName = 'h1';
    $className = 'bearcms-heading-element-large';
} elseif ($size === 'medium') {
    $tagName = 'h2';
    $className = 'bearcms-heading-element-medium';
} else {
    $tagName = 'h3';
    $className = 'bearcms-heading-element-small';
}

$attributes = $outputType === 'full-html' ? ' class="' . $className . '"' : '';
if (strlen($component->linkTargetID) > 0) {
    $attributes .= ' id="' . htmlentities($component->linkTargetID) . '"';
}

$content = '<' . $tagName . $attributes . '>' . htmlspecialchars($text) . '</' . $tagName . '>';
echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>.' . $className . '{word-break:break-word;}</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
