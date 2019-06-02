<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class UploadsSize
{

    /**
     * 
     * @param string $key
     * @param int $size
     * @return void
     */
    static function add(string $key, int $size): void
    {
        $data = self::getData();
        $data[$key] = $size;
        self::setData($data);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    static function remove(string $key): void
    {
        $data = self::getData();
        if (isset($data[$key])) {
            unset($data[$key]);
            self::setData($data);
        }
    }

    /**
     * 
     * @return int
     */
    static function getSize(): int
    {
        $data = self::getData();
        return array_sum($data);
    }

    /**
     * 
     * @return array
     */
    static function getData(): array
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/uploadssize.json');
        return $data === null ? [] : json_decode($data, true);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function setData(array $data): void
    {
        $app = App::get();
        $dataKey = 'bearcms/uploadssize.json';
        $app->data->set($app->data->make($dataKey, json_encode($data)));
        $app->bearCMS->dispatchEvent('internalChangeUploadsSize');
    }

}
