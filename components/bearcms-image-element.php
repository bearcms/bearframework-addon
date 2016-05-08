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

$filename = $component->filename;
if (substr($filename, 0, 5) === 'data:') {
    $filename = $app->data->getFilename(substr($filename, 5));
} elseif (substr($filename, 0, 4) === 'app:') {
    $filename = $app->config->appDir . DIRECTORY_SEPARATOR . substr($filename, 4);
} elseif (substr($filename, 0, 6) === 'addon:') {
    $temp = explode(':', $filename, 3);
    if (sizeof($temp) === 3) {
        $addonDir = \BearFramework\Addons::getDir($temp[1]);
        $filename = $addonDir . DIRECTORY_SEPARATOR . $temp[2];
    }
}

$content = '<div class="bearcms-image-element">';
if (isset($filename{0})) {
    $content .= '<component src="image-gallery" columnsCount="1" onClick="' . $onClick . '">';
    $content .= '<file class="bearcms-image-element-image"' . ($onClick === 'url' ? ' url="' . htmlentities($component->url) . '"' : '') . ' title="' . htmlentities($component->title) . '" filename="' . $filename . '"/>';
    $content .= '</component>';
}
$content .= '</div>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'image', $content);
?><html>
    <body><?= $content ?></body>
</html>