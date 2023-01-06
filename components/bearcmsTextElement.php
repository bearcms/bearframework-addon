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

$attributes = $isFullHtmlOutputType ? ' class="bearcms-text-element"' : '';

$text = $component->text;

$content = '<div' . $attributes . '>' . $text . '</div>';

echo '<html>';
if ($isFullHtmlOutputType) {
    echo '<head><style>';
    echo '.bearcms-text-element{display:block;zoom:1;word-break:break-word;}'; // no clear:both - breaks floating box
    echo '.bearcms-text-element:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
    echo '</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
