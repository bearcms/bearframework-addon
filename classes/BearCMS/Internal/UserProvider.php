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
class UserProvider extends \IvoPetkov\BearFrameworkAddons\Users\LoginProvider
{

    /**
     * 
     */
    public function __construct()
    {
        $this->hasSettings = true;
    }

    /**
     * 
     * @return string
     */
    public function getSettingsForm(): string
    {
        $app = App::get();
        $context = $app->contexts->get();
        return $app->components->process('<component src="form" filename="' . $context->dir . '/components/bearcms-user-profile-settings-form.php"/>');
    }

    /**
     * 
     * @param string $id
     * @return array
     */
    public function getUserProperties(string $id): array
    {
        $app = App::get();
        $properties = [];
        $userData = $app->users->getUserData('bearcms', $id);
        if (empty($userData)) {
            $userData = [];
        }
        $properties['name'] = isset($userData['name']) && strlen($userData['name']) > 0 ? $userData['name'] : __('bearcms.users.Administrator');
        if (isset($userData['image']) && strlen($userData['image']) > 0) {
            $properties['image'] = $app->users->getUserFilePath('bearcms', $userData['image']);
        }
        if (isset($userData['description'])) {
            $properties['description'] = $userData['description'];
        }
        return $properties;
    }

}
