<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal;
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
                $postStatus = $blogPostData['status'];
                if ($postStatus === 'trashed') {
                    $postStatus = 'private';
                }
                if ($status !== 'all' && $status !== $postStatus) {
                    continue;
                }
                $result[$blogPostData['id']] = $blogPostData['slug'];
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $blogPostID
     * @return string
     */
    static private function getDataKey(string $blogPostID): string
    {
        return 'bearcms/blog/post/' . md5($blogPostID) . '.json';
    }

    /**
     * 
     * @param string $blogPostID
     * @return array|null
     */
    static function get(string $blogPostID): ?array
    {
        $data = Internal\Data::getValue(self::getDataKey($blogPostID));
        if ($data !== null) {
            return json_decode($data, true);
        }
        return null;
    }

    /**
     * 
     * @param string $blogPostID
     * @param array $data
     * @return void
     */
    static function set(string $blogPostID, array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getDataKey($blogPostID), json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * 
     * @param string $blogPostID
     * @return void
     */
    static function delete(string $blogPostID): void
    {
        $app = App::get();
        $app->data->delete(self::getDataKey($blogPostID));
    }

    /**
     * 
     * @param string $blogPostID
     * @return void
     */
    static function deleteImage(string $blogPostID, bool $updateData): void
    {
        $data = self::get($blogPostID);
        if ($data !== null) {
            $filename = isset($data['image']) ? (string)$data['image'] : '';
            if (strlen($filename) > 0) {
                $app = App::get();
                $dataKey = Internal\Data::filenameToDataKey($filename);
                $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                UploadsSize::remove($dataKey);
            }
            if ($updateData) {
                $data['image'] = null;
                self::set($blogPostID, $data);
            }
        }
    }
}
