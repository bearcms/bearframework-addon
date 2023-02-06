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
     * @param string $key
     * @return integer|null
     */
    static function getItemSize(string $key): ?int
    {
        $data = self::getData();
        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * 
     * @param array $keys
     * @return integer
     */
    static function getItemsSize(array $keys): int
    {
        $size = 0;
        foreach ($keys as $key) {
            $size += (int) self::getItemSize($key);
        }
        return $size;
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
        if (empty($data)) {
            $app->data->delete($dataKey);
        } else {
            $app->data->set($app->data->make($dataKey, json_encode($data, JSON_THROW_ON_ERROR)));
        }
        $app->bearCMS->dispatchEvent('internalChangeUploadsSize');
    }
}
