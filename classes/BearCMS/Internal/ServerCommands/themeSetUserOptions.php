<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    $themeID = $data['id'];
    $userID = $data['userID'];
    $values = $data['values'];
    $app->bearCMS->data->themes->setUserOptions($themeID, $userID, $values);
};
