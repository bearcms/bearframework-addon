<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

class Page extends \BearCMS\DataObject
{

    function __construct(array $data = [])
    {
        $this
                ->defineProperty('children', [
                    'get' => function() {
                        $app = App::get();
                        return $app->bearCMS->data->pages->getList()
                                ->filterBy('parentID', $this->id);
                    }
        ]);
        parent::__construct($data);
    }

}
