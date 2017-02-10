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

    private static $listDataCache = null;

    private function makePageFromRawData($rawData): \BearCMS\Data\Page
    {
        return new \BearCMS\Data\Page(json_decode($rawData, true));
    }

    /**
     * Retrieves information about the page specified
     * 
     * @param string $id The page ID
     * @return \BearCMS\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Data\Page
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/pages/page/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makePageFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \BearCMS\DataList List containing all pages data
     */
    public function getList(): \BearCMS\DataList
    {
        $app = App::get();
        if (self::$listDataCache === null) {
            $list = $app->data->getList()
                    ->filterBy('key', 'bearcms/pages/page/', 'startWith');
            self::$listDataCache = [];
            foreach ($list as $item) {
                self::$listDataCache[] = $this->makePageFromRawData($item->value);
            }
            $structureData = $app->data->getValue('bearcms/pages/structure.json');
            $flattenStructureData = [];
            if ($structureData !== null) {
                $structureData = json_decode($structureData, true);
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
            usort(self::$listDataCache, function($object1, $object2) use ($flattenStructureData) {
                $object1Index = array_search($object1->id, $flattenStructureData);
                $object2Index = array_search($object2->id, $flattenStructureData);
                return $object1Index - $object2Index;
            });
        }
        return new \BearCMS\DataList(self::$listDataCache);
    }

}
