<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal;
use BearCMS\Internal\Config;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Pages
{

    /**
     * 
     * @param string $status
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
                $pageStatus = $pageData['status'];
                if ($pageStatus === 'published') {
                    $pageStatus = 'public';
                } elseif ($pageStatus === 'notPublished') {
                    $pageStatus = 'private';
                }
                $add = false;
                if ($status === 'all') {
                    $add = true;
                } elseif ($status === 'publicOrSecret') {
                    $add = $pageStatus === 'public' || $pageStatus === 'secret';
                } else {
                    $add =  $status === $pageStatus;
                }
                if ($add) {
                    $result[$pageData['id']] = $pageData['path'];
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $pageID
     * @return string
     */
    static private function getPageDataKey(string $pageID): string
    {
        return 'bearcms/pages/page/' . md5($pageID) . '.json';
    }

    /**
     * 
     * @return string
     */
    static private function getStructureDataKey(): string
    {
        return 'bearcms/pages/structure.json';
    }

    /**
     * 
     * @return array
     */
    static function getRawPagesList(): array
    {
        $cacheKey = 'pages_list';
        if (!isset(Internal\Data::$cache[$cacheKey])) {
            $rawList = Internal\Data::getList('bearcms/pages/page/');
            $pages = [];
            foreach ($rawList as $pageJSON) {
                $page = \BearCMS\Data\Pages\Page::fromJSON($pageJSON);
                $pages[$page->id] = $page;
            }
            $structureData = Internal\Data::getValue(self::getStructureDataKey());
            $structureData = $structureData === null ? [] : json_decode($structureData, true);
            $result = [];
            if (isset($pages['home'])) {
                $result[] = $pages['home'];
            } else {
                if (Config::$autoCreateHomePage) {
                    $result[] = self::getDefaultHomePage();
                }
            }
            $walkPages = function ($structureData) use (&$walkPages, &$result, $pages) {
                foreach ($structureData as $item) {
                    $pageID = $item['id'];
                    if (isset($pages[$pageID])) {
                        $result[] = $pages[$pageID];
                    }
                    if (isset($item['children'])) {
                        $walkPages($item['children']);
                    }
                }
            };
            $walkPages($structureData);
            unset($walkPages);
            unset($structureData);
            unset($pages);
            Internal\Data::$cache[$cacheKey] = $result;
            return $result;
        }
        return Internal\Data::$cache[$cacheKey];
    }

    /**
     * 
     * @param string|null $parentID
     * @return \BearFramework\Models\ModelsList
     */
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

    /**
     * 
     * @return \BearCMS\Data\Pages\Page
     */
    static function getDefaultHomePage(): \BearCMS\Data\Pages\Page
    {
        $page = new \BearCMS\Data\Pages\Page();
        $page->id = 'home';
        $page->path = '/';
        $page->status = 'public';
        $data = Internal\Data::getValue('bearcms/settings.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (is_array($data) && isset($data['keywords']) && is_string($data['keywords'])) { // The key is not used since v1.16.
                $page->keywordsTagContent = $data['keywords'];
            }
        }
        return $page;
    }

    /**
     * 
     * @param string $pageID
     * @return array|null
     */
    static function get(string $pageID): ?array
    {
        $data = Internal\Data::getValue(self::getPageDataKey($pageID));
        if ($data !== null) {
            return json_decode($data, true);
        }
        return null;
    }

    /**
     * 
     * @param string $pageID
     * @param array $data
     * @return void
     */
    static function set(string $pageID, array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getPageDataKey($pageID), json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * 
     * @param string $pageID
     * @return void
     */
    static function delete(string $pageID): void
    {
        $app = App::get();
        $app->data->delete(self::getPageDataKey($pageID));
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    static function setStructure(array $data): void
    {
        $app = App::get();
        $structureDataKey = self::getStructureDataKey();
        if (empty($data)) {
            $app->data->delete($structureDataKey);
        } else {
            $app->data->setValue($structureDataKey, json_encode($data, JSON_THROW_ON_ERROR));
        }
    }

    /**
     * 
     * @param string $pageID
     * @return void
     */
    static function deleteImage(string $pageID, bool $updateData): void
    {
        $data = self::get($pageID);
        if ($data !== null) {
            $filename = isset($data['image']) ? (string)$data['image'] : '';
            if (strlen($filename) > 0) {
                $app = App::get();
                $dataKey = Internal\Data::getFilenameDataKey($filename);
                if ($dataKey !== null && $app->data->exists($dataKey)) {
                    $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                }
                UploadsSize::remove($dataKey);
            }
            if ($updateData) {
                $data['image'] = null;
                self::set($pageID, $data);
            }
        }
    }
}
