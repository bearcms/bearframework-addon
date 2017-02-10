<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the forum replies
 */
class ForumPostsReplies
{

    /**
     * Retrieves a list of all forum replies
     * 
     * @return \BearCMS\DataList List containing all forum replies data
     */
    public function getList()
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/forum/posts/post/', 'startWith');

        $result = new \BearCMS\DataList();
        foreach ($list as $item) {
            $rawData = json_decode($item->value, true);
            if (isset($rawData['id'], $rawData['replies'])) {
                foreach ($rawData['replies'] as $replyData) {
                    $reply = new \BearCMS\Data\ForumPostReply();
                    $reply->id = $replyData['id'];
                    $reply->status = $replyData['status'];
                    $reply->author = $replyData['author'];
                    $reply->text = $replyData['text'];
                    $reply->createdTime = $replyData['createdTime'];
                    $reply->forumPostID = $rawData['id'];
                    $result[] = $reply;
                }
            }
        }
        return $result;
    }

}
