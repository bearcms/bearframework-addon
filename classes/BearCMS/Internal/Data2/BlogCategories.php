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
 * @codeCoverageIgnore
 */
class BlogCategories
{

    /**
     * 
     * @param string $rawData
     * @return \BearCMS\Internal\Data2\BlogCategory
     */
    private function makeBlogCategoryFromRawData(string $rawData): \BearCMS\Internal\Data2\BlogCategory
    {
        $rawData = json_decode($rawData, true);
        $blogCategory = new Internal\Data2\BlogCategory();
        $properties = ['id', 'name', 'status'];
        foreach ($properties as $property) {
            if (array_key_exists($property, $rawData)) {
                $blogCategory->$property = $rawData[$property];
            }
        }
        return $blogCategory;
    }

    /**
     * Retrieves information about the page specified
     * 
     * @param string $id The page ID
     * @return \IvoPetkov\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Internal\Data2\BlogCategory
    {
        $data = Internal\Data::getValue('bearcms/blog/categories/category/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeBlogCategoryFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \IvoPetkov\DataList|\BearCMS\Internal\Data2\BlogCategory[] List containing all pages data
     */
    public function getList(): \IvoPetkov\DataList
    {
        $cacheKey = 'blog_categories_list';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $list = Internal\Data::getList('bearcms/blog/categories/category/');
            array_walk($list, function (&$value) {
                $value = $this->makeBlogCategoryFromRawData($value);
            });

            $structureData = Internal\Data::getValue('bearcms/blog/categories/structure.json');
            $structureData = $structureData === null ? [] : json_decode($structureData, true);
            $flattenStructureData = [];
            foreach ($structureData as $itemData) {
                $flattenStructureData[] = $itemData['id'];
            }
            $flattenStructureData = array_flip($flattenStructureData);
            usort($list, function ($object1, $object2) use ($flattenStructureData) {
                return $flattenStructureData[$object1->id] - $flattenStructureData[$object2->id];
            });
            unset($flattenStructureData);
            Internal\Data::$cache[$cacheKey] = $list;
            unset($list);
        }
        return new \IvoPetkov\DataList(Internal\Data::$cache[$cacheKey]);
    }
}
