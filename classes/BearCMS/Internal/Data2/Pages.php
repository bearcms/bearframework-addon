<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data2;

use BearCMS\Internal;

/**
 * @internal
 */
class Pages
{

//    use \BearFramework\Models\ModelsRepositoryTrait;
//    use \BearFramework\Models\ModelsRepositoryRequestTrait;
//    use \BearFramework\Models\ModelsRepositoryToArrayTrait;
//    use \BearFramework\Models\ModelsRepositoryToJSONTrait;
//
//    function __construct()
//    {
//        $this->setModel(\BearCMS\Internal\Data2\Page::class, 'id');
//        $this->useAppDataDriver('bearcms/pages/page/');
//    }

    static private $cache = [];

    private function makePageFromRawData($rawData): \BearCMS\Internal\Data2\Page
    {
        $data = json_decode($rawData, true);
        if (isset($data['parentID']) && strlen($data['parentID']) === 0) {
            $data['parentID'] = null;
        }
        return new Internal\Data2\Page($data);
    }

    /**
     * Retrieves information about the page specified
     * 
     * @param string $id The page ID
     * @return \BearCMS\Internal\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\Page
    {
        $data = Internal\Data::getValue('bearcms/pages/page/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makePageFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \BearCMS\Internal\DataList List containing all pages data
     */
    public function getList(): \BearCMS\Internal\DataList
    {
        if (!isset(self::$cache['list'])) {
            $list = Internal\Data::getList('bearcms/pages/page/');
            array_walk($list, function(&$value) {
                $value = $this->makePageFromRawData($value);
            });
            $structureData = Internal\Data::getValue('bearcms/pages/structure.json');
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
        return new Internal\DataList(self::$cache['list']);
    }

}
