<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal2;

class Page extends \BearCMS\DataObject
{

    function __construct(array $data = [])
    {
        $this
                ->defineProperty('children', [
                    'get' => function() {
                        $app = App::get();
                        return Internal2::$data2->pages->getList()
                                ->filterBy('parentID', $this->id);
                    }
        ]);
        parent::__construct($data);
    }

}

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
 * @property-read \BearCMS\DataList|\BearCMS\Internal\Data2\Page[] $children
 */
//class Page
//{
//
//    use \BearFramework\Models\ModelTrait;
//
//    function __construct()
//    {
//        $this
//                ->defineProperty('id', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('name', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('parentID', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('status', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('slug', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('path', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('titleTagContent', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('descriptionTagContent', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('keywordsTagContent', [
//                    'type' => '?string'
//                ])
//                ->defineProperty('children', [
//                    'readonly' => true,
//                    'init' => function() {
//                        $app = App::get();
//                        return Internal2::$data2->pages->getList()
//                                ->filterBy('parentID', $this->id);
//                    }
//                ])
//        ;
//    }
//
//}
