<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal2;

$app = App::get();

$count = (int) $component->count;
$threadID = $component->threadID;
$elementID = 'cml' . md5($threadID);

echo '<html>';
echo '<head><link rel="client-packages-embed" name="-bearcms-comments-element-list"></head>';
echo '<body>';
echo '<div id="' . $elementID . '" data-count="' . $count . '">';
$thread = Internal2::$data2->commentsThreads->get($threadID);
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
    $startIndex = $thread->comments->count() - $count;
    if ($startIndex < 0) {
        $startIndex = 0;
    }
    if ($startIndex > 0) {
        echo '<div class="bearcms-comments-show-more-button-container">';
        $loadMoreData = [
            'serverData' => \BearCMS\Internal\TempClientData::set(['threadID' => $threadID])
        ];
        $onClick = 'bearCMS.commentsElementList.loadMore(this,' . json_encode($loadMoreData) . ');';
        echo '<a class="bearcms-comments-show-more-button" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '">' . __('bearcms.comments.Show more') . '</a>';
        echo '</div>';
    }

    $urlsToHTML = function($text) {
        $letters = 'абвгдежзийклмнопрстуфхчцшщьъюяАБВГДЕЖЗИЙКЛМНОПРСТУФХЧЦШЩЬЪЮЯ';
        $exp = '/(http|https|ftp|ftps)\:\/\/[' . $letters . 'a-zA-Z0-9\-\.]+\.[' . $letters . 'a-zA-Z]+[^\s]*/';
        $matches = null;
        preg_match_all($exp, $text, $matches);
        if (empty($matches[0])) {
            return $text;
        }
        $parts = [];
        foreach ($matches[0] as $i => $url) {
            $matches[0][$i] = rtrim($url, '.,?!');
        }
        $tempText = $text;
        foreach ($matches[0] as $url) {
            $temp = explode($url, $tempText, 2);
            $parts[] = $temp[0];
            $tempText = $temp[1];
        }
        $parts[] = $temp[1];
        $newTextParts = [];
        foreach ($parts as $i => $part) {
            $newTextParts[] = $part;
            if (isset($matches[0][$i])) {
                $newTextParts[] = '<a href="' . htmlentities($matches[0][$i]) . '" rel="nofollow noreferrer noopener">' . $matches[0][$i] . '</a>';
            }
        }
        return implode('', $newTextParts);
    };

    $result = $thread->comments->slice($startIndex);
    foreach ($result as $comment) {
        $statusText = '';
        if ($comment->status === 'pendingApproval') {
            $statusText = __('bearcms.comments.pending approval') . ', ';
        }
        $author = $comment->author;
        $profile = Internal\PublicProfile::getFromAuthor($author);

        $onClick = 'bearCMS.commentsElementList.previewUser("' . $author['provider'] . '","' . $author['id'] . '");';
        $linkAttributes = ' title="' . htmlentities($profile->name) . '" href="javascript:void(0);" onclick="' . htmlentities($onClick) . '"';
        echo '<div class="bearcms-comments-comment">';
        echo '<a class="bearcms-comments-comment-author-image"' . $linkAttributes . (strlen($profile->imageSmall) > 0 ? ' style="background-image:url(' . htmlentities($profile->imageSmall) . ');background-size:cover;"' : ' style="background-color:rgba(0,0,0,0.2);"') . '></a>';
        echo '<a class="bearcms-comments-comment-author-name"' . $linkAttributes . '>' . htmlspecialchars($profile->name) . '</a> <span class="bearcms-comments-comment-date">' . $statusText . $app->localization->formatDate($comment->createdTime, ['timeAgo']) . '</span>';
        echo '<div class="bearcms-comments-comment-text">' . nl2br($urlsToHTML(htmlspecialchars($comment->text))) . '</div>';
        echo '</div>';
    }
}
echo '</div>';
echo '</body>';
echo '</html>';
