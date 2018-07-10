<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $list = \BearCMS\Internal\Data::getList('bearcms/pages/page/');
    $structure = \BearCMS\Internal\Data::getValue('bearcms/pages/structure.json');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['pages'] = [];
    foreach ($list as $value) {
        $temp['pages'][] = json_decode($value, true);
    }
    return $temp;
};
