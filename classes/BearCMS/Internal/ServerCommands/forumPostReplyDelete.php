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
    if (!isset($data['forumPostID'])) {
        throw new Exception('');
    }
    if (!isset($data['replyID'])) {
        throw new Exception('');
    }
    BearCMS\Internal\Data\ForumPostsReplies::deleteReplyForever($data['forumPostID'], $data['replyID']);
    return true;
};
