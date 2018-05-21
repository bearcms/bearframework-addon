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
    $dataKey = $app->bearCMS->themes->export($themeID);
    $app->data->makePublic($dataKey);
    return ['downloadUrl' => $app->assets->getUrl($app->data->getFilename($dataKey), ['download' => true])];
};
