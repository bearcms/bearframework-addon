<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

/**
 * @property string $id
 * @property string $status
 * @property array $author
 * @property ?string $text
 * @property ?int $createdTime
 */
class Comment
{

    use \IvoPetkov\DataObjectTrait;
    use \IvoPetkov\DataObjectToArrayTrait;

    function __construct()
    {
        $this->defineProperty('id', [
            'type' => 'string'
        ]);
        $this->defineProperty('status', [
            'type' => 'string'
        ]);
        $this->defineProperty('author', [
            'type' => 'array'
        ]);
        $this->defineProperty('text', [
            'type' => '?string'
        ]);
        $this->defineProperty('createdTime', [
            'type' => '?int'
        ]);
    }

}
