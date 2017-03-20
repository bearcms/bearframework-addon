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

$includePost = $component->includePost === 'true';
$forumPostID = $component->forumPostID;
$elementID = 'frl' . md5($forumPostID);
?><html>
    <head>

        <style>
            .bearcms-forum-post-page-reply{
                clear:both;
            }
            .bearcms-forum-post-page-reply-image{
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

            $renderItem = function($item) {
                $statusText = '';
                if ($item->status === 'pendingApproval') {
                    $statusText = ', pending approval';
                }
                $profile = PublicProfile::getFromAuthor($item->author);
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
                echo '<' . $tagName . ' class="bearcms-forum-post-page-reply-author-name"' . $linkAttributes . '>' . htmlspecialchars($profile->name) . '</' . $tagName . '> <span class="bearcms-forum-post-page-reply-date">' . Localization::getTimeAgo($item->createdTime) . $statusText . '</span>';
                echo '<div class="bearcms-forum-post-page-reply-text">' . nl2br(htmlspecialchars($item->text)) . '</div>';
                echo '</div>';
            };

            if ($includePost) {
                $renderItem($forumPost);
            }
            foreach ($forumPost->replies as $reply) {
                $renderItem($reply);
            }
        }
        echo '</div>';
        ?></body>
</html>
