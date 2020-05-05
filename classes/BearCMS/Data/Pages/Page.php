<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\Pages;

/**
 * @property string|null $id
 * @property string|null $name
 * @property string|null $parentID
 * @property string|null $status
 * @property string|null $slug
 * @property string|null $path
 * @property string|null $titleTagContent
 * @property string|null $descriptionTagContent
 * @property string|null $keywordsTagContent
 * @property-read \BearFramework\Models\ModelsList|\BearCMS\Data\Pages\Page[] $children
 * @property int|null $lastChangeTime
 */
class Page extends \BearFramework\Models\Model
{

    function __construct()
    {
        $this
            ->defineProperty('id', [
                'type' => '?string'
            ])
            ->defineProperty('name', [
                'type' => '?string'
            ])
            ->defineProperty('parentID', [
                'type' => '?string',
                'set' => function ($value) {
                    if ($value === '') {
                        return null;
                    }
                    return $value;
                }
            ])
            ->defineProperty('status', [
                'type' => '?string'
            ])
            ->defineProperty('slug', [
                'type' => '?string'
            ])
            ->defineProperty('path', [
                'type' => '?string'
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
            ->defineProperty('children', [
                'readonly' => true,
                'init' => function () {
                    if ($this->id === null) {
                        return [];
                    }
                    return \BearCMS\Internal\Data\Pages::getChildrenList($this->id);
                }
            ])
            ->defineProperty('lastChangeTime', [
                'type' => '?int'
            ]);
    }

    public function __modelWakeup(array $data)
    {
        if (isset($data['status'])) {
            if ($data['status'] === 'published') {
                $data['status'] = 'public';
            } elseif ($data['status'] === 'notPublished') {
                $data['status'] = 'private';
            }
        }
        return $data;
    }
}
