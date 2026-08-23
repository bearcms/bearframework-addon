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
class FileCategories
{

    /**
     * 
     * @param string string $id
     * @return array
     */
    static function get(string $id): ?array
    {
        $app = App::get();
        $dataKey = 'bearcms/files/custom-categories/' . md5($id) . '.json';
        $item = $app->data->get($dataKey);
        if ($item !== null) {
            return json_decode($item->value, true);
        }
        return null;
    }

    /**
     * 
     * @param string $id
     * @param array $data
     * @return void
     */
    static function set(string $id, array $data)
    {
        $app = App::get();
        $dataKey = 'bearcms/files/custom-categories/' . md5($id) . '.json';
        $app->data->setValue($dataKey, json_encode($data, true));
    }

    /**
     * 
     * @param string $id
     * @return void
     */
    static function delete(string $id)
    {
        $app = App::get();
        $dataKey = 'bearcms/files/custom-categories/' . md5($id) . '.json';
        $app->data->delete($dataKey);
    }

    /**
     * 
     * @return array
     */
    static function getList(): array
    {
        $app = App::get();
        $prefix = 'bearcms/files/custom-categories/';
        $list = $app->data->getList()
            ->filterBy('key', $prefix, 'startWith');
        $result = [];
        foreach ($list as $item) {
            $result[] = json_decode($item->value, true);
        }
        return $result;
    }
}
