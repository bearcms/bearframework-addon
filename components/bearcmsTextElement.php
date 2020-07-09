<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';

$attributes = $outputType === 'full-html' ? ' class="bearcms-text-element"' : '';

$content = '<div' . $attributes . '>' . $component->text . '</div>';

echo '<html>';
if ($outputType === 'full-html') {
    echo '<head><style>';
    echo '.bearcms-text-element{word-break:break-word;}';
    echo '.bearcms-text-element:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
    echo '</style></head>';
}
echo '<body>';
echo $content;
echo '</body></html>';
