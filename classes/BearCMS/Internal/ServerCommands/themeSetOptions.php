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
    $themeID = $data['id'];
    $values = $data['values'];
    $app->bearCMS->data->themes->setOptions($themeID, $values);
};
