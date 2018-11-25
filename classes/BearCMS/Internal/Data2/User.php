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
 * @property ?int $registerTime
 * @property ?int $lastLoginTime
 * @property ?string $hashedPassword
 * @property array $emails
 * @property array $permissions
 * @internal
 */
class User
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
        $this->defineProperty('id', [
            'type' => 'string'
        ]);
        $this->defineProperty('registerTime', [
            'type' => '?int'
        ]);
        $this->defineProperty('lastLoginTime', [
            'type' => '?int'
        ]);
        $this->defineProperty('hashedPassword', [
            'type' => '?string'
        ]);
        $this->defineProperty('emails', [
            'type' => 'array'
        ]);
        $this->defineProperty('permissions', [
            'type' => 'array'
        ]);
    }

}
