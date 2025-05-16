<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$contentID = (string)$component->contentID;

$attributes = $isFullHtmlOutputType ? ' class="bearcms-shared-content-element"' : '';

$elementsContent = '<bearcms-elements id="bearcms-shared-content-' . htmlentities($contentID) . '" editable="false" />';

echo '<html><body>';
echo '<div' . $attributes . '>' . $elementsContent . '</div>';
echo '</body></html>';
