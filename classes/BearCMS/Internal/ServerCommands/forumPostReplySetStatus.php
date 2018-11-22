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
    if (!isset($data['replyID'])) {
        throw new Exception('');
    }
    if (!isset($data['status'])) {
        throw new Exception('');
    }
    Internal\Data\ForumPostsReplies::setStatus($data['forumPostID'], $data['replyID'], $data['status']);
    return true;
};
