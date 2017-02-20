<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class BlogPosts
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getSlugsList(string $status = 'all'): array
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/blog/post/', 'startWith');

        $result = [];
        foreach ($list as $item) {
            $blogPostData = json_decode($item->value, true);
            if (
                    is_array($blogPostData) &&
                    isset($blogPostData['id']) &&
                    isset($blogPostData['slug']) &&
                    isset($blogPostData['status']) &&
                    is_string($blogPostData['id']) &&
                    is_string($blogPostData['slug']) &&
                    is_string($blogPostData['status'])
            ) {
                if ($status !== 'all' && $status !== $blogPostData['status']) {
                    continue;
                }
                $result[$blogPostData['id']] = $blogPostData['slug'];
            }
        }
        return $result;
    }

}