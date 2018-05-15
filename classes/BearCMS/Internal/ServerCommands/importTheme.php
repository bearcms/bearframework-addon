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
    $dataKey = $data['key'];
    $themeID = $data['themeID'];
    $userID = $data['userID'];
    try {
        $app->bearCMS->themes->import($dataKey, $themeID, $userID);
        return ['status' => 'ok'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'errorCode' => $e->getCode()];
    }
};
