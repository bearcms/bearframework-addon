<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class UsersDataTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testUsers()
    {
        $app = $this->getApp();

        $this->assertFalse(Internal2::$data2->users->hasUsers());

        $app->data->setValue('bearcms/users/user/' . md5('ewo8phkta3fa1') . '.json', '{
    "id": "ewo8phkta3fa1",
    "registerTime": 1464920375,
    "lastLoginTime": 1464920375,
    "hashedPassword": "1c8128bd6e4aa8303f7c6a48bb70a23c1a17aa5b765e986fa1",
    "emails": [
        "john@example.com"
    ],
    "permissions": [
        "modifyContent",
        "managePages",
        "manageAppearance",
        "manageBlog",
        "manageFiles",
        "manageAddons",
        "manageAdministrators",
        "manageSettings",
        "viewAboutInformation"
    ]
}');

        $app->data->setValue('bearcms/users/user/' . md5('ewo8phkta3fa2') . '.json', '{
    "id": "ewo8phkta3fa2",
    "registerTime": 1464920375,
    "lastLoginTime": 1464920375,
    "hashedPassword": "1c8128bd6e4aa8303f7c6a48bb70a23c1a17aa5b765e986fa1",
    "emails": [
        "mark@example.com"
    ],
    "permissions": [
        "modifyContent",
        "managePages",
        "manageAppearance",
        "manageBlog",
        "manageFiles",
        "manageAddons",
        "manageAdministrators",
        "manageSettings",
        "viewAboutInformation"
    ]
}');

        $list = Internal2::$data2->users->getList()
                ->sortBy('id');
        $this->assertTrue($list[0]->id === 'ewo8phkta3fa1');
        $this->assertTrue($list[1]->id === 'ewo8phkta3fa2');

        $user = Internal2::$data2->users->get('ewo8phkta3fa1');
        $this->assertTrue($user->id === 'ewo8phkta3fa1');

        $this->assertTrue(Internal2::$data2->users->hasUsers());
    }

}
