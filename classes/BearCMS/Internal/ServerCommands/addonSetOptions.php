<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function($data) {
    BearCMS\Internal\Data\Addons::setOptions($data['id'], $data['options']);
    if ($data['enabled'] !== null) {
        if ($data['enabled']) {
            BearCMS\Internal\Data\Addons::enable($data['id']);
        } else {
            BearCMS\Internal\Data\Addons::disable($data['id']);
        }
    }
};
