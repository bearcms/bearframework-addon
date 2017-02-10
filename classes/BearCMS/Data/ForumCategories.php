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
class ForumCategories
{

    private static $listDataCache = null;

    private function makeForumCategoryFromRawData($rawData): \BearCMS\Data\ForumCategory
    {
        $rawData = json_decode($rawData, true);
        $forumCategory = new \BearCMS\Data\ForumCategory();
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
     * @return \BearCMS\DataObject|null The page data or null if page not found
     * @throws \InvalidArgumentException
     */
    public function get(string $id): ?\BearCMS\Data\ForumCategory
    {
        $app = App::get();
        $data = $app->data->getValue('bearcms/forum/categories/category/' . md5($id) . '.json');
        if ($data !== null) {
            return $this->makeForumCategoryFromRawData($data);
        }
        return null;
    }

    /**
     * Retrieves a list of all pages
     * 
     * @return \BearCMS\DataList|\BearCMS\Data\ForumCategory[] List containing all pages data
     */
    public function getList(): \BearCMS\DataList
    {
        $app = App::get();
        if (self::$listDataCache === null) {
            $list = $app->data->getList()
                    ->filterBy('key', 'bearcms/forum/categories/category/', 'startWith');
            self::$listDataCache = [];
            foreach ($list as $item) {
                self::$listDataCache[] = $this->makeForumCategoryFromRawData($item->value);
            }
            $structureData = $app->data->getValue('bearcms/forum/categories/structure.json');
            $flattenStructureData = [];
            foreach ($structureData as $itemData) {
                $flattenStructureData[] = $itemData['id'];
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
