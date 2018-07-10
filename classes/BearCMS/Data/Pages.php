<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearFramework\App;

/**
 * Information about the site pages
 */
class Pages
{

    static private $cache = [];

    private function makePageFromRawData($rawData): \BearCMS\Data\Page
    {
        $data = json_decode($rawData, true);
        if (isset($data['parentID']) && strlen($data['parentID']) === 0) {
            $data['parentID'] = null;
        }
        return new \BearCMS\Data\Page($data);
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
        $data = \BearCMS\Internal\Data::getValue('bearcms/pages/page/' . md5($id) . '.json');
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
        if (!isset(self::$cache['list'])) {
            $list = \BearCMS\Internal\Data::getList('bearcms/pages/page/');
            array_walk($list, function(&$value) {
                $value = $this->makePageFromRawData($value);
            });
            $structureData = \BearCMS\Internal\Data::getValue('bearcms/pages/structure.json');
            $structureData = $structureData === null ? [] : json_decode($structureData, true);
            $flattenStructureData = [];
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
            $flattenStructureData = array_flip($flattenStructureData);
            usort($list, function($object1, $object2) use ($flattenStructureData) {
                return $flattenStructureData[$object1->id] - $flattenStructureData[$object2->id];
            });
            unset($flattenStructureData);
            self::$cache['list'] = $list;
            unset($list);
        }
        return new \BearCMS\DataList(self::$cache['list']);
    }

}
