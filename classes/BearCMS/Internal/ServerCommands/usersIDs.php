<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $users = $app->bearCMS->data->users->getList();
    $result = [];
    foreach ($users as $user) {
        $result[] = $user->id;
    }
    return $result;
};
