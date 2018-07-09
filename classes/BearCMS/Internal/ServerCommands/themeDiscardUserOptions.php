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
    $themeID = $data['id'];
    $userID = $data['userID'];
    if (strlen($themeID) > 0 && strlen($userID) > 0) {
        $app->bearCMS->data->themes->discardUserOptions($themeID, $userID);
    }
};
