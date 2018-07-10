<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the comments threads
 */
class CommentsThreads
{

    private function makeCommentsThreadPostFromRawData($rawData): \BearCMS\Data\CommentsThread
    {
        $rawData = json_decode($rawData, true);
        $object = new \BearCMS\Data\CommentsThread($rawData);
        $object->comments = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
        return $object;
    }

    /**
     * Retrieves information about the comments thread specified
     * 
     * @param string $id The comments thread ID
     * @return \BearCMS\DataObject|null The comments thread data or null if the thread not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Data\CommentsThread
    {
        $data = \BearCMS\Internal\Data::getValue('bearcms/comments/thread/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeCommentsThreadPostFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all comments threads
     * 
     * @return \BearCMS\DataList List containing all comments threads data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/comments/thread/');
        array_walk($list, function(&$value) {
            $value = $this->makeCommentsThreadPostFromRawData($value);
        });
        return new \BearCMS\DataList($list);
    }

}
