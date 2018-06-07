<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $userInvitations = $app->bearCMS->data->usersInvitations->getList();
    $result = [];
    foreach ($userInvitations as $userInvitation) {
        $result[] = $userInvitation->toArray();
    }
    return $result;
};
