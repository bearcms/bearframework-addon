<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

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
    $content .= '<script id="bearcms-bearframework-addon-script-5" src="' . htmlentities($context->assets->getUrl('components/bearcmsCommentsElement/assets/commentsElement.min.js', ['cacheMaxAge' => 999999999, 'version' => 4])) . '" async></script>';
    $content .= '<script id="bearcms-bearframework-addon-script-4" src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async></script>';
    $content .= '</div>';
}
?><html>
    <head>
        <style>
            .bearcms-comments-comment{
                clear:both;
                min-height:50px;
            }
            .bearcms-comments-comment-image{
                display:inline-block;
                width:50px;
                height:50px;
                float:left;
            }
        </style>
    </head>
    <body><?= $content ?></body>
</html>