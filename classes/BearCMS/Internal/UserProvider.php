<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class UserProvider extends \IvoPetkov\BearFrameworkAddons\Users\Provider
{

    /**
     * 
     */
    public function __construct()
    {
        $this->options['profileFields'] = ['image', 'name', 'description'];
    }

    /**
     * 
     * @param string $id
     * @return array
     */
    public function getProfileData(string $id): array
    {
        $app = App::get();
        $properties = [];
        $userData = $app->users->getUserData('bearcms', $id);
        if (empty($userData)) {
            $userData = [];
        }
        $properties['name'] = isset($userData['name']) && strlen($userData['name']) > 0 ? $userData['name'] : __('bearcms.users.Administrator');
        if (isset($userData['image']) && strlen($userData['image']) > 0) {
            $properties['image'] = $app->users->getUserFilePath($this->id, $userData['image']);
        }
        if (isset($userData['description'])) {
            $properties['description'] = $userData['description'];
        }
        return $properties;
    }
}
