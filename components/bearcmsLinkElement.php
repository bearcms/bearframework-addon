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

$url = (string)$component->url;
$text = (string)$component->text;
$title = (string)$component->title;

list($linkURL, $linkOnClick, $linkHTML) = \BearCMS\Internal\Links::updateURL($url);

echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>'
        . '.bearcms-link-element{word-break:break-word;}'
        . '.bearcms-link-element a{cursor:pointer;}'
        . '</style></head>';
}
echo '<body>';

echo '<div' . ($isFullHtmlOutputType ? ' class="bearcms-link-element"' : '') . '>';
echo '<a title="' . htmlentities($title) . '"' . ($linkURL !== null ? ' href="' . htmlentities($linkURL) . '"' : '') . ($linkOnClick !== null ? ' onclick="' . htmlentities($linkOnClick) . '" role="button" onkeydown="if(event.keyCode===13){this.click();}"' : '') . ' tabindex="0">' . htmlspecialchars($text) . '</a>'; // htmlspecialchars(isset($text[0]) ? $text : $url)
echo '</div>';

if ($linkHTML !== null) {
    echo $linkHTML;
}

echo '</body></html>';
