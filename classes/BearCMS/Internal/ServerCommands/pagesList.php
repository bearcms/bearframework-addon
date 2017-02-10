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
    $structure = $app->data->getValue('bearcms/pages/structure.json');
    $pages = $app->data->getList()
            ->filterBy('key', 'bearcms/pages/page/', 'startWith');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['pages'] = [];
    foreach ($pages as $page) {
        $temp['pages'][] = json_decode($page->value, true);
    }
    return $temp;
};
