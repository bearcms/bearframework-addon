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
     * @return \BearCMS\DataList|\BearCMS\Data\Comment[] List containing all comments data
     */
    public function getList()
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/comments/thread/', 'startWith');
        $result = new \BearCMS\DataList();
        foreach ($list as $item) {
            $rawData = json_decode($item->value, true);
            $tempCollection = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
            foreach ($tempCollection as $dataObject) {
                $result[] = $dataObject;
            }
        }
        return $result;
    }

}
