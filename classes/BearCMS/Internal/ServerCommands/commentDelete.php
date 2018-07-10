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
    if (!isset($data['threadID'])) {
        throw new Exception('');
    }
    if (!isset($data['commentID'])) {
        throw new Exception('');
    }
    BearCMS\Internal\Data\Comments::deleteCommentForever($data['threadID'], $data['commentID']);
    return true;
};
