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
 * Information about the blog posts
 */
class BlogPosts
{

    private function makeBlogPostFromRawData($rawData): \BearCMS\Internal\Data2\BlogPost
    {
        return new \BearCMS\Internal\Data2\BlogPost(json_decode($rawData, true));
    }

    /**
     * Retrieves information about the blog post specified
     * 
     * @param string $id The blog post ID
     * @return \BearCMS\DataObject|null The blog post data or null if blog post not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id)
    {
        $data = \BearCMS\Internal\Data::getValue('bearcms/blog/post/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeBlogPostFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all blog posts
     * 
     * @return \BearCMS\DataList List containing all blog posts data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/blog/post/');
        array_walk($list, function(&$value) {
            $value = $this->makeBlogPostFromRawData($value);
        });
        return new \BearCMS\DataList($list);
    }

}
