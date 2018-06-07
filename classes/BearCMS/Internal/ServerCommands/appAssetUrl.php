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
    if (!isset($data['key'])) {
        throw new Exception('');
    }
    if (!isset($data['options'])) {
        throw new Exception('');
    }
    return $app->assets->getUrl($app->config->appDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
};
