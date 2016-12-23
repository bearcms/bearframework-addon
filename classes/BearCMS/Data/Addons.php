<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the addons managed by Bear CMS
 */
class Addons
{

    /**
     * Retrieves information about the addon specified
     * 
     * @param string $id The addon ID
     * @return \BearCMS\DataObject|null The addon data or null if addon not found
     * @throws \InvalidArgumentException
     */
    public function getAddon($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('The id agrument must be of type string');
        }
        $app = App::get();
        $data = $app->data->get(
                [
                    'key' => 'bearcms/addons/addon/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return new \BearCMS\DataObject(json_decode($data['body'], true));
        }
        return null;
    }

    /**
     * Retrieves a list of all addons
     * 
     * @return \BearCMS\DataCollection List containing all addons data
     */
    public function getList()
    {
        $app = App::get();
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/addons/addon/', 'startsWith']
                    ],
                    'result' => ['body']
                ]
        );
        $result = [];
        foreach ($data as $item) {
            $result[] = new \BearCMS\DataObject(json_decode($item['body'], true));
        }
        return new \BearCMS\DataCollection($result);
    }

}
