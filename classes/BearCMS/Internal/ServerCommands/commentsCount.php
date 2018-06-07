<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    if (!isset($data['type'])) {
        throw new Exception('');
    }
    $result = $app->bearCMS->data->comments->getList();
    if ($data['type'] !== 'all') {
        $result->filterBy('status', $data['type']);
    }
    return $result->length;
};
