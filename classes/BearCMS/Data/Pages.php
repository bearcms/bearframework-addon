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
 * Information about the site pages
 */
class Pages
{

    /**
     * Retrieves information about the page specified
     * 
     * @param string $id The page ID
     * @return array|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function getPage($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/pages/page/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return json_decode($data['body'], true);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return array List containing all pages data
     */
    public function getList()
    {
        $app = App::$instance;
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/pages/page/', 'startsWith']
                    ],
                    'result' => ['body']
                ]
        );
        $result = [];
        foreach ($data as $item) {
            $result[] = json_decode($item['body'], true);
        }
        return $result;
    }

    /**
     * Retrieves an array containing the pages structure
     * 
     * @return array An array containing the pages structure
     */
    public function getStructure()
    {
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/pages/structure.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return json_decode($data['body'], true);
        }
        return [];
    }

}
