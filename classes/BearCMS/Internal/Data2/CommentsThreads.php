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
 * @codeCoverageIgnore
 */
class CommentsThreads
{

    private function makeCommentsThreadPostFromRawData($rawData): \BearCMS\Internal\Data2\CommentsThread
    {
        $rawData = json_decode($rawData, true);
        $object = new Internal\Data2\CommentsThread($rawData);
        $object->comments = Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
        return $object;
    }

    /**
     * Retrieves information about the comments thread specified
     * 
     * @param string $id The comments thread ID
     * @return \IvoPetkov\DataObject|null The comments thread data or null if the thread not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\CommentsThread
    {
        $data = Internal\Data::getValue('bearcms/comments/thread/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeCommentsThreadPostFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all comments threads
     * 
     * @return \IvoPetkov\DataList List containing all comments threads data
     */
    public function getList()
    {
        $list = Internal\Data::getList('bearcms/comments/thread/');
        array_walk($list, function(&$value) {
            $value = $this->makeCommentsThreadPostFromRawData($value);
        });
        return new \IvoPetkov\DataList($list);
    }

}
