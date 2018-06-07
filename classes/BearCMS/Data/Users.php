<?php

/*
 * BearCMS addon for Bear Framework
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

    private function makeUserFromRawData($rawData): \BearCMS\Data\User
    {
        $rawData = json_decode($rawData, true);
        $user = new \BearCMS\Data\User();
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
     * @return \BearCMS\Data\User|null The user data or null if user not found
     */
    public function get(string $id): ?\BearCMS\Data\User
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
     * @return \BearCMS\DataList|\BearCMS\Data\User[] List containing all users data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/user/');
        array_walk($list, function(&$value) {
            $value = $this->makeUserFromRawData($value);
        });
        return new \BearCMS\DataList($list);
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
