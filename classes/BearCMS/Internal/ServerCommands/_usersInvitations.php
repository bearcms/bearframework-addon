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
    $result = $app->data->getList()
            ->filterBy('key', 'bearcms/users/invitation/', 'startWith');
    $temp = [];
    foreach ($result as $item) {
        $temp[] = json_decode($item->value, true);
    }
    return $temp;
};
