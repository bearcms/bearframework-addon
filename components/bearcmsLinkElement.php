<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$url = $component->url;
$text = $component->text;
$title = $component->title;

list($url, $onClick, $linkHTML) = \BearCMS\Internal\Links::updateURL($url);

echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>.bearcms-link-element{word-break:break-word;}</style></head>';
}
echo '<body>';

echo '<div' . ($isFullHtmlOutputType ? ' class="bearcms-link-element"' : '') . '>';
echo '<a title="' . htmlentities($title) . '" href="' . htmlentities($url) . '"' . ($onClick !== null ? ' onclick="' . htmlentities($onClick) . '"' : '') . '>' . htmlspecialchars($text) . '</a>'; // htmlspecialchars(isset($text[0]) ? $text : $url)
echo '</div>';

if ($linkHTML !== null) {
    echo $linkHTML;
}

echo '</body></html>';
