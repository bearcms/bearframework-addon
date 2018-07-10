<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\PublicProfile;

return function($data) {
    $app = App::get();
    if (!isset($data['type'])) {
        throw new Exception('');
    }
    if (!isset($data['page'])) {
        throw new Exception('');
    }
    if (!isset($data['limit'])) {
        throw new Exception('');
    }
    $result = $app->bearCMS->data->comments->getList();
    $result->sortBy('createdTime', 'desc');
    if ($data['type'] !== 'all') {
        $result->filterBy('status', $data['type']);
    }
    $result = $result->slice($data['limit'] * ($data['page'] - 1), $data['limit']);
    $locations = BearCMS\Internal\Data\Comments::getCommentsElementsLocations();
    foreach ($result as $i => $item) {
        if (isset($locations[$item->threadID])) {
            $result[$i]->location = $locations[$item->threadID];
        } else {
            $result[$i]->location = '';
        }
        $result[$i]->author = PublicProfile::getFromAuthor($item->author)->toArray();
    }
    return $result->toArray();
};
