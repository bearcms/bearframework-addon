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

}
