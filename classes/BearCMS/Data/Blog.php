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
 * Information about the blog posts
 */
class Blog
{

    /**
     * Retrieves information about the blog post specified
     * 
     * @param string $id The blog post ID
     * @return array|null The blog post data or null if blog post not found
     * @throws \InvalidArgumentException
     */
    public function getPost($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/blog/post/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            return json_decode($data['body'], true);
        }
        return null;
    }

    /**
     * Retrieves a list of all blog posts
     * 
     * @param array $options List of options. Available values: PUBLISHED_ONLY, DRAFTS_ONLY, TRASHED_ONLY, SORT_BY_TITLE, SORT_BY_TITLE_DESC, SORT_BY_PUBLISHED_TIME, SORT_BY_PUBLISHED_TIME_DESC, SORT_BY_CREATED_TIME, SORT_BY_CREATED_TIME_DESC, SORT_BY_TRASHED_TIME, SORT_BY_TRASHED_TIME_DESC
     * @return array List containing all blog posts data
     */
    public function getList($options = [])
    {
        $app = App::$instance;
        $data = $app->data->search(
                [
                    'where' => [
                        ['key', 'bearcms/blog/post/', 'startsWith']
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

        $sortByIntAttribute = function($name, $order) use (&$result) {
            usort($result, function($item1, $item2) use ($name, $order) {
                if (isset($item1[$name], $item2[$name])) {
                    if ($item1[$name] < $item2[$name]) {
                        return -1 * ($order === 'asc' ? 1 : -1);
                    } elseif ($item1[$name] > $item2[$name]) {
                        return 1 * ($order === 'asc' ? 1 : -1);
                    }
                }
                return 0;
            });
        };

        if (array_search('PUBLISHED_ONLY', $options) !== false) {
            $filterByAttribute('status', 'published');
        } elseif (array_search('DRAFTS_ONLY', $options) !== false) {
            $filterByAttribute('status', 'draft');
        } elseif (array_search('TRASHED_ONLY', $options) !== false) {
            $filterByAttribute('status', 'trashed');
        }

        if (array_search('SORT_BY_TITLE', $options) !== false) {
            $sortByStringAttribute('title', 'asc');
        } elseif (array_search('SORT_BY_TITLE_DESC', $options) !== false) {
            $sortByStringAttribute('title', 'desc');
        } elseif (array_search('SORT_BY_PUBLISHED_TIME', $options) !== false) {
            $sortByIntAttribute('publishedTime', 'asc');
        } elseif (array_search('SORT_BY_PUBLISHED_TIME_DESC', $options) !== false) {
            $sortByIntAttribute('publishedTime', 'desc');
        } elseif (array_search('SORT_BY_CREATED_TIME', $options) !== false) {
            $sortByIntAttribute('createdTime', 'asc');
        } elseif (array_search('SORT_BY_CREATED_TIME_DESC', $options) !== false) {
            $sortByIntAttribute('createdTime', 'desc');
        } elseif (array_search('SORT_BY_TRASHED_TIME', $options) !== false) {
            $sortByIntAttribute('trashedTime', 'asc');
        } elseif (array_search('SORT_BY_TRASHED_TIME_DESC', $options) !== false) {
            $sortByIntAttribute('trashedTime', 'desc');
        }

        return $result;
    }

}
