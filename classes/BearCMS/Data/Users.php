<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the CMS users (administrators)
 */
class Users
{

    /**
     * Retrieves information about the user specified
     * 
     * @param string $id The user ID
     * @return array|null The user data or null if user not found
     * @throws \InvalidArgumentException
     */
    public function getUser($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/users/user/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return json_decode($data['body'], true);
        }
        return null;
    }

    /**
     * Retrieves a list of all users
     * 
     * @param array $options List of options. Available values: SORT_BY_LAST_LOGIN_TIME, SORT_BY_LAST_LOGIN_TIME_DESC, SORT_BY_EMAIL, SORT_BY_EMAIL_DESC
     * @return array List containing all users data
     */
    public function getList($options = [])
    {
        $app = App::$instance;
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/users/user/', 'startsWith']
                    ],
                    'result' => ['body']
                ]
        );
        $result = [];
        foreach ($data as $item) {
            $result[] = json_decode($item['body'], true);
        }

        $sortByIntAttribute = function($name, $order) use (&$result) {
            usort($result, function($item1, $item2) use ($name, $order) {
                if (isset($item1[$name], $item2[$name])) {
                    if ($item1[$name] < $item2[$name]) {
                        return -1 * ($order === 'asc' ? 1 : -1);
                    } elseif ($item1[$name] > $item2[$name]) {
                        return 1 * ($order === 'asc' ? 1 : -1);
                    }
                }
                return 0;
            });
        };

        $sortByArrayKeyStringAttribute = function($name, $key, $order = 'asc') use (&$result) {
            usort($result, function($item1, $item2) use ($name, $key, $order) {
                if (isset($item1[$name], $item2[$name], $item1[$name][$key], $item2[$name][$key])) {
                    return strcmp($item1[$name][$key], $item2[$name][$key]) * ($order === 'asc' ? 1 : -1);
                }
                return 0;
            });
        };

        if (array_search('SORT_BY_LAST_LOGIN_TIME', $options) !== false) {
            $sortByIntAttribute('lastLoginTime', 'asc');
        } elseif (array_search('SORT_BY_LAST_LOGIN_TIME_DESC', $options) !== false) {
            $sortByIntAttribute('lastLoginTime', 'desc');
        } elseif (array_search('SORT_BY_EMAIL', $options) !== false) {
            $sortByArrayKeyStringAttribute('emails', 0, 'asc');
        } elseif (array_search('SORT_BY_EMAIL_DESC', $options) !== false) {
            $sortByArrayKeyStringAttribute('emails', 0, 'desc');
        }

        return $result;
    }

    /**
     * Checks if there are any users
     * 
     * @return boolean TRUE if there is at least one user, FALSE if there are no users
     */
    public function hasUsers()
    {
        $app = App::$instance;
        $result = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/users/user/', 'startsWith']
                    ],
                    'limit' => 1
                ]
        );
        return sizeof($result) > 0;
    }

}
