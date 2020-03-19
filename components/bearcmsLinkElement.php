<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

$url = $component->url;
$text = $component->text;
$title = $component->title;

echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>.bearcms-link-element{word-wrap:break-word;}</style></head>';
}
echo '<body>';
if ($outputType === 'full-html') {
    echo '<div class="bearcms-link-element">';
}
echo '<a title="' . htmlentities($title) . '" href="' . htmlentities($url) . '">' . htmlspecialchars(isset($text[0]) ? $text : $url) . '</a>';
if ($outputType === 'full-html') {
    echo '</div>';
}
echo '</body>'</html>';
