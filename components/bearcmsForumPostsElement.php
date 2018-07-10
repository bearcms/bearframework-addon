<?php
/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

$count = strlen($component->count) > 0 ? (int) $component->count : 5;
if ($count < 1) {
    $count = 1;
}
$categoryID = $component->categoryID;

$content = '';
$content .= '<component src="file:' . $context->dir . '/components/bearcmsForumPostsElement/forumPostsList.php" count="' . htmlentities($count) . '" categoryID="' . htmlentities($categoryID) . '" />';
$content .= '<script id="bearcms-bearframework-addon-script-9" src="' . htmlentities($context->assets->getUrl('components/bearcmsForumPostsElement/assets/forumPostsElement.min.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async></script>';
$content .= '<script id="bearcms-bearframework-addon-script-4" src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async></script>';

$newPostUrl = $app->request->base . '/f/' . $categoryID . '/';
$content .= '<div class="bearcms-forum-posts-new-post-button-container">';
$content .= '<a class="bearcms-forum-posts-new-post-button" href="' . htmlentities($newPostUrl) . '">' . __('bearcms.forumPosts.New post') . '<a>';
$content .= '</div>';
?><html>
    <body><?= $content ?></body>
</html>