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
 * Information about the forum posts
 */
class ForumPosts
{

    private function makeForumPostFromRawData($rawData): \BearCMS\Data\ForumPost
    {
        $rawData = json_decode($rawData, true);
        $user = new \BearCMS\Data\ForumPost();
        $properties = ['id', 'status', 'author', 'title', 'text', 'categoryID', 'createdTime', 'replies'];
        foreach ($properties as $property) {
            if ($property === 'replies') {
                $temp = new \BearCMS\DataList();
                if (isset($rawData['replies'])) {
                    foreach ($rawData['replies'] as $replyData) {
                        $reply = new \BearCMS\Data\ForumPostReply();
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
     * @return \BearCMS\DataObject|null The forum post data or null if the thread not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Data\ForumPost
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/forum/posts/post/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeForumPostFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all forum posts
     * 
     * @return \BearCMS\DataList|\BearCMS\Data\ForumPost[] List containing all forum posts data
     */
    public function getList()
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/forum/posts/post/', 'startWith');
        $result = new \BearCMS\DataList();
        foreach ($list as $item) {
            $result[] = $this->makeForumPostFromRawData($item->value);
        }
        return $result;
    }

}
