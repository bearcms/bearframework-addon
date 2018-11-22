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
    if (!isset($data['forumPostID'])) {
        throw new Exception('');
    }
    if (!isset($data['status'])) {
        throw new Exception('');
    }
    Internal\Data\ForumPosts::setStatus($data['forumPostID'], $data['status']);
    return true;
};
