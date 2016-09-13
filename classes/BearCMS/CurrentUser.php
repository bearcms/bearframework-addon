<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;
use BearCMS\Internal\Cookies;

/**
 * Information about the current logged in user
 */
class CurrentUser
{

    /**
     * Local cache
     * 
     * @var array 
     */
    private static $cache = [];

    /**
     * Returns information about whether there is a current user logged in
     * 
     * @return boolean TRUE if there is a current user logged in, FALSE otherwise
     */
    public function exists()
    {
        return strlen($this->getSessionKey()) > 70;
    }

    /**
     * Returns the session key if there is a logged in user
     * 
     * @return string|null The session key if there is a logged in user, NULL otherwise
     */
    public function getSessionKey()
    {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        $cookieKey = '_s';
        $key = isset($cookies[$cookieKey]) ? (string) $cookies[$cookieKey] : '';
        return strlen((string) $key) > 70 ? $key : null;
    }

    /**
     * Returns the current logged in user ID
     * 
     * @return string|null ID of the current logged in user or null
     */
    public function getID()
    {

        $sessionKey = $this->getSessionKey();
        if (strlen($sessionKey) === 0) {
            return null;
        }
        $cacheKey = 'id-' . $sessionKey;
        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = null;
            $app = App::$instance;
            $data = $app->data->get([
                'key' => '.temp/bearcms/userkeys/' . md5($sessionKey),
                'result' => ['body']
            ]);
            if (isset($data['body'])) {
                self::$cache[$cacheKey] = $data['body'];
            }
        }
        return self::$cache[$cacheKey];
    }

    /**
     * Returns the current logged in user permissions
     * 
     * @return array Array containing the permission of the current logged in user
     */
    public function getPermissions()
    {
        $userID = $this->getID();
        if ($userID === null) {
            return [];
        }
        $app = App::$instance;
        $data = $app->data->get([
            'key' => 'bearcms/users/user/' . md5($userID) . '.json',
            'result' => ['body']
        ]);
        if (isset($data['body'])) {
            $user = json_decode($data['body'], true);
            return isset($user['permissions']) ? $user['permissions'] : [];
        }
        return [];
    }

    /**
     * Checks whether the current logged in user has the specified permission
     * 
     * @param string $name The name of the permission
     * @return boolean TRUE if the current logged in user has the permission specified, FALSE otherwise
     * @throws \InvalidArgumentException
     */
    public function hasPermission($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('');
        }
        $permissions = $this->getPermissions();
        return array_search($name, $permissions) !== false;
    }

}
