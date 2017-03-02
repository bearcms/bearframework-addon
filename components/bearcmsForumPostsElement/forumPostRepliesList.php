<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;
use \BearCMS\Internal\Localization;
use \BearCMS\Internal\PublicProfile;

$app = App::get();

$forumPostID = $component->forumPostID;
$elementID = 'frl' . md5($forumPostID);
?><html>
    <head>

        <style>
            .bearcms-forum-post-reply{
                clear:both;
            }
            .bearcms-forum-post-reply-image{
                display:inline-block;
                width:50px;
                height:50px;
                float:left;
                margin-right:10px;
                margin-bottom:10px;
            }
        </style>
    </head>
    <body><?php
        echo '<div id="' . $elementID . '">';
        $forumPost = $app->bearCMS->data->forumPosts->get($forumPostID);
        if ($forumPost !== null) {
            foreach ($forumPost->replies as $reply) {
                $statusText = '';
                if ($reply->status === 'pendingApproval') {
                    $statusText = ', pending approval';
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
                echo '<div class="bearcms-forum-post-reply">';
                echo '<' . $tagName . ' class="bearcms-forum-post-reply-image"' . $linkAttributes . (strlen($profile->imageSmall) > 0 ? ' style="background-image:url(' . htmlentities($profile->imageSmall) . ');background-size:cover;"' : ' style="background-color:rgba(0,0,0,0.2);"') . '></' . $tagName . '>';
                echo '<' . $tagName . ' class="bearcms-forum-post-reply-name"' . $linkAttributes . '>' . htmlspecialchars($profile->name) . '</' . $tagName . '> <span class="bearcms-forum-post-reply-date">' . Localization::getTimeAgo($reply->createdTime) . $statusText . '</span>';
                echo '<div class="bearcms-forum-post-reply-content">' . nl2br(htmlspecialchars($reply->text)) . '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
        ?></body>
</html>
