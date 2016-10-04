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
     * @return array|null The addon data or null if addon not found
     * @throws \InvalidArgumentException
     */
    public function getAddon($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/addons/addon/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return json_decode($data['body'], true);
        }
        return null;
    }

    /**
     * Retrieves a list of all addons
     * 
     * @param array $options List of options. Available values: ENABLED_ONLY, DISABLED_ONLY
     * @return array List containing all addons data
     */
    public function getList($options = [])
    {
        $app = App::$instance;
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
            $result[] = json_decode($item['body'], true);
        }

        $filterByAttribute = function($name, $value) use (&$result) {
            $temp = [];
            foreach ($result as $item) {
                if (isset($item[$name]) && $item[$name] === $value) {
                    $temp[] = $item;
                }
            }
            $result = $temp;
        };

        if (array_search('ENABLED_ONLY', $options) !== false) {
            $filterByAttribute('enabled', true);
        } elseif (array_search('DISABLED_ONLY', $options) !== false) {
            $filterByAttribute('enabled', false);
        }
        return $result;
    }

}
