<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Pages
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getPathsList(string $status = 'all'): array
    {
        $app = App::get();
        $list = $app->data->getList()
                ->filterBy('key', 'bearcms/pages/page/', 'startWith');
        $result = [];
        foreach ($list as $item) {
            $pageData = json_decode($item->value, true);
            if (
                    is_array($pageData) &&
                    isset($pageData['id']) &&
                    isset($pageData['path']) &&
                    isset($pageData['status']) &&
                    is_string($pageData['id']) &&
                    is_string($pageData['path']) &&
                    is_string($pageData['status'])
            ) {
                if ($status !== 'all' && $status !== $pageData['status']) {
                    continue;
                }
                $result[$pageData['id']] = $pageData['path'];
            }
        }
        return $result;
    }

}
