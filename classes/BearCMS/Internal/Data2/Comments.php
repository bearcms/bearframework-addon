<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;

/**
 * Information about the comments
 */
class Comments
{

    /**
     * Retrieves a list of all comments
     * 
     * @return \BearCMS\DataList|\BearCMS\Internal\Data2\Comment[] List containing all comments data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/comments/thread/');
        $result = new \BearCMS\DataList();
        foreach ($list as $value) {
            $rawData = json_decode($value, true);
            $tempCollection = \BearCMS\Internal\Data\Comments::createCommentsCollection($rawData['comments'], $rawData['id']);
            foreach ($tempCollection as $dataObject) {
                $result[] = $dataObject;
            }
        }
        return $result;
    }

}
