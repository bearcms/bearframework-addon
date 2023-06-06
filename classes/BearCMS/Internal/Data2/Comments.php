<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearCMS\Internal;
use BearCMS\Internal2;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Comments
{

    /**
     * Retrieves a list of all comments
     * 
     * @return \IvoPetkov\DataList|\BearCMS\Internal\Data2\Comment[] List containing all comments data
     */
    public function getList()
    {
        $list = Internal\Data::getList('bearcms/comments/thread/');
        $result = new \IvoPetkov\DataList();
        foreach ($list as $value) {
            $rawData = json_decode($value, true);
            $tempCollection = Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
            foreach ($tempCollection as $dataObject) {
                $result[] = $dataObject;
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $threadID
     * @param string $commentID
     * @return \BearCMS\Internal\Data2\Comment|null
     */
    public function get(string $threadID, string $commentID): ?\BearCMS\Internal\Data2\Comment
    {
        $thread = Internal2::$data2->commentsThreads->get($threadID);
        foreach ($thread->comments as $comment) {
            if ($comment->id === $commentID) {
                return $comment;
            }
        }
        return null;
    }
}
