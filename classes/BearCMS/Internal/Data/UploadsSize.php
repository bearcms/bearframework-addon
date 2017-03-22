<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

class UploadsSize
{

    static function add($key, $size)
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/uploadssize.json');
        $data = $data === null ? [] : json_decode($data, true);
        $data[$key] = $size;
        $app->data->set($app->data->make('bearcms/uploadssize.json', json_encode($data)));
    }

    static function remove($key)
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/uploadssize.json');
        $data = $data === null ? [] : json_decode($data, true);
        if (isset($data[$key])) {
            unset($data[$key]);
            $app->data->set($app->data->make('bearcms/uploadssize.json', json_encode($data)));
        }
    }

    static function getSize()
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/uploadssize.json');
        $data = $data === null ? [] : json_decode($data, true);
        return array_sum($data);
    }

}
