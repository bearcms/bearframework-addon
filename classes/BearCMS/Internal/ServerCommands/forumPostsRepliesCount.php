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
    if (!isset($data['type'])) {
        throw new Exception('');
    }
    $result = $app->bearCMS->data->forumPostsReplies->getList();
    if (isset($data['forumPostID']) && strlen($data['forumPostID']) > 0) {
        $result->filterBy('forumPostID', $data['forumPostID']);
    }
    if ($data['type'] !== 'all') {
        $result->filterBy('status', $data['type']);
    }
    return $result->length;
};
