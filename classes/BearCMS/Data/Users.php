<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

/**
 * 
 */
class Users
{

    /**
     * 
     * @param string $id
     * @return \BearCMS\Data\Users\User|null
     */
    public function get(string $id): ?\BearCMS\Data\Users\User
    {
        $data = \BearCMS\Internal\Data::getValue('bearcms/users/user/' . md5($id) . '.json');
        if ($data !== null) {
            return \BearCMS\Data\Users\User::fromJSON($data);
        }
        return null;
    }

    /**
     * 
     * @return \BearFramework\Models\ModelsList
     */
    public function getList(): \BearFramework\Models\ModelsList
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/user/');
        array_walk($list, function(&$value): void {
            $value = \BearCMS\Data\Users\User::fromJSON($value);
        });
        return new \BearFramework\Models\ModelsList($list);
    }

    /**
     * 
     * @return bool
     */
    public function hasUsers(): bool
    {
        $list = \BearCMS\Internal\Data::getList('bearcms/users/user/'); //'limit' => 1 //todo
        return count($list) > 0;
    }

}
