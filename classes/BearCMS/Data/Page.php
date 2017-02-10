<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use \BearFramework\App;

class Page extends \BearCMS\DataObject
{

    function initialize()
    {

        $this->defineProperty('children', [
            'get' => function() {
                $app = App::get();
                return $app->bearCMS->data->pages->getList()
                                ->filterBy('parentID', $this->id);
            }
        ]);
    }

}
