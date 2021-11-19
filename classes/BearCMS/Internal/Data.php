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
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Data
{

    static $cacheRequests = [];
    static $cache = [];
    static $loadedBundleHash = null;
    static $hasContentChange = false;

    /**
     * 
     * @param string $key
     * @return string
     */
    static function _getGroupValue(string $key): string
    {
        $localCacheKey = 'group-' . $key;
        if (array_key_exists($localCacheKey, self::$cache)) {
            return self::$cache[$localCacheKey];
        }
        $app = App::get();
        $cacheKey = 'bearcms-group-' . $key;
        $data = $app->cache->getValue($cacheKey);
        self::$cacheRequests[] = ['get', 'group', $key];
        if ($data !== null) {
            self::$cache[$localCacheKey] = $data;
            return $data;
        }
        return self::_updateGroupValue($key);
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    static function _updateGroupValue(string $key): string
    {
        $localCacheKey = 'group-' . $key;
        $app = App::get();
        $cacheKey = 'bearcms-group-' . $key;
        $data = md5(uniqid());
        $app->cache->set($app->cache->make($cacheKey, $data));
        self::$cacheRequests[] = ['set', 'group', $key];
        self::$cache[$localCacheKey] = $data;
        return $data;
    }

    /**
     * 
     * @param string $type
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    static function _get(string $type, string $key, callable $callback)
    {
        $localCacheKey = $type . '-' . $key;
        if (array_key_exists($localCacheKey, self::$cache)) {
            return self::$cache[$localCacheKey];
        }
        $app = App::get();
        $cacheKey = 'bearcms-data-' . $key . '-' . self::_getGroupValue('all');
        $cachedValue = $app->cache->getValue($cacheKey);
        self::$cacheRequests[] = ['get', $type, $key];
        if ($cachedValue !== null && is_array($cachedValue) && isset($cachedValue[0]) && $cachedValue[0] === 'bd') {
            if (array_key_exists(1, $cachedValue)) {
                self::$cache[$localCacheKey] = $cachedValue[1];
                return $cachedValue[1];
            }
        }
        $data = $callback();
        $app->cache->set($app->cache->make($cacheKey, ['bd', $data]));
        self::$cacheRequests[] = ['set', $type, $key];
        self::$cache[$localCacheKey] = $data;
        return $data;
    }

    /**
     * 
     * @param string $requestPath
     * @return void
     */
    static function loadCacheBundle(string $requestPath): void
    {
        $app = App::get();
        $cacheKey = 'bearcms-bundle-' . $requestPath . '-' . self::_getGroupValue('all');
        $bundle = $app->cache->getValue($cacheKey);
        if ($bundle !== null) {
            foreach ($bundle[1] as $data) {
                self::$cache[$data[0] . '-' . $data[1]] = $data[2];
                self::$cacheRequests[] = ['bundleget', $data[0], $data[1]];
            }
            self::$loadedBundleHash = $bundle[0];
        }
    }

    /**
     * 
     * @param string $requestPath
     * @return void
     */
    static function saveCacheBundle(string $requestPath): void
    {
        $app = App::get();
        $keys = [];
        foreach (self::$cacheRequests as $requestData) {
            if (strpos($requestData[2], 'bearcms/') === 0) {
                $keys[$requestData[1] . '-' . $requestData[2]] = [$requestData[1], $requestData[2]];
            }
        }
        $keys = array_values($keys);
        $bundle = [];
        foreach ($keys as $keyData) {
            if ($keyData[0] === 'value') {
                $bundle[] = [$keyData[0], $keyData[1], self::getValue($keyData[1])];
            } elseif ($keyData[0] === 'list') {
                $bundle[] = [$keyData[0], $keyData[1], self::getList($keyData[1])];
            }
        }
        $hash = md5(serialize($bundle));
        if (self::$loadedBundleHash !== $hash) {
            $cacheKey = 'bearcms-bundle-' . $requestPath . '-' . self::_getGroupValue('all');
            try {
                $app->cache->set($app->cache->make($cacheKey, [$hash, $bundle]));
            } catch (\Exception $e) {
                // dont care if failed
            }
        }
    }

    /**
     * 
     * @param string $key
     * @return string|null
     */
    static function getValue(string $key): ?string
    {
        return self::_get('value', $key, function () use ($key) {
            $app = App::get();
            return $app->data->getValue($key);
        });
    }

    /**
     * 
     * @param string $prefix
     * @return array
     */
    static function getList(string $prefix): array
    {
        return self::_get('list', $prefix, function () use ($prefix) {
            $found = false;
            if ($prefix === 'bearcms/pages/page/' || $prefix === 'bearcms/blog/post/') {
                $dataBundleID = 'bearcmsdataprefix-' . $prefix;
                $app = App::get();
                if (!$app->dataBundle->exists($dataBundleID)) {
                    //$dir = $app->config->dataDir . '/objects/' . $prefix;
                    $dir = 'appdata://' . $prefix;
                    $itemKeys = [];
                    if (is_dir($dir)) {
                        $keys = scandir($dir);
                        foreach ($keys as $key) {
                            if ($key !== '.' && $key !== '..') {
                                $itemKeys[] = $prefix . $key;
                            }
                        }
                    }
                    $app->dataBundle->create($dataBundleID, $itemKeys);
                }
                $app->dataBundle->prepare($dataBundleID);
                $list = $app->dataBundle->getItemsList($dataBundleID);
                $data = [];
                foreach ($list as $item) {
                    $data[$item->key] = $item->value;
                }
                $found = true;
            }
            if (!$found) {
                $app = App::get();
                //$dir = $app->config->dataDir . '/objects/' . $prefix;
                $dir = 'appdata://' . $prefix;
                $data = [];
                if (is_dir($dir)) {
                    $keys = scandir($dir);
                    foreach ($keys as $key) {
                        if ($key !== '.' && $key !== '..') {
                            $data[$prefix . $key] = file_get_contents($dir . $key);
                        }
                    }
                }
            }
            //                    $list = $app->data->getList()
            //                            ->filterBy('key', $prefix, 'startWith');
            //                    $data = [];
            //                    foreach ($list as $item) {
            //                        $data[] = $item->value;
            //                    }
            return $data;
        });
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    static function onDataChanged(string $key): void
    {
        if (strpos($key, 'bearcms/') === 0) {
            $app = App::get();
            self::$hasContentChange = true;
            self::$cache = [];
            self::_updateGroupValue('all');
            if (strpos($key, 'bearcms/elements/') === 0 || strpos($key, 'bearcms/pages/') === 0 || strpos($key, 'bearcms/blog/') === 0) {
                $app->data->delete('.temp/bearcms/comments-elements-locations');
            }
            if (strpos($key, 'bearcms/pages/') === 0) {
                $prefix = '.temp/bearcms/navigation-element-cache/';
                $list = $app->data->getList()->filterBy('key', $prefix, 'startWith')->sliceProperties(['key']);
                $cacheKeysToDelete = [];
                $dataKeysToDelete = [];
                foreach ($list as $item) {
                    $dataKey = $item->key;
                    $cacheKeysToDelete[] = 'bearcms-navigation-' . str_replace($prefix, '', $dataKey);
                    $dataKeysToDelete[] = $dataKey;
                }
                foreach ($cacheKeysToDelete as $cacheKeyToDelete) {
                    $app->cache->delete($cacheKeyToDelete);
                }
                foreach ($dataKeysToDelete as $dataKeyToDelete) {
                    $app->data->delete($dataKeyToDelete);
                }
            }
            if ($key === 'bearcms/settings.json') {
                Settings::updateIconsDetails();
            }
            if (strpos($key, 'bearcms/elements/element/') === 0) {
                Elements::optimizeElementData($key, false);
            }
        }
    }

    /**
     * 
     * @param string $type
     * @param string $status
     * @param string $authorName
     * @param string $message
     * @param int $pendingApprovalCount
     * @return void
     */
    static function sendNotification(string $type, string $status, string $authorName, string $message, int $pendingApprovalCount): void
    {
        $app = App::get();
        Localization::setAdminLocale();
        $host = $app->request->host;
        if ($status === 'pendingApproval') {
            $title = sprintf(__('bearcms.notifications.' . $type . '.new.pendingApproval'), $host);
            if ($pendingApprovalCount === 1) {
                $text = $authorName . ':' . "\n" . $message;
            } elseif ($pendingApprovalCount === 2) {
                $text = sprintf(__('bearcms.notifications.' . $type . '.new.thisAndOneMoreArePendingApproval'), $authorName);
            } elseif ($pendingApprovalCount > 2) {
                $text = sprintf(__('bearcms.notifications.' . $type . '.new.thisAndSomeMoreArePendingApproval'), $authorName, $pendingApprovalCount);
            }
        } else {
            $title = sprintf(__('bearcms.notifications.' . $type . '.new.notPendingApproval'), $host);
            if ($pendingApprovalCount === 0) {
                $text = $authorName . ':' . "\n" . $message;
            } elseif ($pendingApprovalCount === 1) {
                $text = sprintf(__('bearcms.notifications.' . $type . '.new.oneOtherIsPendingApproval'), $authorName);
            } elseif ($pendingApprovalCount > 1) {
                $text = sprintf(__('bearcms.notifications.' . $type . '.new.manyOthersArePendingApproval'), $authorName, $pendingApprovalCount);
            }
        }
        $notification = $app->notifications->make($title, $text);
        $notification->clickURL = $app->urls->get() . '#admin-open-' . $type;
        $notification->type = 'bearcms-' . $type . '-new';
        $app->notifications->send('bearcms-user-administrator', $notification);
        Localization::restoreLocale();
    }

    static function generateNewFilename(string $filename): string
    {
        $path = pathinfo($filename, PATHINFO_DIRNAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        for ($i = 0; $i < 100; $i++) {
            $newFilename = $path . '/' . md5(uniqid()) . (strlen($extension) > 0 ? '.' . $extension : '');
            if (!is_file($newFilename)) {
                return $newFilename;
            }
        }
        throw new \Exception('Too many reties');
    }

    static function filenameToDataKey(string $filename)
    {
        if (strpos($filename, 'appdata://') === 0) {
            return substr($filename, 10);
        }
        throw new \Exception('The filename provided (' . $filename . ') is not a valid data key');
    }
}
