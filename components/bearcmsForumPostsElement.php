<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$forumCategoryID = '1213123123';

$app = App::get();
$context = $app->context->get(__FILE__);

$posts = $app->bearCMS->data->forumPosts->getList()->filterBy('categoryID', $forumCategoryID);

$content = '';
foreach ($posts as $post) {
    $postUrl = $app->request->base . '/f/' . $post->id . '/' . $post->id . '/';
    $content .= '<div class="bearcms-forum-posts-post">';
    $content .= '<a class="bearcms-forum-posts-post-title" href="' . htmlentities($postUrl) . '">' . htmlspecialchars($post->title) . '</a>';
    $content .= '<div class="bearcms-forum-posts-post-replies-count">3 replies</div>';
    $content .= '</div>';
}
$newPostUrl = $app->request->base . '/f/' . $forumCategoryID . '/';
$content .= '<div class="bearcms-forum-posts-show-more-button-container">';
$loadMoreData = [
        //'serverData' => \BearCMS\Internal\TempClientData::set(['threadID' => 1])
];
$onClick = 'bearCMS.commentsElement.loadMore(event,' . json_encode($loadMoreData) . ');';
$content .= '<a class="bearcms-forum-posts-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.comments.Show more') . '</a>';
$content .= '</div>';
$content .= '<div class="bearcms-forum-posts-new-post-button-container">';
$content .= '<a class="bearcms-forum-posts-new-post-button" href="' . htmlentities($newPostUrl) . '">New post<a>';
$content .= '</div>';
?><html>
    <body><?= $content ?></body>
</html>