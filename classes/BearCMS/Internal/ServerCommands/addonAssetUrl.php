<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
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
    if (!isset($data['addonID'])) {
        throw new Exception('');
    }
    $addonDir = BearFramework\Addons::get($data['addonID'])->dir;
    return $app->assets->getUrl($addonDir . DIRECTORY_SEPARATOR . $data['key'], $data['options']);
};
