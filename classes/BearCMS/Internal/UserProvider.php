<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

class UserProvider extends \IvoPetkov\BearFrameworkAddons\Users\GuestLoginProvider
{

    public function hasLoginButton(): bool
    {
        return false;
    }

    public function hasLogoutButton(): bool
    {
        return false;
    }

    public function getUserProperties(string $id): array
    {
        $properties = parent::getUserProperties($id);
        return $properties;
    }

}
