<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

/**
 * @internal
 * @codeCoverageIgnore
 */
class BlogCategory
{

    use \IvoPetkov\DataObjectTrait;
    use \IvoPetkov\DataObjectToArrayTrait;

    function __construct()
    {
        $this
            ->defineProperty('id', [
                'type' => 'string'
            ])
            ->defineProperty('name', [
                'type' => 'string'
            ])
            ->defineProperty('status', [
                'type' => 'string'
            ]);
    }
}
