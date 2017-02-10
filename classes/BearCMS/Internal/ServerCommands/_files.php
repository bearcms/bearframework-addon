<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    $result = $app->data->getList()
            ->filterBy('key', 'bearcms/files/custom/', 'startWith');
    $temp = [];
    foreach ($result as $item) {
        $key = $item->key;
        $temp[] = [
            'filename' => str_replace('bearcms/files/custom/', '', $key),
            'name' => (isset($item->metadata->name) ? $item->metadata->name : str_replace('bearcms/files/custom/', '', $key)),
            'published' => (isset($item->metadata->published) ? (int) $item->metadata->published : 0)
        ];
    }
    return $temp;
};
