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

$includePost = $component->includePost === 'true';
$forumPostID = $component->forumPostID;
$elementID = 'frl' . md5($forumPostID);
?><html>
    <head>

        <style>
            .bearcms-forum-post-page-reply{
                clear:both;
                min-height:50px;
            }
            .bearcms-forum-post-page-reply-image{
                display:inline-block;
                width:50px;
                height:50px;
                float:left;
            }
        </style>
    </head>
    <body><?php
        echo '<div id="' . $elementID . '">';
        $forumPost = $app->bearCMS->data->forumPosts->get($forumPostID);
        if ($forumPost !== null) {

            $renderItem = function($reply) use ($app) {
                $statusText = '';
                if ($reply->status === 'pendingApproval') {
                    $statusText = __('bearcms.forumPosts.pending approval') . ', ';
                }
                $profile = PublicProfile::getFromAuthor($reply->author);
                $linkAttributes = '';
                if (strlen($profile->url) > 0) {
                    $tagName = 'a';
                    $linkAttributes .= ' href="' . htmlentities($profile->url) . '" target="_blank" rel="nofollow"';
                } else {
                    $tagName = 'span';
                    $linkAttributes .= ' href="javascript:void(0);"';
                }
                $linkAttributes .= ' title="' . htmlentities($profile->name) . '"';
                echo '<div class="bearcms-forum-post-page-reply">';
                echo '<' . $tagName . ' class="bearcms-forum-post-page-reply-author-image"' . $linkAttributes . (strlen($profile->imageSmall) > 0 ? ' style="background-image:url(' . htmlentities($profile->imageSmall) . ');background-size:cover;"' : ' style="background-color:rgba(0,0,0,0.2);"') . '></' . $tagName . '>';
                echo '<' . $tagName . ' class="bearcms-forum-post-page-reply-author-name"' . $linkAttributes . '>' . htmlspecialchars($profile->name) . '</' . $tagName . '> <span class="bearcms-forum-post-page-reply-date">' . $statusText . $app->localization->formatDate($reply->createdTime, ['timeAgo']) . '</span>';
                echo '<div class="bearcms-forum-post-page-reply-text">' . nl2br(htmlspecialchars($reply->text)) . '</div>';
                echo '</div>';
            };

            if ($includePost) {
                $renderItem($forumPost);
            }
            foreach ($forumPost->replies as $reply) {
                $render = false;
                if ($reply->status === 'approved') {
                    $render = true;
                } elseif ($reply->status === 'pendingApproval') {
                    if ($app->currentUser->exists()) {
                        $render = $app->currentUser->provider === $reply->author['provider'] && $app->currentUser->id === $reply->author['id'];
                    }
                }
                if ($render) {
                    $renderItem($reply);
                }
            }
        }
        echo '</div>';
        ?></body>
</html>
