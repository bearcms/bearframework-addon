<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\BlogPosts;

use BearFramework\App;
use BearCMS\Internal\Config;
use BearCMS\Internal\Data\BlogPosts as InternalBlogPosts;

/**
 * @property string|null $id
 * @property string|null $title
 * @property string|null $slug
 * @property int|null $createdTime
 * @property string|null $status
 * @property string|null $publishedTime
 * @property array $categoriesIDs
 * @property string|null $titleTagContent
 * @property string|null $descriptionTagContent
 * @property string|null $keywordsTagContent
 * @property int|null $lastChangeTime
 * @property string|null $language
 * @property string|null $image
 */
class BlogPost extends \BearFramework\Models\Model
{

    /**
     * 
     */
    function __construct()
    {
        $this
            ->defineProperty('id', [
                'type' => '?string'
            ])
            ->defineProperty('title', [
                'type' => '?string'
            ])
            ->defineProperty('slug', [
                'type' => '?string'
            ])
            ->defineProperty('createdTime', [
                'type' => '?int'
            ])
            ->defineProperty('status', [
                'type' => '?string'
            ])
            ->defineProperty('publishedTime', [
                'type' => '?int'
            ])
            ->defineProperty('categoriesIDs', [
                'type' => 'array'
            ])
            ->defineProperty('titleTagContent', [
                'type' => '?string'
            ])
            ->defineProperty('descriptionTagContent', [
                'type' => '?string'
            ])
            ->defineProperty('keywordsTagContent', [
                'type' => '?string'
            ])
            ->defineProperty('lastChangeTime', [
                'type' => '?int'
            ])
            ->defineProperty('language', [
                'type' => '?string'
            ])
            ->defineProperty('image', [
                'type' => '?string'
            ]);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    public function __modelWakeup(array $data)
    {
        if (isset($data['status'])) {
            if ($data['status'] === 'trashed') {
                $data['status'] = 'private';
            }
        }
        return $data;
    }

    /**
     * 
     * @return string|null
     */
    public function getURLPath(): ?string
    {
        if (strlen((string)$this->id) === 0) {
            return null;
        }
        return InternalBlogPosts::getURLPath($this->id, $this->slug);
    }

    /**
     * 
     * @return string|null
     */
    public function getURL(): ?string
    {
        $path = $this->getURLPath();
        if ($path === null) {
            return null;
        }
        $app = App::get();
        return $app->urls->get($path);
    }
}
