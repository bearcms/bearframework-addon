<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;

/**
 * 
 */
class BlogPosts
{

    /**
     * 
     * @param string $id
     * @return \BearCMS\Data\BlogPosts\BlogPost|null
     */
    public function get(string $id): ?\BearCMS\Data\BlogPosts\BlogPost
    {
        $data = Internal\Data::getValue('bearcms/blog/post/' . md5($id) . '.json');
        if ($data !== null) {
            return \BearCMS\Data\BlogPosts\BlogPost::fromJSON($data);
        }
        return null;
    }

    /**
     * 
     * @return \BearFramework\Models\ModelsList
     */
    public function getList(): \BearFramework\Models\ModelsList
    {
        $list = Internal\Data::getList('bearcms/blog/post/');
        array_walk($list, function(&$value) {
            $value = \BearCMS\Data\BlogPosts\BlogPost::fromJSON($value);
        });
        return new \BearFramework\Models\ModelsList($list);
    }

}
