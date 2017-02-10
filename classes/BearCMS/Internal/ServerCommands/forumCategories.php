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
    $structure = $app->data->getValue('bearcms/forum/categories/structure.json');
    $categories = $app->data->getList()
            ->filterBy('key', 'bearcms/forum/categories/category/', 'startWith');
    $temp = [];
    $temp['structure'] = $structure !== null ? json_decode($structure, true) : [];
    $temp['categories'] = [];
    foreach ($categories as $category) {
        $temp['categories'][] = json_decode($category->value, true);
    }
    return $temp;
};
