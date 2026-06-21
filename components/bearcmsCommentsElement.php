<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\ComponentUtilities;
use BearFramework\App;

$app = App::get();
$context = $app->contexts->get(__DIR__);

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
$isFullHtmlOutputType = $outputType === 'full-html';

$componentCount = (string)$component->count;
$count = strlen($componentCount) > 0 ? (int) $componentCount : 5;
if ($count < 1) {
    $count = 1;
}
$threadID = (string)$component->threadID;
$allowFilesUpload = (string)$component->allowFilesUpload === 'true';
$content = '';
if (strlen($threadID) > 0) {
    $content .= '<div' . ($isFullHtmlOutputType ? ' class="bearcms-comments-element"' : '') . '>';
    $content .= '<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" output-type="' . $outputType . '" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" />';
    if ($isFullHtmlOutputType) {
        $content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsCommentsElement/commentsForm.php" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" ' . ($allowFilesUpload ? 'allowFilesUpload="true"' : '') . '/>';
    }
    $content .= '</div>';
}
echo '<html><head>';
if ($isFullHtmlOutputType) {
    $styleHTML = '<style>'
        . '.bearcms-comments-comment{display:block;clear:both;zoom:1;word-break:break-word;}'
        . '.bearcms-comments-comment:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}'
        . '.bearcms-comments-comment-author-image{display:inline-block;float:left;}'
        . '.bearcms-comments-comment-date{float:right;}'
        . '</style>';
    echo ComponentUtilities::createComponentFragment('bcme', $styleHTML);
}
echo '</head><body>';
echo $content;
echo '</body></html>';
