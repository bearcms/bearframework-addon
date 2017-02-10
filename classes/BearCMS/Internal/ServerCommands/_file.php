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
    if (!isset($data['filename'])) {
        throw new Exception('');
    }
    $item = $app->data->get('bearcms/files/custom/' . $data['filename']);
    if ($item !== null) {
        $key = $item->key;
        $fullFilename = $app->data->getFilename($key);
        $result = [
            'filename' => str_replace('bearcms/files/custom/', '', $key),
            'name' => (isset($item->metadata->name) ? $item->metadata->name : str_replace('bearcms/files/custom/', '', $key)),
            'published' => (isset($item->metadata->published) ? (int) $item->metadata->published : 0),
            'size' => filesize($fullFilename),
            'dateUploaded' => filemtime($fullFilename)
        ];
        return $result;
    }
    return null;
};
