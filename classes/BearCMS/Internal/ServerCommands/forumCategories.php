<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

return function() {
    $app = App::get();
    $list = Internal\Data::getList('bearcms/forums/categories/category/');
    $structure = Internal\Data::getValue('bearcms/forums/categories/structure.json');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['categories'] = [];
    foreach ($list as $value) {
        $temp['categories'][] = json_decode($value, true);
    }
    return $temp;
};
