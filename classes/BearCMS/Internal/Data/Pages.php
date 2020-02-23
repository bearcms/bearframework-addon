<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal;
use BearCMS\Internal\ElementsHelper;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Pages
{

    /**
     * 
     * @param string $status all or published
     * @return array
     */
    static function getPathsList(string $status = 'all'): array
    {
        $list = Internal\Data::getList('bearcms/pages/page/');
        $result = [];
        foreach ($list as $value) {
            $pageData = json_decode($value, true);
            if (
                is_array($pageData) &&
                isset($pageData['id']) &&
                isset($pageData['path']) &&
                isset($pageData['status']) &&
                is_string($pageData['id']) &&
                is_string($pageData['path']) &&
                is_string($pageData['status'])
            ) {
                if ($status !== 'all' && $status !== $pageData['status']) {
                    continue;
                }
                $result[$pageData['id']] = $pageData['path'];
            }
        }
        return $result;
    }

    static function getDataKey(string $id)
    {
        return 'bearcms/pages/page/' . md5($id) . '.json';
    }

    static function getLastModifiedDetails(string $pageID)
    {
        $app = App::get();
        $details = ElementsHelper::getLastModifiedDetails('bearcms-page-' . $pageID);
        $details['dataKeys'][] = self::getDataKey($pageID);
        $details['dataKeys'][] = 'bearcms/settings.json';
        $page = $app->bearCMS->data->pages->get($pageID);
        if ($page !== null) {
            $details['dates'][] = $page->lastChangeTime;
        }
        return $details;
    }

    static function getRawPagesList(): array
    {
        $cacheKey = 'pages_list';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $list = Internal\Data::getList('bearcms/pages/page/');
            array_walk($list, function (&$value) {
                $value = \BearCMS\Data\Pages\Page::fromJSON($value);
            });
            $structureData = Internal\Data::getValue('bearcms/pages/structure.json');
            $structureData = $structureData === null ? [] : json_decode($structureData, true);
            $flattenStructureData = [];
            $flattenStructure = function ($structureData) use (&$flattenStructure, &$flattenStructureData) {
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
            usort($list, function ($object1, $object2) use ($flattenStructureData) {
                return $flattenStructureData[$object1->id] - $flattenStructureData[$object2->id];
            });
            unset($flattenStructureData);
            Internal\Data::$cache[$cacheKey] = $list;
            unset($list);
        }
        return Internal\Data::$cache[$cacheKey];
    }

    static function getPagesList(): \BearFramework\Models\ModelsList
    {
        return new \BearFramework\Models\ModelsList(self::getRawPagesList());
    }

    static function getChildrenList(string $parentID = null): \BearFramework\Models\ModelsList
    {
        $cacheKey = 'pages_children_list';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $list = self::getRawPagesList();
            $result = [];
            foreach ($list as $page) {
                $_parentID = $page->parentID;
                if (!isset($result[$_parentID])) {
                    $result[$_parentID] = [];
                }
                $result[$_parentID][] = $page;
            }
            Internal\Data::$cache[$cacheKey] = $result;
        }
        return new \BearFramework\Models\ModelsList(isset(Internal\Data::$cache[$cacheKey][$parentID]) ? Internal\Data::$cache[$cacheKey][$parentID] : []);
    }
}
