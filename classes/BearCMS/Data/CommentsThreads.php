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
        $app = App::get();
        $data = $app->data->getValue('bearcms/comments/thread/' . md5($id) . '.json');
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
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/comments/thread/', 'startWith');
        $result = [];
        foreach ($list as $item) {
            $result[] = $this->makeCommentsThreadPostFromRawData($item->value);
        }
        return new \BearCMS\DataList($result);
    }

}
