<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;
use \BearCMS\Internal\PublicProfile;

$app = App::get();

$count = (int) $component->count;
$threadID = $component->threadID;
$elementID = 'cml' . md5($threadID);

echo '<div id="' . $elementID . '" data-count="' . $count . '">';
$thread = $app->bearCMS->data->commentsThreads->get($threadID);
if ($thread !== null) {
    $thread->comments->filter(function($comment) use ($app) {
        if ($comment->status === 'approved') {
            return true;
        }
        if ($comment->status === 'pendingApproval') {
            if ($app->currentUser->exists()) {
                return $app->currentUser->provider === $comment->author['provider'] && $app->currentUser->id === $comment->author['id'];
            }
        }
        return false;
    })->sortBy('createdTime', 'asc');
    $startIndex = $thread->comments->length - $count;
    if ($startIndex < 0) {
        $startIndex = 0;
    }
    if ($startIndex > 0) {
        echo '<div class="bearcms-comments-show-more-button-container">';
        $loadMoreData = [
            'serverData' => \BearCMS\Internal\TempClientData::set(['threadID' => $threadID])
        ];
        $onClick = 'bearCMS.commentsElement.loadMore(event,' . json_encode($loadMoreData) . ');';
        echo '<a class="bearcms-comments-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.comments.Show more') . '</a>';
        echo '</div>';
    }

    $result = $thread->comments->slice($startIndex);
    foreach ($result as $comment) {
        $statusText = '';
        if ($comment->status === 'pendingApproval') {
            $statusText = __('bearcms.comments.pending approval') . ', ';
        }
        $profile = PublicProfile::getFromAuthor($comment->author);
        $linkAttributes = '';
        if (strlen($profile->url) > 0) {
            $tagName = 'a';
            $linkAttributes .= ' href="' . htmlentities($profile->url) . '" target="_blank" rel="nofollow"';
        } else {
            $tagName = 'span';
            $linkAttributes .= ' href="javascript:void(0);"';
        }
        $linkAttributes .= ' title="' . htmlentities($profile->name) . '"';
        echo '<div class="bearcms-comments-comment">';
        echo '<' . $tagName . ' class="bearcms-comments-comment-author-image"' . $linkAttributes . (strlen($profile->imageSmall) > 0 ? ' style="background-image:url(' . htmlentities($profile->imageSmall) . ');background-size:cover;"' : ' style="background-color:rgba(0,0,0,0.2);"') . '></' . $tagName . '>';
        echo '<' . $tagName . ' class="bearcms-comments-comment-author-name"' . $linkAttributes . '>' . htmlspecialchars($profile->name) . '</' . $tagName . '> <span class="bearcms-comments-comment-date">' . $statusText . $app->localization->formatDate($comment->createdTime, ['timeAgo']) . '</span>';
        echo '<div class="bearcms-comments-comment-text">' . nl2br(htmlspecialchars($comment->text)) . '</div>';
        echo '</div>';
    }
}
echo '</div>';
