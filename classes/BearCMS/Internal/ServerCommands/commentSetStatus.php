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
    if (!isset($data['threadID'])) {
        throw new Exception('');
    }
    if (!isset($data['commentID'])) {
        throw new Exception('');
    }
    if (!isset($data['status'])) {
        throw new Exception('');
    }
    BearCMS\Internal\Data\Comments::setStatus($data['threadID'], $data['commentID'], $data['status']);
    return true;
};