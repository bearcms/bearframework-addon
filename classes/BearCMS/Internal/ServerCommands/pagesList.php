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
    $list = Internal\Data::getList('bearcms/pages/page/');
    $structure = Internal\Data::getValue('bearcms/pages/structure.json');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['pages'] = [];
    foreach ($list as $value) {
        $temp['pages'][] = json_decode($value, true);
    }
    return $temp;
};
