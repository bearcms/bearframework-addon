<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */
namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Blog
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getSlugsList($status = 'all')
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
            if (isset($item['body']) && is_string($item['body'])) {
                $blogPostData = json_decode($item['body'], true);
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
        }
        return $result;
    }

}
