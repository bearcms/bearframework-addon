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
     * @param array $options List of options. Available values: PUBLISHED_ONLY, NOT_PUBLISHED_ONLY, SORT_BY_NAME, SORT_BY_NAME_DESC
     * @return array List containing all pages data
     */
    public function getList($options = [])
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

        $filterByAttribute = function($name, $value) use (&$result) {
            $temp = [];
            foreach ($result as $item) {
                if (isset($item[$name]) && $item[$name] === $value) {
                    $temp[] = $item;
                }
            }
            $result = $temp;
        };

        $sortByStringAttribute = function($name, $order = 'asc') use (&$result) {
            usort($result, function($item1, $item2) use ($name, $order) {
                if (isset($item1[$name], $item2[$name])) {
                    return strcmp($item1[$name], $item2[$name]) * ($order === 'asc' ? 1 : -1);
                }
                return 0;
            });
        };

        if (array_search('PUBLISHED_ONLY', $options) !== false) {
            $filterByAttribute('status', 'published');
        } elseif (array_search('NOT_PUBLISHED_ONLY', $options) !== false) {
            $filterByAttribute('status', 'notPublished');
        }

        if (array_search('SORT_BY_NAME', $options) !== false) {
            $sortByStringAttribute('name', 'asc');
        } elseif (array_search('SORT_BY_NAME_DESC', $options) !== false) {
            $sortByStringAttribute('name', 'desc');
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
