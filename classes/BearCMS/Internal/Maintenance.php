<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearCMS\Internal\Data\Elements;
use BearCMS\Internal\Data\Settings;
use BearCMS\Internal\Data\UploadsSize;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Maintenance
{

    /**
     * Used by addons
     */
    static $fixUploadsSizeCallbacks = [];

    /**
     * 
     * @param boolean $preview
     * @return array
     */
    static function fixUploadsSize($preview): array
    {

        $app = App::get();
        $bearCMS = $app->bearCMS;

        $files = [];

        $elementsContainersToCheck = [];

        // Pages

        $pages = $bearCMS->data->pages->getList();
        foreach ($pages as $page) {
            $elementsContainersToCheck[] = 'bearcms-page-' . $page->id;
            if (strlen($page->image) > 0) {
                $files[] = str_replace('appdata://', '', $page->image);
            }
        }
        if (array_search('bearcms-page-home', $elementsContainersToCheck) === false) {
            $elementsContainersToCheck[] = 'bearcms-page-home';
        }

        // Blog posts

        $blogPosts = $bearCMS->data->blogPosts->getList();
        foreach ($blogPosts as $blogPost) {
            $elementsContainersToCheck[] = 'bearcms-blogpost-' . $blogPost->id;
            if (strlen($blogPost->image) > 0) {
                $files[] = str_replace('appdata://', '', $blogPost->image);
            }
        }
        if (array_search('bearcms-page-home', $elementsContainersToCheck) === false) {
            $elementsContainersToCheck[] = 'bearcms-page-home';
        }

        // Custom files

        $customFiles = $app->data->getList()->filterBy('key', 'bearcms/files/custom/', 'startWith');
        foreach ($customFiles as $customFile) {
            $files[] = $customFile->key;
        }

        // Elements

        foreach ($elementsContainersToCheck as $elementsContainerID) {
            $files = array_merge($files, Elements::getContainerUploadsSizeItems($elementsContainerID));
        }

        // Items from addons

        foreach (self::$fixUploadsSizeCallbacks as $callback) {
            $files = array_merge($files, call_user_func($callback));
        }

        $currentItems = UploadsSize::getData();

        $foundItems = [];
        foreach ($files as $key) {
            $foundItems[$key] = filesize('appdata://' . $key);
        }

        $itemsToKeep = [];
        $itemsToAdd = [];
        $itemsToDelete = [];
        $itemsToUpdate = [];

        foreach ($foundItems as $key => $size) {
            if (isset($currentItems[$key])) {
                if ($currentItems[$key] !== $size) {
                    $itemsToUpdate[$key] = $size;
                } else {
                    $itemsToKeep[$key] = $size;
                }
            } else {
                $itemsToAdd[$key] = $size;
            }
        }

        foreach ($currentItems as $key => $size) {
            if (!isset($foundItems[$key])) {
                $itemsToDelete[$key] = [$size, $app->data->exists($key) ? 'exists' : 'missing'];
            }
        }

        $result = [
            'mode' => ($preview ? 'Preview mode' : 'Fix mode'),
            'keep' => $itemsToKeep,
            'add' => $itemsToAdd,
            'delete' => $itemsToDelete,
            'update' => $itemsToUpdate
        ];

        if (!$preview) {
            foreach ($itemsToAdd as $key => $size) {
                UploadsSize::add($key, $size);
            }

            foreach ($itemsToUpdate as $key => $size) {
                UploadsSize::add($key, $size);
            }

            $recycleBinPrefix = '.recyclebin/bearcms/maintenance-' . str_replace('.', '-', microtime(true)) . '/';
            foreach ($itemsToDelete as $key => $deleteData) {
                if ($app->data->exists($key)) {
                    $app->data->rename($key, $recycleBinPrefix . $key);
                }
                UploadsSize::remove($key);
            }
        }

        return $result;
    }

    /**
     * 
     * @param boolean $preview
     * @return array
     */
    static function optimizeSettings(bool $preview): array
    {
        $result = [];
        $result['icons'] = Settings::updateIconsDetails($preview);
        return $result;
    }

    /**
     * 
     * @param boolean $preview
     * @return array
     */
    static function optimizeElements(bool $preview): array
    {
        $result = [];
        $app = App::get();
        $list = $app->data->getList()
            ->filterBy('key', 'bearcms/elements/element/', 'startWith')
            ->sliceProperties(['key']);
        foreach ($list as $item) {
            $dataKey = $item->key;
            $optimizationResult = Elements::optimizeElementData($dataKey, $preview);
            if (!empty($optimizationResult)) {
                $result[$dataKey] = $optimizationResult;
            }
        }
        return $result;
    }

    /**
     * 
     * @param boolean $preview
     * @return array
     */
    static function optimizeData(bool $preview): array
    {
        $result = [];
        $result['settings'] = self::optimizeSettings($preview);
        $result['elements'] = self::optimizeElements($preview);
        return $result;
    }
}
