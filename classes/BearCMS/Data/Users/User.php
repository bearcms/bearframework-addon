<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data\Users;

/**
 * @property string $id
 * @property ?int $registerTime
 * @property ?int $lastLoginTime
 * @property ?string $hashedPassword
 * @property array $emails
 * @property array $permissions
 */
class User extends \BearFramework\Models\Model
{

    function __construct()
    {
        $this
            ->defineProperty('id', [
                'type' => 'string'
            ])
            ->defineProperty('registerTime', [
                'type' => '?int'
            ])
            ->defineProperty('lastLoginTime', [
                'type' => '?int'
            ])
            ->defineProperty('hashedPassword', [
                'type' => '?string'
            ])
            ->defineProperty('emails', [
                'type' => 'array'
            ])
            ->defineProperty('permissions', [
                'type' => 'array'
            ]);
    }
}
