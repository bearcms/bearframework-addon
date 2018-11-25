<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearFramework\App;

/**
 * @internal
 */
class Users
{

    private function makeUserFromRawData($rawData): \BearCMS\Internal\Data2\User
    {
        $rawData = json_decode($rawData, true);
        $user = new \BearCMS\Internal\Data2\User();
        $properties = ['id', 'registerTime', 'lastLoginTime', 'hashedPassword', 'emails', 'permissions'];
        foreach ($properties as $property) {
            if (array_key_exists($property, $rawData)) {
                $user->$property = $rawData[$property];
            }
        }
        return $user;
    }

    /**
     * Retrieves information about the user specified
     * 
     * @param string $id The user ID
     * @return \BearCMS\Internal\Data2\User|null The user data or null if user not found
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\User
    {
        $data = \BearCMS\Internal\Data::getValue('bearcms/users/user/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeUserFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all users
     * 
     * @return \BearCMS\Internal\DataList|\BearCMS\Internal\Data2\User[] List containing all users data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/user/');
        array_walk($list, function(&$value) {
            $value = $this->makeUserFromRawData($value);
        });
        return new \BearCMS\Internal\DataList($list);
    }

    /**
     * Checks if there are any users
     * 
     * @return boolean TRUE if there is at least one user, FALSE if there are no users
     */
    public function hasUsers()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/user/'); //'limit' => 1 //todo
        return sizeof($list) > 0;
    }

}
