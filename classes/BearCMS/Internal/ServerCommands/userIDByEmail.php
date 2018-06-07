<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    if (!isset($data['email'])) {
        throw new Exception('');
    }
    $email = (string) $data['email'];
    $app = App::get();
    $users = $app->bearCMS->data->users->getList();
    foreach ($users as $user) {
        if (array_search($email, $user->emails) !== false) {
            return $user->id;
        }
    }
    return null;
};
