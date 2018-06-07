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
    $sourceDataKey = $data['sourceDataKey'];
    $themeID = $data['id'];
    $userID = $data['userID'];
    try {
        $app->bearCMS->themes->import($sourceDataKey, $themeID, $userID);
        return ['status' => 'ok'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'errorCode' => $e->getCode()];
    }
};
