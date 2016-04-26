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
    static function exists()
    {
        return strlen(self::getKey()) > 70;
    }

    /**
     * 
     * @return string
     */
    static function getKey()
    {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        $cookieKey = '_s';
        $key = isset($cookies[$cookieKey]) ? (string) $cookies[$cookieKey] : '';
        return strlen((string) $key) > 70 ? $key : '';
    }

    /**
     * 
     * @param string $key
     * @param string $userID
     */
    static function saveKey($key, $userID)
    {
        $app = App::$instance;
        $app->data->set([
            'key' => '.temp/bearcms/userkeys/' . md5($key),
            'body' => $userID
        ]);
    }

    /**
     * 
     * @return string
     */
    static function getID()
    {
        $cacheKey = 'id-' . CurrentUser::getKey();
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
    static function getPermissions()
    {
        $userID = CurrentUser::getID();
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
    static function hasPermission($name)
    {
        $permissions = self::getPermissions();
        return array_search($name, $permissions) !== false;
    }

}
