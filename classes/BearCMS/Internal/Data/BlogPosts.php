<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearCMS\Internal\ElementsHelper;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class BlogPosts
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getSlugsList(string $status = 'all'): array
    {
        $list = Internal\Data::getList('bearcms/blog/post/');
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

    static function getDataKey(string $id)
    {
        return 'bearcms/blog/post/' . md5($id) . '.json';
    }

    static function getLastModifiedDetails(string $blogPostID)
    {
        $app = App::get();
        $details = ElementsHelper::getLastModifiedDetails('bearcms-blogpost-' . $blogPostID);
        $details['dataKeys'][] = self::getDataKey($blogPostID);
        $blogPost = $app->bearCMS->data->blogPosts->get($blogPostID);
        if ($blogPost !== null) {
            $details['dates'][] = $blogPost->lastChangeTime;
        }
        return $details;
    }
}
