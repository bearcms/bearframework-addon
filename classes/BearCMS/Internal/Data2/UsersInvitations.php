<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

/**
 * @internal
 */
class UsersInvitations
{

    private function makeUserFromRawData($rawData): \BearCMS\Internal\Data2\UserInvitation
    {
        $rawData = json_decode($rawData, true);
        $user = new \BearCMS\Internal\Data2\UserInvitation();
        $properties = ['key', 'email', 'permissions'];
        foreach ($properties as $property) {
            if (array_key_exists($property, $rawData)) {
                $user->$property = $rawData[$property];
            }
        }
        return $user;
    }

    /**
     * Retrieves information about the user invitation specified
     * 
     * @param string $id The user invitation key
     * @return \BearCMS\Internal\Data2\UserInvitation|null The user invitation data or null if not found
     */
    public function get(string $key): ?\BearCMS\Internal\Data2\UserInvitation
    {
        $data = \BearCMS\Internal\Data::getValue('bearcms/users/invitation/' . md5($key) . '.json');
        if ($data !== null) {
            return $this->makeUserFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all users invitations
     * 
     * @return \IvoPetkov\DataList|\BearCMS\Internal\Data2\UserInvitation[] List containing all users invitations data
     */
    public function getList()
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/invitation/');
        array_walk($list, function(&$value) {
            $value = $this->makeUserFromRawData($value);
        });
        return new \IvoPetkov\DataList($list);
    }

}
