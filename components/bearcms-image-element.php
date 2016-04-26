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
$content = '<div class="bearcms-image-element">';
$content .= '<component src="image-gallery" columnsCount="1" onClick="' . $onClick . '">';
$content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $app->data->getFilename('bearcms/files/image/' . $component->filename) . '"/>';
$content .= '</component>';
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'image', $content);
?><html>
    <body><?= $content ?></body>
</html>