<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

/**
 * @property string $id
 * @property string $status
 * @property array $author
 * @property ?string $text
 * @property ?int $createdTime
 * @internal
 * @codeCoverageIgnore
 */
class Comment
{

    use \IvoPetkov\DataObjectTrait;
    use \IvoPetkov\DataObjectToArrayTrait;

    function __construct()
    {
        $this
            ->defineProperty('id', [
                'type' => 'string'
            ])
            ->defineProperty('status', [
                'type' => 'string'
            ])
            ->defineProperty('author', [
                'type' => 'array'
            ])
            ->defineProperty('text', [
                'type' => '?string'
            ])
            ->defineProperty('createdTime', [
                'type' => '?int'
            ])
            ->defineProperty('files', [
                'type' => '?array'
            ]);
    }
}
