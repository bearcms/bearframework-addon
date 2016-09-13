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
     * @return array List containing all users data
     */
    public function getList()
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
