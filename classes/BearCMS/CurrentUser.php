<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearCMS\Internal;

/**
 * 
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
    public function exists(): bool
    {
        return $this->getID() !== null;
    }

    /**
     * Returns the session key if there is a logged in user
     * 
     * @return string|null The session key if there is a logged in user, NULL otherwise
     */
    public function getSessionKey(): ?string
    {
        $cacheKey = 'sessionkey';
        if (!isset(self::$cache[$cacheKey])) {
            $cookies = Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER);
            $cookieKey = '_s';
            $key = isset($cookies[$cookieKey]) ? (string) $cookies[$cookieKey] : '';
            self::$cache[$cacheKey] = strlen((string) $key) > 70 ? $key : null;
        }
        return self::$cache[$cacheKey];
    }

    /**
     * Returns the current logged in user ID
     * 
     * @return string|null ID of the current logged in user or null
     */
    public function getID(): ?string
    {
        $sessionKey = $this->getSessionKey();
        if (strlen($sessionKey) === 0) {
            return null;
        }
        $cacheKey = 'id-' . $sessionKey;
        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = null;
            $data = Internal\Data::getValue('.temp/bearcms/userkeys/' . md5($sessionKey));
            if ($data !== null) {
                self::$cache[$cacheKey] = $data;
            }
        }
        return self::$cache[$cacheKey];
    }

    /**
     * Returns the current logged in user permissions
     * 
     * @return array Array containing the permission of the current logged in user
     */
    public function getPermissions(): array
    {
        $userID = $this->getID();
        if ($userID === null) {
            return [];
        }
        $data = Internal\Data::getValue('bearcms/users/user/' . md5($userID) . '.json');
        if ($data !== null) {
            $user = json_decode($data, true);
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
    public function hasPermission(string $name): bool
    {
        $permissions = $this->getPermissions();
        if (array_search('all', $permissions) !== false) {
            return true;
        }
        return array_search($name, $permissions) !== false;
    }

    /**
     * Login a user without email and password validation. This methods must be enabled on the CMS server.
     * 
     * @param string $userID
     * @return bool Returns TRUE if the user is logged in successfully, FALSE otherwise.
     */
    public function login(string $userID): bool
    {
        $data = Internal\Server::call('login', ['userID' => $userID], true);
        if (isset($data['result'])) {
            return $data['result'] === 'ok';
        }
        return false;
    }

    /**
     * Logout the current user.
     * 
     * @param string $userID
     * @throws \InvalidArgumentException
     */
    public function logout(): void
    {
        Internal\Cookies::setList(Internal\Cookies::TYPE_SERVER, [
            ['name' => '_s', 'value' => 'deleted', 'expire' => 0],
            ['name' => '_a', 'value' => 'deleted', 'expire' => 0]
        ]);
        self::$cache = [];
    }

}
