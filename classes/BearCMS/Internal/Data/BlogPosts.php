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
        $list = \BearCMS\Internal\Data::getList('bearcms/blog/post/');
        $result = [];
        foreach ($list as $value) {
            $blogPostData = json_decode($value, true);
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
