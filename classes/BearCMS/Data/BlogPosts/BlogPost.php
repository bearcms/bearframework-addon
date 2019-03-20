<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\BlogPosts;

/**
 * @property string|null $id
 * @property string|null $title
 * @property string|null $slug
 * @property int|null $createdTime
 * @property string|null $status
 * @property string|null $publishedTime
 * @property string|null $trashedTime
 * @property array $categoriesIDs
 * @property string|null $titleTagContent
 * @property string|null $descriptionTagContent
 * @property string|null $keywordsTagContent
 * @property int|null $lastChangeTime
 */
class BlogPost extends \BearFramework\Models\Model
{

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
                ->defineProperty('trashedTime', [
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
        ;
    }

}
