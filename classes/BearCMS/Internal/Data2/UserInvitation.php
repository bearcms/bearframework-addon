<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

/**
 * @property string $key
 * @property string $email
 * @property array $permissions
 */
class UserInvitation
{

    use \IvoPetkov\DataObjectTrait;
    use \IvoPetkov\DataObjectToArrayTrait;

    function __construct()
    {
        $this->defineProperty('key', [
            'type' => 'string'
        ]);
        $this->defineProperty('email', [
            'type' => 'string'
        ]);
        $this->defineProperty('permissions', [
            'type' => 'array'
        ]);
    }

}
