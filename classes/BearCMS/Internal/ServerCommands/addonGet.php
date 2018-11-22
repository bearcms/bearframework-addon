<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal;

return function($data) {
    $addon = Internal\Data\Addons::get($data['id']);
    if ($addon !== null) {
        return $addon->toArray();
    }
    return null;
};
