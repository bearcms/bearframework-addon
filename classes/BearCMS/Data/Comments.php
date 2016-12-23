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
 * Information about the comments
 */
class Comments
{

    /**
     * Retrieves a list of all comments
     * 
     * @return \BearCMS\DataCollection List containing all comments data
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
        $result = new \BearCMS\DataCollection();
        foreach ($data as $item) {
            $rawData = json_decode($item['body'], true);
            if (isset($rawData['id'], $rawData['comments'])) {
                $tempCollection = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
                foreach ($tempCollection as $dataObject) {
                    $result[] = $dataObject;
                }
            }
        }
        return $result;
    }

}
