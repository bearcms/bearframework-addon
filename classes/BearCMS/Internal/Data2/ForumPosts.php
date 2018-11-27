<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearCMS\Internal;

/**
 * @internal
 */
class ForumPosts
{

    private function makeForumPostFromRawData($rawData): \BearCMS\Internal\Data2\ForumPost
    {
        $rawData = json_decode($rawData, true);
        $user = new Internal\Data2\ForumPost();
        $properties = ['id', 'status', 'author', 'title', 'text', 'categoryID', 'createdTime', 'replies'];
        foreach ($properties as $property) {
            if ($property === 'replies') {
                $temp = new Internal\DataList();
                if (isset($rawData['replies'])) {
                    foreach ($rawData['replies'] as $replyData) {
                        $reply = new Internal\Data2\ForumPostReply();
                        $reply->id = $replyData['id'];
                        $reply->status = $replyData['status'];
                        $reply->author = $replyData['author'];
                        $reply->text = $replyData['text'];
                        $reply->createdTime = $replyData['createdTime'];
                        $temp[] = $reply;
                    }
                }
                $user->replies = $temp;
            } elseif (array_key_exists($property, $rawData)) {
                $user->$property = $rawData[$property];
            }
        }
        return $user;
    }

    /**
     * Retrieves information about the forum post specified
     * 
     * @param string $id The forum post ID
     * @return \BearCMS\Internal\DataObject|null The forum post data or null if the thread not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\ForumPost
    {
        $data = Internal\Data::getValue('bearcms/forums/posts/post/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeForumPostFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all forum posts
     * 
     * @return \BearCMS\Internal\DataList|\BearCMS\Internal\Data2\ForumPost[] List containing all forum posts data
     */
    public function getList()
    {
        $list = Internal\Data::getList('bearcms/forums/posts/post/');
        array_walk($list, function(&$value) {
            $value = $this->makeForumPostFromRawData($value);
        });
        return new Internal\DataList($list);
    }

}
