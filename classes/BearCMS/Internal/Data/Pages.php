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
            $rawList = Internal\Data::getList('bearcms/pages/page/');
            $pages = [];
            foreach ($rawList as $pageJSON) {
                $page = \BearCMS\Data\Pages\Page::fromJSON($pageJSON);
                $pages[$page->id] = $page;
            }
            $structureData = Internal\Data::getValue('bearcms/pages/structure.json');
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

    static function getDefaultHomePage()
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

    static function onCreatePage(string $pageID): void
    {
        $app = App::get();
        $page = $app->bearCMS->data->pages->get($pageID);
        if ($page === null) {
            return;
        }
        $containerID = 'bearcms-page-' . $pageID;
        $containerData = ElementsHelper::getContainerData($containerID);
        if (empty($containerData['elements'])) {
            $containerData['id'] = $containerID;

            $elementID = ElementsHelper::generateElementID('np');
            $containerData['elements'][] = ['id' => $elementID];

            $elementData = [
                'id' => $elementID,
                'type' => 'heading',
                'data' => [
                    'text' => $page->name,
                    'size' => 'large'
                ],
                'lastChangeTime' => time()
            ];

            $app->data->setValue('bearcms/elements/element/' . md5($elementData['id']) . '.json', json_encode($elementData));
            $app->data->setValue('bearcms/elements/container/' . md5($containerData['id']) . '.json', json_encode($containerData));
        }
    }
}
