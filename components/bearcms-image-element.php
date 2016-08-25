<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$onClick = 'none';
if ($component->onClick === 'fullscreen') {
    $onClick = 'fullscreen';
} elseif ($component->onClick === 'openUrl') {
    $onClick = 'url';
}
$onClickAttribute = ' onClick="' . $onClick . '"';

$class = (string) $component->class;
$classAttribute = isset($class{0}) ? ' class="' . htmlentities($class) . '"' : '';

$filename = $app->bearCMS->data->getRealFilename($component->filename);
$content = '<div class="bearcms-image-element">';
if (isset($filename{0})) {
    $content .= '<component src="image-gallery" columnsCount="1"' . $onClickAttribute . $classAttribute . '>';
    $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $filename . '"/>';
    $content .= '</component>';
}
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'image', $content);
?><html>
    <body><?= $content ?></body>
</html>