<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$content = '<div class="bearcms-text-element">' . $component->text . '</div>';

echo '<html>';
echo '<head><style>';
echo '.bearcms-text-element{word-wrap:break-word;}';
echo '.bearcms-text-element:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
echo '</style></head>';
echo '<body>';
echo $content;
echo '</body>';
echo '</html>';
