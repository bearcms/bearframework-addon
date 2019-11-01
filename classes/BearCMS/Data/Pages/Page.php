<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\Pages;

use BearFramework\App;

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
                'type' => '?string'
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
                    $app = App::get();
                    return $app->bearCMS->data->pages->getList()
                        ->filterBy('parentID', $this->id);
                }
            ])
            ->defineProperty('lastChangeTime', [
                'type' => '?int'
            ]);
    }

    static function fromJSON(string $data)
    {
        $data = json_decode($data, true);
        if (isset($data['parentID']) && strlen($data['parentID']) === 0) {
            $data['parentID'] = null;
        }
        $data = json_encode($data);
        return parent::fromJSON($data);
    }
}
