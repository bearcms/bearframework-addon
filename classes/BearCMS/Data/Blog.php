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
class Blog
{

    /**
     * Retrieves information about the blog post specified
     * 
     * @param string $id The blog post ID
     * @return \BearCMS\DataObject|null The blog post data or null if blog post not found
     * @throws \InvalidArgumentException
     */
    public function getPost($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('The id agrument must be of type string');
        }
        $app = App::get();
        $data = $app->data->get(
                [
                    'key' => 'bearcms/blog/post/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return new \BearCMS\DataObject(json_decode($data['body'], true));
        }
        return null;
    }

    /**
     * Retrieves a list of all blog posts
     * 
     * @return \BearCMS\DataCollection List containing all blog posts data
     */
    public function getList()
    {
        $app = App::get();
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/blog/post/', 'startsWith']
                    ],
                    'result' => ['body']
                ]
        );
        $result = [];
        foreach ($data as $item) {
            $result[] = new \BearCMS\DataObject(json_decode($item['body'], true));
        }
        return new \BearCMS\DataCollection($result);
    }

}
