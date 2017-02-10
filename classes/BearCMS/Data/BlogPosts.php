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
 * Information about the blog posts
 */
class BlogPosts
{

    private static $listDataCache = null;

    private function makeBlogPostFromRawData($rawData): \BearCMS\Data\BlogPost
    {
        return new \BearCMS\Data\BlogPost(json_decode($rawData, true));
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
        $app = App::get();
        $data = $app->data->getValue('bearcms/blog/post/' . md5($id) . '.json');
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
        $app = App::get();
        if (self::$listDataCache === null) {
            $list = $app->data->getList()
                    ->filterBy('key', 'bearcms/blog/post/', 'startWith');
            self::$listDataCache = [];
            foreach ($list as $item) {
                self::$listDataCache[] = $this->makeBlogPostFromRawData($item->value);
            }
        }
        return new \BearCMS\DataList(self::$listDataCache);
    }

}
