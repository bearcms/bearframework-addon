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

    /**
     * Retrieves information about the comments thread specified
     * 
     * @param string $id The comments thread ID
     * @return \BearCMS\DataObject|null The comments thread data or null if the thread not found
     * @throws \InvalidArgumentException
     */
    public function getThread($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('The id agrument must be of type string');
        }
        $app = App::get();
        $data = $app->data->get(
                [
                    'key' => 'bearcms/comments/thread/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            $rawData = json_decode($data['body'], true);
            if (isset($rawData['id'], $rawData['comments'])) {
                $dataObject = new \BearCMS\DataObject($rawData);
                $dataObject->comments = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
                return $dataObject;
            }
        }
        return null;
    }

    /**
     * Retrieves a list of all comments threads
     * 
     * @return \BearCMS\DataCollection List containing all comments threads data
     */
    public function getList()
    {
        $app = App::get();
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/comments/thread/', 'startsWith']
                    ],
                    'result' => ['body']
                ]
        );
        $result = [];
        foreach ($data as $item) {
            $rawData = json_decode($item['body'], true);
            if (isset($rawData['id'], $rawData['comments'])) {
                $dataObject = new \BearCMS\DataObject($rawData);
                $dataObject->comments = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
                $result[] = $dataObject;
            }
        }
        return new \BearCMS\DataCollection($result);
    }

}
