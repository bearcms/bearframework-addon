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
class UsersTest extends BearCMSTestCase
{

    /**
     * 
     */
    public function testData()
    {
        $app = $this->getApp();

        $this->assertEquals($app->bearCMS->data->users->hasUsers(), false);
        $this->assertEquals($app->bearCMS->data->users->get('userid'), null);
        $this->assertEquals($app->bearCMS->data->users->getList()->count(), 0);

        $app->data->setValue('bearcms/users/user/' . md5('userid') . '.json', json_encode([
            'id' => 'userid',
            'emails' => ['john@example.com']
        ]));

        $this->assertEquals($app->bearCMS->data->users->hasUsers(), true);

        $user = $app->bearCMS->data->users->get('userid');
        $this->assertEquals($user->id, 'userid');
        $this->assertEquals($user->registerTime, null);
        $this->assertEquals($user->lastLoginTime, null);
        $this->assertEquals($user->hashedPassword, null);
        $this->assertEquals($user->emails, ['john@example.com']);
        $this->assertEquals($user->permissions, []);

        $this->assertEquals($app->bearCMS->data->users->getList()->count(), 1);
    }

}
