<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $list = \BearCMS\Internal\Data::getList('bearcms/forums/categories/category/');
    $structure = \BearCMS\Internal\Data::getValue('bearcms/forums/categories/structure.json');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['categories'] = [];
    foreach ($list as $value) {
        $temp['categories'][] = json_decode($value, true);
    }
    return $temp;
};
