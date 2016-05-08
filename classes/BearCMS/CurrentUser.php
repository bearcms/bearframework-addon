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

class CurrentUser
{

    private static $cache = [];

    /**
     * 
     * @return boolean
     */
    public function exists()
    {
        return strlen($this->getKey()) > 70;
    }

    /**
     * 
     * @return string
     */
    public function getKey()
    {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        $cookieKey = '_s';
        $key = isset($cookies[$cookieKey]) ? (string) $cookies[$cookieKey] : '';
        return strlen((string) $key) > 70 ? $key : '';
    }

    /**
     * 
     * @return string
     */
    public function getID()
    {
        $cacheKey = 'id-' . $this->getKey();
        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = null;
            $app = App::$instance;
            $key = self::getKey();
            if (strlen($key) > 0) {
                $data = $app->data->get([
                    'key' => '.temp/bearcms/userkeys/' . md5($key),
                    'result' => ['body']
                ]);
                if (isset($data['body'])) {
                    self::$cache[$cacheKey] = $data['body'];
                }
            }
        }
        return self::$cache[$cacheKey];
    }

    /**
     * 
     * @return array
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
     * 
     * @return array
     */
    public function hasPermission($name)
    {
        $permissions = $this->getPermissions();
        return array_search($name, $permissions) !== false;
    }

}
