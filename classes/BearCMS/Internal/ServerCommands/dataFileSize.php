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
    $filename = $app->data->getFilename($data['key']);
    if (is_file($filename)) {
        return filesize($filename);
    }
    return 0;
};
