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
     * @return \BearCMS\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function getPage($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('The id agrument must be of type string');
        }
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/pages/page/' . md5($id) . '.json',
                    'result' => ['body']
                ]
        );
        if (isset($data['body'])) {
            $object = new \BearCMS\DataObject(json_decode($data['body'], true));
            $pages = $this;
            $object->defineProperty('children', [
                'get' => function() use ($pages, $object) {
                    return $pages->getList()
                                    ->filterBy('parentID', $object->object->id);
                }
            ]);
            return $object;
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \BearCMS\DataCollection List containing all pages data
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
        $pages = $this;
        foreach ($data as $item) {
            $object = new \BearCMS\DataObject(json_decode($item['body'], true));
            $object->defineProperty('children', [
                'get' => function() use ($pages, $object) {
                    return $pages->getList()
                                    ->filterBy('parentID', $object->id);
                }
            ]);
            $result[] = $object;
        }
        $data = $app->data->get(
                [
                    'key' => 'bearcms/pages/structure.json',
                    'result' => ['body']
                ]
        );
        $flattenStructureData = [];
        if (isset($data['body'])) {
            $structureData = json_decode($data['body'], true);
            $flattenStructure = function($structureData) use (&$flattenStructure, &$flattenStructureData) {
                foreach ($structureData as $item) {
                    $flattenStructureData[] = $item['id'];
                    if (isset($item['children'])) {
                        $flattenStructure($item['children']);
                    }
                }
            };
            $flattenStructure($structureData);
            unset($flattenStructure);
            unset($structureData);
        }
        return (new \BearCMS\DataCollection($result))
                        ->sort(function($object1, $object2) use ($flattenStructureData) {
                            $object1Index = array_search($object1->id, $flattenStructureData);
                            $object2Index = array_search($object2->id, $flattenStructureData);
                            return $object1Index - $object2Index;
                        });
    }

}
