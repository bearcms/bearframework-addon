<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

class Features
{

    static $data = [];

    static function enabled($name)
    {
        return array_search($name, self::$data) !== false || (sizeof(self::$data) === 1 && self::$data[0] === 'all');
    }

}
