<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();
$context = $app->contexts->get(__DIR__);

$count = strlen($component->count) > 0 ? (int) $component->count : 5;
if ($count < 1) {
    $count = 1;
}
$threadID = $component->threadID;
$content = '';
if (strlen($threadID) > 0) {
    $content .= '<div class="bearcms-comments-element">';
    $content .= '<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" />';
    $content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsCommentsElement/commentsForm.php" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" />';
    $content .= '</div>';
}
echo '<html><head><style>';
echo '.bearcms-comments-comment{display:block;clear:both;zoom:1;word-break:break-word;}';
echo '.bearcms-comments-comment:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
echo '.bearcms-comments-comment-author-image{display:inline-block;float:left;}';
echo '.bearcms-comments-comment-date{float:right;}';
echo '</style></head><body>';
echo $content;
echo '</body></html>';
