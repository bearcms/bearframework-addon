<?php

/*
 * BearCMS addon for Bear Framework
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
        $dataKey = 'bearcms/uploadssize.json';
        $app->data->set($app->data->make($dataKey, json_encode($data)));
        \BearCMS\Internal\Data::setChanged($dataKey);
        $app->hooks->execute('bearCMSUploadsSizeChanged');
    }

    static function remove($key)
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/uploadssize.json');
        $data = $data === null ? [] : json_decode($data, true);
        if (isset($data[$key])) {
            unset($data[$key]);
            $dataKey = 'bearcms/uploadssize.json';
            $app->data->set($app->data->make($dataKey, json_encode($data)));
            \BearCMS\Internal\Data::setChanged($dataKey);
            $app->hooks->execute('bearCMSUploadsSizeChanged');
        }
    }

    static function getSize()
    {
        $app = App::get();
        $data = \BearCMS\Internal\Data::getValue('bearcms/uploadssize.json');
        $data = $data === null ? [] : json_decode($data, true);
        return array_sum($data);
    }

}
