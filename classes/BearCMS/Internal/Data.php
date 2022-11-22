<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Data\UploadsSize;
use BearCMS\Internal\ImportExport\ImportContext;

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

    /**
     * 
     * @param string $filename
     * @param mixed|null $context If provided it will be used to create a persistent filename, but it will generate error if the file exists
     * @return string
     */
    static function generateNewFilename(string $filename, $context = null): string
    {
        $path = pathinfo($filename, PATHINFO_DIRNAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($context !== null) {
            $newFilename = $path . '/' . md5(serialize($context)) . (strlen($extension) > 0 ? '.' . $extension : '');
            if (is_file($newFilename)) {
                throw new \Exception('The filename generated is taken (' . $newFilename . ')');
            }
            return $newFilename;
        }
        for ($i = 0; $i < 100; $i++) {
            $newFilename = $path . '/' . md5(uniqid()) . (strlen($extension) > 0 ? '.' . $extension : '');
            if (!is_file($newFilename)) {
                return $newFilename;
            }
        }
        throw new \Exception('Too many reties');
    }

    /**
     * 
     * @param string $filename
     * @return string|null
     */
    static function getFilenameDataKey(string $filename): ?string
    {
        $filename = self::removeFilenameOptions($filename);
        if (strpos($filename, 'data:') === 0) {
            return substr($filename, 5);
        }
        if (strpos($filename, 'appdata://') === 0) {
            return substr($filename, 10);
        }
        return null;
        //throw new \Exception('The filename provided (' . $filename . ') is not a valid data key!');
    }

    /**
     * 
     * @param string $filename
     * @return string|null
     */
    static function getFilenameExtension(string $filename): ?string
    {
        $filename = self::removeFilenameOptions($filename);
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * Converts filenames that start with "data:" or "addon:id:" to real/absolute ones.
     * 
     * @param string $filename The filename to convert.
     * @param boolean $isLocal If TRUE the result filename will be required to be in the data repository or in an addon.
     * @return string|null The real filename or NULL if $isLocal is TRUE.
     */
    static function getRealFilename(string $filename, bool $isLocal = false): ?string
    {
        if (substr($filename, 0, 10) === 'appdata://') {
            return $filename;
        }
        if (substr($filename, 0, 5) === 'data:') {
            return 'appdata://' . substr($filename, 5);
        }
        if (substr($filename, 0, 6) === 'addon:') {
            $temp = explode(':', $filename, 3);
            if (sizeof($temp) === 3) {
                $addon = \BearFramework\Addons::get($temp[1]);
                if ($addon !== null) {
                    return $addon->dir . '/' . $temp[2];
                }
            }
        }
        if ($isLocal) {
            return null;
        }
        return $filename;
    }

    /**
     * Converts real/absolute filenames to ones that start with "data:" or "addon:id:".
     * 
     * @param string $filename The filename to convert.
     * @param boolean $isLocal If TRUE the result filename will be required to be in the data repository or in an addon.
     * @return string|null The short filename or NULL if $isLocal is TRUE.
     */
    static function getShortFilename(string $filename, bool $isLocal = false): ?string
    {
        if (substr($filename, 0, 5) === 'data:') {
            return $filename;
        }
        if (substr($filename, 0, 10) === 'appdata://') {
            return 'data:' . substr($filename, 10);
        }
        $addonsList = \BearFramework\Addons::getList();
        foreach ($addonsList as $addon) {
            if (strpos($filename, $addon->dir) === 0) {
                return 'addon:' . $addon->id . substr($filename, strlen($addon->dir));
            }
        }
        if ($isLocal) {
            return null;
        }
        return $filename;
    }

    /**
     * 
     * @param string $filename Filename in format appdata://file.png?opt1=value1&opt2=value2
     * @return array
     */
    static function getFilenameOptions(string $filename): array
    {
        $query = parse_url($filename, PHP_URL_QUERY);
        if ($query === null) {
            return [];
        }
        $result = [];
        parse_str($query, $result);
        return $result;
    }

    /**
     * 
     * @param string $filename Filename in format appdata://file.png?opt1=value1&opt2=value2
     * @return string Returns the updated filename
     */
    static function removeFilenameOptions(string $filename): string
    {
        $index = strpos($filename, '?');
        return $index !== false ? substr($filename, 0, $index) : $filename;
    }

    /**
     * 
     * @param string $filename
     * @param array $options
     * @return string
     */
    static function setFilenameOptions(string $filename, array $options): string
    {
        $currentOptions = self::getFilenameOptions($filename);
        if (!empty($currentOptions)) {
            $options = array_merge($currentOptions, $options);
            $filename = self::removeFilenameOptions($filename);
        }
        return $filename . (empty($options) ? '' : '?' . http_build_query($options));
    }

    /**
     * Will be moved to the recycle bin and removed from UploadsSize
     * 
     * @param string $filename Can start with appdata:// or data: and can have options
     * @return void
     */
    static function deleteElementAsset(string $filename): void
    {
        if (strlen($filename) === 0) {
            return;
        }
        $app = App::get();
        $dataKey = self::getFilenameDataKey($filename);
        if ($dataKey !== null && $app->data->exists($dataKey)) {
            $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
        }
        UploadsSize::remove($dataKey);
    }

    /**
     * Will make a copy and add to UploadsSize
     *
     * @param string $filename Can start with appdata:// or data: and can have options
     * @return string Returns the new filename
     */
    static function duplicateElementAsset(string $filename): string
    {
        $filenameOptions = self::getFilenameOptions($filename);
        $filenameWithoutOptions = self::removeFilenameOptions($filename);
        $realFilenameWithoutOptions = self::getRealFilename($filenameWithoutOptions);
        $newRealFilenameWithoutOptions = self::generateNewFilename($realFilenameWithoutOptions);
        $newRealFilenameWithOptions = self::setFilenameOptions($newRealFilenameWithoutOptions, $filenameOptions);
        $newFilenameDataKey = self::getFilenameDataKey($newRealFilenameWithoutOptions);
        $newFilenameWithOptions = self::getShortFilename($newRealFilenameWithOptions);
        copy($realFilenameWithoutOptions, $newRealFilenameWithoutOptions);
        UploadsSize::add($newFilenameDataKey, filesize($newRealFilenameWithoutOptions));
        return $newFilenameWithOptions;
    }

    /**
     * 
     * @param string $filename Can start with appdata:// or data: and can have options
     * @param string $newBasename The filename (without extension)
     * @param callable $exportAddCallback
     * @return string Returns the new filename
     */
    static function exportElementAsset(string $filename, string $newFilenamePrefix, callable $exportAddCallback): string
    {
        $filenameOptions = self::getFilenameOptions($filename);
        $filenameWithoutOptions = self::removeFilenameOptions($filename);
        $realFilenameWithoutOptions = self::getRealFilename($filenameWithoutOptions);
        $newFilenameWithoutOptions = $newFilenamePrefix . '.' . self::getFilenameExtension($filename);
        $newFilenameWithOptions = self::setFilenameOptions($newFilenameWithoutOptions, $filenameOptions);
        $exportAddCallback($newFilenameWithoutOptions, file_get_contents($realFilenameWithoutOptions));
        return $newFilenameWithOptions;
    }

    /**
     * 
     * @param string $filename Can start with appdata:// or data: and can have options
     * @param string $dataKeyPrefix
     * @param ImportContext $context
     * @return string|null Returns the new filename
     */
    static function importElementAsset(string $filename, string $dataKeyPrefix, ImportContext $context): ?string
    {
        $app = App::get();
        $content = $context->getValue($filename);
        if ($content !== null) {
            $filenameOptions = self::getFilenameOptions($filename);
            $newRealFilename = self::generateNewFilename($app->data->getFilename($dataKeyPrefix . 'file.' . self::getFilenameExtension($filename)));
            $newRealFilenameWithOptions = self::setFilenameOptions($newRealFilename, $filenameOptions);
            $newRealFilenameDataKey = self::getFilenameDataKey($newRealFilename);
            $newRealFilenameFileSize = strlen($content);
            $newFilenameWithOptions = self::getShortFilename($newRealFilenameWithOptions);
            if ($context->isExecuteMode()) {
                file_put_contents($newRealFilename, $content);
                UploadsSize::add($newRealFilenameDataKey, $newRealFilenameFileSize);
            }
            $context->logChange('elementFilesAdd', ['dataKey' => $newRealFilenameDataKey]);
            $context->logChange('uploadsSizeAdd', ['key' => $newRealFilenameDataKey, 'size' => $newRealFilenameFileSize]);
            return $newFilenameWithOptions;
        } else {
            $context->logWarning('File not found in archive (' . $filename . ')');
            return null;
        }
    }
}
