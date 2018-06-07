<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $userInvitation = $app->bearCMS->data->usersInvitations->getList();
    $result = [];
    foreach ($userInvitation as $user) {
        $result[] = $userInvitation->toArray();
    }
    return $result;
};
