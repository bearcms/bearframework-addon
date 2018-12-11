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
class ForumCategories
{

    static private $cache = [];

    private function makeForumCategoryFromRawData($rawData): \BearCMS\Internal\Data2\ForumCategory
    {
        $rawData = json_decode($rawData, true);
        $forumCategory = new Internal\Data2\ForumCategory();
        $properties = ['id', 'name', 'status'];
        foreach ($properties as $property) {
            if (array_key_exists($property, $rawData)) {
                $forumCategory->$property = $rawData[$property];
            }
        }
        return $forumCategory;
    }

    /**
     * Retrieves information about the page specified
     * 
     * @param string $id The page ID
     * @return \IvoPetkov\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\ForumCategory
    {
        $data = Internal\Data::getValue('bearcms/forums/categories/category/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeForumCategoryFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \IvoPetkov\DataList|\BearCMS\Internal\Data2\ForumCategory[] List containing all pages data
     */
    public function getList(): \IvoPetkov\DataList
    {
        if (!isset(self::$cache['list'])) {
            $list = Internal\Data::getList('bearcms/forums/categories/category/');
            array_walk($list, function(&$value) {
                $value = $this->makeForumCategoryFromRawData($value);
            });

            $structureData = Internal\Data::getValue('bearcms/forums/categories/structure.json');
            $structureData = $structureData === null ? [] : json_decode($structureData, true);
            $flattenStructureData = [];
            foreach ($structureData as $itemData) {
                $flattenStructureData[] = $itemData['id'];
            }
            $flattenStructureData = array_flip($flattenStructureData);
            usort($list, function($object1, $object2) use ($flattenStructureData) {
                return $flattenStructureData[$object1->id] - $flattenStructureData[$object2->id];
            });
            unset($flattenStructureData);
            self::$cache['list'] = $list;
            unset($list);
        }
        return new \IvoPetkov\DataList(self::$cache['list']);
    }

}
