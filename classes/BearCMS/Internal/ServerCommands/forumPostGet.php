<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

return function($data) {
    $app = App::get();
    if (!isset($data['forumPostID'])) {
        throw new Exception('');
    }
    $result = $app->bearCMS->data->forumPosts->get($data['forumPostID']);
    $result->author = Internal\PublicProfile::getFromAuthor($result->author)->toArray();
    $result->replies = new \BearCMS\DataList();
    return $result->toArray();
};
