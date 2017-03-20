<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
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

$posts = $app->bearCMS->data->forumPosts->getList()->filterBy('categoryID', $categoryID);
$counter = 0;
echo '<div>';
foreach ($posts as $post) {
    $postUrl = $app->request->base . '/f/' . $post->id . '/' . $post->id . '/';
    $repliesCount = $post->replies->length;
    echo '<div class="bearcms-forum-posts-post">';
    echo '<a class="bearcms-forum-posts-post-title" href="' . htmlentities($postUrl) . '">' . htmlspecialchars($post->title) . '</a>';
    echo '<div class="bearcms-forum-posts-post-replies-count">' . ($repliesCount === 1 ? __('bearcms.forumPosts.1 reply') : sprintf(__('bearcms.forumPosts.%s replies'), $repliesCount)) . '</div>';
    echo '</div>';
    $counter++;
    if ($counter >= $count) {
        break;
    }
}
if ($count < $posts->length) {
    echo '<div class="bearcms-forum-posts-show-more-button-container">';
    $component = '<component src="file:' . $context->dir . '/components/bearcmsForumPostsElement/forumPostsList.php" count="' . htmlentities($count + 10) . '" categoryID="' . htmlentities($categoryID) . '" />';
    $loadMoreData = [
        'serverData' => \BearCMS\Internal\TempClientData::set(['componentHTML' => $component])
    ];
    $onClick = 'bearCMS.forumPostsElement.loadMore(event,' . json_encode($loadMoreData) . ');';
    echo '<a class="bearcms-forum-posts-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.forumPosts.Show more') . '</a>';
    echo '</div>';
}
echo '</div>';
