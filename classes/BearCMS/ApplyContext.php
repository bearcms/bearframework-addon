<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * 
 * @property string|null $language
 */
class ApplyContext
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
        $this
            ->defineProperty('language', [
                'type' => '?string'
            ]);
    }
}
