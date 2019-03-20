<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;

/**
 * 
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
//        $this->setModel(\BearCMS\Data\Pages\Page::class, 'id');
//        $this->useAppDataDriver('bearcms/pages/page/');
//    }

    /**
     * 
     * @param string $id
     * @return \BearCMS\Data\Pages\Page|null
     */
    public function get(string $id): ?\BearCMS\Data\Pages\Page
    {
        $data = Internal\Data::getValue('bearcms/pages/page/' . md5($id) . '.json');
        if ($data !== null) {
            return \BearCMS\Data\Pages\Page::fromJSON($data);
        }
        return null;
    }

    /**
     * 
     * @return \BearFramework\Models\ModelsList
     */
    public function getList(): \BearFramework\Models\ModelsList
    {
        $cacheKey = 'pages_list';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $list = Internal\Data::getList('bearcms/pages/page/');
            array_walk($list, function(&$value) {
                $value = \BearCMS\Data\Pages\Page::fromJSON($value);
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
            Internal\Data::$cache[$cacheKey] = $list;
            unset($list);
        }
        return new \BearFramework\Models\ModelsList(Internal\Data::$cache[$cacheKey]);
    }

}
