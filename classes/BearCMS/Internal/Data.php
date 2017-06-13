<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

class Data
{

    static $cacheRequests = [];
    static $cache = [];
    static $loadedBundleHash = null;
    static $hasContentChange = false;

    static function _getGroupValue($key)
    {
        $localCacheKey = 'group-' . $key;
        if (array_key_exists($localCacheKey, self::$cache)) {
            return self::$cache[$localCacheKey];
        }
        $app = App::get();
        $cacheKey = 'bearcms-group-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . $key;
        $data = $app->cache->getValue($cacheKey);
        self::$cacheRequests[] = ['get', 'group', $key];
        if ($data !== null) {
            self::$cache[$localCacheKey] = $data;
            return $data;
        }
        return self::_updateGroupValue($key);
    }

    static function _updateGroupValue($key)
    {
        $localCacheKey = 'group-' . $key;
        $app = App::get();
        $cacheKey = 'bearcms-group-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . $key;
        $data = md5(uniqid());
        $app->cache->set($app->cache->make($cacheKey, $data));
        self::$cacheRequests[] = ['set', 'group', $key];
        self::$cache[$localCacheKey] = $data;
        return $data;
    }

    static function _get(string $type, string $key, callable $callback)
    {
        if (!\BearCMS\Internal\Options::$useDataCache) {
            return $callback();
        }
        $localCacheKey = $type . '-' . $key;
        if (array_key_exists($localCacheKey, self::$cache)) {
            return self::$cache[$localCacheKey];
        }
        $app = App::get();
        $cacheKey = 'bearcms-data-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . $key . '-' . self::_getGroupValue('all');
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

    static function loadCacheBundle($requestPath)
    {
        if (!\BearCMS\Internal\Options::$useDataCache) {
            return;
        }
        $app = App::get();
        $cacheKey = 'bearcms-bundle-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . $requestPath . '-' . self::_getGroupValue('all');
        $bundle = $app->cache->getValue($cacheKey);
        if ($bundle !== null) {
            foreach ($bundle[1] as $data) {
                self::$cache[$data[0] . '-' . $data[1]] = $data[2];
                self::$cacheRequests[] = ['bundleget', $data[0], $data[1]];
            }
            self::$loadedBundleHash = $bundle[0];
        }
    }

    static function saveCacheBundle($requestPath)
    {
        if (!\BearCMS\Internal\Options::$useDataCache) {
            return;
        }
        $app = App::get();
        $keys = [];
        foreach (self::$cacheRequests as $requestData) {
            if (strpos($requestData[2], '.temp/') !== 0) {
                $keys[$requestData[1] . '-' . $requestData[2]] = [$requestData[1], $requestData[2]];
            }
        };
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
            $cacheKey = 'bearcms-bundle-' . \BearCMS\Internal\Options::$dataCachePrefix . '-' . $requestPath . '-' . self::_getGroupValue('all');
            try {
                $app->cache->set($app->cache->make($cacheKey, [$hash, $bundle]));
            } catch (\Exception $e) {
                // dont care if failed
            }
        }
    }

    static function getValue($key)
    {
        return self::_get('value', $key, function() use ($key) {
                    $app = App::get();
                    return $app->data->getValue($key);
                });
    }

    static function getList($prefix)
    {
        return self::_get('list', $prefix, function() use ($prefix) {
                    $found = false;
                    if ($prefix === 'bearcms/pages/page/' || $prefix === 'bearcms/blog/post/') {
                        $dataBundleID = 'bearcmsdataprefix-' . $prefix;
                        $app = App::get();
                        if (!$app->dataBundle->exists($dataBundleID)) {
                            $dir = $app->config->dataDir . '/objects/' . $prefix;
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
                        $dir = $app->config->dataDir . '/objects/' . $prefix;
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

    static function setChanged($key)
    {
        $app = App::get();
        if (strpos($key, '.temp/') !== 0) {
            self::$hasContentChange = true;
        }
        if (\BearCMS\Internal\Options::$useDataCache) {
            self::$cache = [];
            self::_updateGroupValue('all');
        }
        if (strpos($key, 'bearcms/elements/element/') === 0 && $app->hooks->exists('bearCMSElementChanged')) {
            $data = new \ArrayObject();
            $rawElementData = \BearCMS\Internal\Data::getValue($key);
            $elementData = ElementsHelper::decodeElementRawData($rawElementData);
            $data->id = $elementData['id'];
            $data->type = $elementData['type'];
            $data->data = $elementData['data'];
            $app->hooks->execute('bearCMSElementChanged', $data);
        }
    }

}
