<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    try {
        $result = mail($data['recipient'], $data['subject'], $data['body']);
    } catch (Exception $e) {
        $result = false;
    }
    $app->logger->log('info', json_encode(['message' => $data, 'result' => (int) $result]));
    return $result;
};
