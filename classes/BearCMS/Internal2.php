<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * @property-read \BearCMS\Internal\Data2 $data2
 */
class Internal2
{

    /**
     *
     * @var \BearCMS\Internal\Data2 
     */
    static $data2 = null;

    /**
     * 
     */
    static function initialize()
    {
        if (self::$data2 === null) {
            self::$data2 = new \BearCMS\Internal\Data2();
        }
    }

}
