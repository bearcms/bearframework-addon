<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Config;
use BearCMS\Internal;
use BearCMS\Internal2;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Themes
{

    static $elementsOptions = [];
    static $pagesOptions = [];
    static $registrations = [];
    static $cache = [];

    /**
     * 
     * @return string
     */
    static function getActiveThemeID(): string
    {
        $data = Internal\Data::getValue('bearcms/themes/active.json');
        if ($data !== null) {
            $data = json_decode($data, true);
            if (isset($data['id'])) {
                return $data['id'];
            }
        }
        if (strlen(Config::$defaultThemeID) > 0) {
            return Config::$defaultThemeID;
        }
        return 'none';
    }

    /**
     * 
     * @return array
     */
    static public function getIDs(): array
    {
        return array_keys(self::$registrations);
    }

    /**
     * 
     * @param string $id
     * @return \BearCMS\Themes\Theme|null
     */
    static function get(string $id): ?\BearCMS\Themes\Theme
    {
        if (isset(self::$registrations[$id])) {
            if (is_callable(self::$registrations[$id])) {
                $app = App::get();
                $theme = new \BearCMS\Themes\Theme($id);
                call_user_func(self::$registrations[$id], $theme);
                self::$registrations[$id] = $theme;
            }
            return self::$registrations[$id];
        }
        return null;
    }

    /**
     * 
     * @param string $id
     * @return void
     */
    static function initialize(string $id): void
    {
        $theme = Internal\Themes::get($id);
        if ($theme instanceof \BearCMS\Themes\Theme && is_callable($theme->initialize)) {
            $app = App::get();
            $currentUserID = $app->bearCMS->currentUser->exists() ? $app->bearCMS->currentUser->getID() : null;
            $currentCustomizations = Internal\Themes::getCustomizations($id, $currentUserID);
            call_user_func($theme->initialize, $currentCustomizations);
        }
    }

    /**
     * 
     * @param string $id
     * @return string|null
     */
    static function getVersion(string $id): ?string
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        return $theme->version;
    }

    /**
     * 
     * @param string $id
     * @param bool $updateMediaFilenames
     * @return array|null
     * @throws \Exception
     */
    static function getManifest(string $id, bool $updateMediaFilenames = true): ?array
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        if (is_callable($theme->manifest)) {
            $app = App::get();
            $context = $app->contexts->get(__FILE__);
            $manifest = call_user_func($theme->manifest);
            if ((!$manifest instanceof \BearCMS\Themes\Theme\Manifest)) {
                throw new \Exception('Invalid theme manifest value for theme ' . $id . '!');
            }
            if ($manifest->name === null) {
                $manifest->name = '';
            }
            if ($manifest->description === null) {
                $manifest->description = '';
            }
            $result = $manifest->toArray();
            if ($updateMediaFilenames) {
                if (isset($result['media']) && is_array($result['media'])) {
                    foreach ($result['media'] as $i => $mediaItem) {
                        if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                            $result['media'][$i]['filename'] = $context->dir . '/assets/tm/' . md5($id) . '/' . md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION);
                        }
                    }
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
//    public function getManifest2(string $id): ?array
//    {
//        if (!isset(self::$registrations[$id])) {
//            return null;
//        }
//        $result = Internal\Themes::getManifest($id);
//        $styles = Internal\Themes::getStyles($id);
//        $result['styles'] = [];
//        foreach ($styles as $style) {
//            $result['styles'][] = [
//                'id' => $style['id'],
//                'name' => $style['name'],
//                'media' => $style['media']
//            ];
//        }
//        return $result;
//    }

    /**
     * 
     * @param string $id
     * @return \BearCMS\Themes\Theme\Options|null
     * @throws \Exception
     */
    static function getOptions(string $id): ?\BearCMS\Themes\Theme\Options
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        if (is_callable($theme->options)) {
            $options = call_user_func($theme->options);
            if ($options !== null && !($options instanceof \BearCMS\Themes\Theme\Options)) {
                throw new \Exception('Invalid theme options value for theme ' . $id . '!');
            }
            return $options;
        }
        return null;
    }

    /**
     * 
     * @param string $id
     * @return array
     * @throws \Exception
     */
    static function getOptionsAsArray(string $id): array
    {
        $options = self::getOptions($id);
        if ($options === null) {
            return [];
        }
        $walkOptions = function(array $options) use (&$walkOptions) {
            $result = [];
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    $item = array_merge($option->details, [
                        "id" => $option->id,
                        "type" => $option->type,
                        "name" => $option->name
                    ]);
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    $item = [
                        "type" => "group",
                        "name" => $option->name,
                        "description" => $option->description,
                    ];
                    $item['options'] = $walkOptions($option->getList());
                }
                $result[] = $item;
            }
            return $result;
        };
        return $walkOptions($options->getList());
    }

    /**
     * 
     * @param string $id
     * @param type $updateMediaFilenames
     * @return array|null
     * @throws \Exception
     */
    static function getStyles(string $id, $updateMediaFilenames = true): ?array
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        if (is_callable($theme->styles)) {
            $styles = call_user_func($theme->styles);
            if (!is_array($styles)) {
                throw new \Exception('Invalid theme styles value for theme ' . $id . '!');
            }
            $result = [];
            foreach ($styles as $j => $style) {
                if (!($style instanceof \BearCMS\Themes\Theme\Style)) {
                    throw new \Exception('Invalid theme style at index ' . $j . '!');
                }
                if (strlen($style->id) === 0) {
                    $style->id = 'style' . $j;
                }
                if ($style->name == null) {
                    $style->name = '';
                }
                $result[] = $style->toArray();
            }
            if ($updateMediaFilenames) {
                $app = App::get();
                $context = $app->contexts->get(__FILE__);
                foreach ($result as $j => $style) {
                    if (isset($style['media']) && is_array($style['media'])) {
                        foreach ($style['media'] as $i => $mediaItem) {
                            if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                                $result[$j]['media'][$i]['filename'] = $context->dir . '/assets/tm/' . md5($id) . '/' . md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION);
                            }
                        }
                    }
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    static public function getStyleValues(string $id, string $styleID): ?array
    {
        if (!isset(self::$registrations[$id])) {
            return null;
        }
        $styles = Internal\Themes::getStyles($id);
        foreach ($styles as $style) {
            if ($style['id'] === $styleID) {
                if (isset($style['values'])) {
                    return $style['values'];
                }
            }
        }
        return [];
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return \BearCMS\Themes\Theme\Customizations|null
     */
    static public function getCustomizations(string $id, string $userID = null): ?\BearCMS\Themes\Theme\Customizations
    {
        if (!isset(self::$registrations[$id])) {
            return null;
        }
        $localCacheKey = 'options-' . $id . '-' . $userID;
        if (!isset(self::$cache[$localCacheKey])) {
            $app = App::get();
            $cacheKey = Internal\Themes::getCacheItemKey($id, $userID);
            $envKey = md5(serialize(array_keys(self::$elementsOptions)) . serialize(array_keys(self::$pagesOptions)) . '-v2');
            $useCache = $cacheKey !== null;
            $resultData = null;
            if ($useCache) {
                $resultData = $app->cache->getValue($cacheKey);
                if ($resultData !== null) {
                    $resultData = json_decode($resultData, true);
                }
            }
            if ($resultData === null || !isset($resultData[3]) || $resultData[3] !== $envKey) {
                $values = [];
                $html = '';
                $assetsToUpdate = [];
                $currentValues = null;
                if ($userID !== null) {
                    $userOptions = Internal2::$data2->themes->getUserOptions($id, $userID);
                    if (is_array($userOptions)) {
                        $currentValues = $userOptions;
                    }
                }
                if ($currentValues === null) {
                    $currentValues = Internal2::$data2->themes->getValues($id);
                }
                $themeOptions = Internal\Themes::getOptions($id);
                if ($themeOptions !== null) {
                    $updateAppDataKey = function($filename) {
                        if (substr($filename, 0, 5) === 'data:') {
                            return 'appdata://' . substr($filename, 5);
                        }
                        return $filename;
                    };
                    $walkOptions = function(array $options) use (&$walkOptions, $currentValues, &$values, &$assetsToUpdate, $updateAppDataKey) {
                        foreach ($options as $option) {
                            if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                                $optionID = $option->id;
                                $optionType = $option->type;
                                $value = isset($currentValues[$optionID]) ? $currentValues[$optionID] : (isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value']) : $option->details['value']) : null);
                                if (strlen($value) > 0) {
                                    if ($optionType === 'image') {
                                        $value = $updateAppDataKey($value);
                                        $assetsToUpdate[] = $value;
                                    } elseif ($optionType === 'css' || $optionType === 'cssBackground') {
                                        if (strpos($value, 'url') !== false) {
                                            $temp = json_decode($value, true);
                                            if (is_array($temp)) {
                                                $hasChange = false;
                                                foreach ($temp as $_key => $_value) {
                                                    $matches = [];
                                                    preg_match_all('/url\((.*?)\)/', $_value, $matches);
                                                    if (!empty($matches[1])) {
                                                        $temp2 = array_unique($matches[1]);
                                                        foreach ($temp2 as $_value2) {
                                                            $updatedValue2 = $updateAppDataKey($_value2);
                                                            $temp[$_key] = str_replace($_value2, $updatedValue2, $temp[$_key]);
                                                            $assetsToUpdate[] = $updatedValue2;
                                                        }
                                                        $hasChange = true;
                                                    }
                                                }
                                                if ($hasChange) {
                                                    $value = json_encode($temp);
                                                }
                                            }
                                        }
                                    }
                                }
                                $values[$optionID] = $value;
                            } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                                $walkOptions($option->getList());
                            }
                        }
                    };
                    $walkOptions($themeOptions->getList());
                    $themeOptions->setValues($values);
                    $html = $themeOptions->getHTML();
                }
                $assetsToUpdate = array_unique($assetsToUpdate);
                if ($useCache) {
                    $app->cache->set($app->cache->make($cacheKey, json_encode([$values, $html, $assetsToUpdate, $envKey])));
                }
            } else {
                $values = $resultData[0];
                $html = $resultData[1];
                $assetsToUpdate = $resultData[2];
            }

            if (!empty($assetsToUpdate)) {
                $search = [];
                $replace = [];
                foreach ($assetsToUpdate as $filename) {
                    $newFileName = Internal2::$data2->getRealFilename($filename);
                    if ($newFileName !== null) {
                        $url = $app->assets->getURL($newFileName, ['cacheMaxAge' => 999999999]);
                        $search[] = $filename;
                        $replace[] = $url;
                        $search[] = trim(json_encode($filename), '"');
                        $replace[] = trim(json_encode($url), '"');
                    }
                }
                $html = str_replace($search, $replace, $html);
            }
            self::$cache[$localCacheKey] = new \BearCMS\Themes\Theme\Customizations($values, $html);
        }
        return self::$cache[$localCacheKey];
    }

    /**
     * 
     * @param string $id The theme ID
     * @return string
     * @throws \Exception
     */
    static public function export(string $id): string
    {
        if (!isset(self::$registrations[$id])) {
            throw new \Exception('Theme does not exists!');
        }
        $app = App::get();
        $customizations = self::getCustomizations($id);
        $values = $customizations->getValues();
        $filesToAttach = [];
        $filesInValues = Internal\Themes::getFilesInValues($values);
        $filesKeysToUpdate = [];
        foreach ($filesInValues as $filename) {
            $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($filename, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
            $filesToAttach[$attachmentName] = $filename;
            $filesKeysToUpdate[$filename] = 'data:' . $attachmentName;
        }
        $values = Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);

        $manifest = [
            'themeID' => $id,
            'exportDate' => date('c')
        ];

        $archiveFileDataKey = '.temp/bearcms/themeexport/theme-export-' . md5(uniqid()) . '.zip';
        $archiveFilename = $app->data->getFilename($archiveFileDataKey);
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-export-' . uniqid() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename, \ZipArchive::CREATE) === true) {
            $zip->addFromString('manifest.json', json_encode($manifest));
            $zip->addFromString('values.json', json_encode($values));
            foreach ($filesToAttach as $attachmentName => $filename) {
                $newFileName = Internal2::$data2->getRealFilename($filename);
                if ($newFileName !== null) {
                    $filename = $newFileName;
                }
                $zip->addFromString($attachmentName, file_get_contents($filename));
            }
            $zip->close();
        } else {
            throw new \Exception('Cannot open zip archive (' . $tempArchiveFilename . ')');
        }
        copy($tempArchiveFilename, $archiveFilename);
        unlink($tempArchiveFilename);
        return $archiveFileDataKey;
    }

    /**
     * 
     * @param string $fileDataKey The import file data key
     * @param string $id The theme ID
     * @param string $userID The user ID
     * @throws \Exception
     */
    static public function import(string $fileDataKey, string $id, string $userID = null): void
    {
        if (!isset(self::$registrations[$id])) {
            throw new \Exception('Theme does not exists!', 1);
        }
        $app = App::get();
        if (!$app->data->exists($fileDataKey)) {
            throw new \Exception('Import file not found!', 2);
        }
        $hasUser = strlen($userID) > 0;
        $archiveFilename = $app->data->getFilename($fileDataKey);
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-import-' . uniqid() . '.zip';
        copy($archiveFilename, $tempArchiveFilename);
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename) === true) {

            $getManifest = function() use ($zip) {
                $data = $zip->getFromName('manifest.json');
                if (strlen($data) > 0) {
                    $data = json_decode($data, true);
                    if (is_array($data) && isset($data['themeID']) && is_string($data['themeID'])) {
                        return $data;
                    }
                }
                throw new \Exception('The manifest file is not valid!', 3);
            };
            $manifest = $getManifest();

            if ($manifest['themeID'] !== $id) { // cannot import options to different theme
                throw new \Exception('The import file is for different theme (' . $manifest['themeID'] . ')', 4);
            }

            $getValues = function() use ($zip) {
                $data = $zip->getFromName('values.json');
                if (strlen($data) > 0) {
                    $data = json_decode($data, true);
                    if (is_array($data)) {
                        return $data;
                    }
                }
                throw new \Exception('The values file is not valid!', 5);
            };
            $values = $getValues();

            $filesInValues = Internal\Themes::getFilesInValues($values);
            $filesKeysToUpdate = [];
            foreach ($filesInValues as $key) {
                if (strpos($key, 'data:files/') !== 0) {
                    throw new \Exception('Invalid file (' . $key . ')!', 6);
                }
                $filename = substr($key, 5);
                $data = $zip->getFromName($filename);
                if ($data !== false) {
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    if (array_search($extension, ['jpg', 'jpeg', 'gif', 'png']) === false) {
                        throw new \Exception('Invalid file (' . $filename . ')!', 9);
                    }
                    $dataKey = ($hasUser ? '.temp/bearcms/files/themeimage/' : 'bearcms/files/themeimage/') . md5($filename . '-' . uniqid()) . '.' . $extension;
                    $app->data->setValue($dataKey, $data);
                    $filesKeysToUpdate[$key] = 'data:' . $dataKey;
                    $isInvalid = false;
                    try {
                        $details = $app->assets->getDetails($app->data->getFilename($dataKey), ['width', 'height']);
                        $size = [$details['width'], $details['height']];
                        if ($size[0] <= 0 || $size[1] <= 0) {
                            $isInvalid = true;
                        }
                    } catch (\Exception $e) {
                        $isInvalid = true;
                    }

                    if ($isInvalid) {
                        foreach ($filesKeysToUpdate as $dataKeyWithPrefix) { // remove previously added files
                            $app->data->delete(substr($dataKeyWithPrefix, 5));
                        }
                        throw new \Exception('Invalid file (' . $key . ')!', 7);
                    }
                    //$app->data->makePublic($dataKey);
                }
            }

            $values = Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);
            if ($hasUser) {
                Internal2::$data2->themes->setUserOptions($id, $userID, $values);
            } else {
                Internal2::$data2->themes->setOptions($id, $values);
            }
            self::$cache = [];

            $zip->close();
            unlink($tempArchiveFilename);
        } else {
            unlink($tempArchiveFilename);
            throw new \Exception('Cannot open zip archive (' . $tempArchiveFilename . ')', 8);
        }
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return void
     */
    static public function applyUserValues(string $id, string $userID): void
    {
        $app = App::get();
        $values = Internal2::$data2->themes->getUserOptions($id, $userID);
        if (is_array($values)) {
            $filesInValues = Internal\Themes::getFilesInValues($values);
            $filesKeysToUpdate = [];
            foreach ($filesInValues as $key) {
                if (strpos($key, 'data:') === 0) {
                    $dataKay = substr($key, 5);
                    if (strpos($dataKay, '.temp/bearcms/files/themeimage/') === 0) {
                        $newDataKey = 'bearcms/files/themeimage/' . pathinfo($dataKay, PATHINFO_BASENAME);
                        $app->data->duplicate($dataKay, $newDataKey); // setValues() will remove the files in the user options 
                        $filesKeysToUpdate['data:' . $dataKay] = 'data:' . $newDataKey;
                    }
                }
            }
            $values = Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);
            Internal2::$data2->themes->setOptions($id, $values);
            Internal2::$data2->themes->discardUserOptions($id, $userID);
            self::$cache = [];
        }
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return string|null
     */
    static public function getCacheItemKey(string $id, string $userID = null): ?string
    {
        $version = self::getVersion($id);
        if ($version === null) {
            return null;
        }
        return 'bearcms-theme-options-' . md5($id) . '-' . md5($version) . '-' . md5($userID) . '-6';
    }

    /**
     * 
     * @param array $values
     * @return array
     */
    static public function getFilesInValues(array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $matches = [];
            preg_match_all('/url\((.*?)\)/', $value, $matches);
            if (!empty($matches[1])) {
                $matches[1] = array_unique($matches[1]);
                foreach ($matches[1] as $key) {
                    $jsJsonEncoded = is_array(json_decode($value, true));
                    $result[] = $jsJsonEncoded ? json_decode('"' . $key . '"') : $key;
                }
            }
            if (strpos($value, 'appdata://') === 0 || strpos($value, 'data:') === 0 || strpos($value, 'addon:') === 0) { //strpos($value, 'app:') === 0 || 
                $result[] = $value;
            }
        }
        return array_values(array_unique($result));
    }

    /**
     * 
     * @param array $values
     * @param array $keysToUpdate
     * @return array
     */
    static public function updateFilesInValues(array $values, array $keysToUpdate): array
    {
        if (!empty($keysToUpdate)) {
            $search = [];
            $replace = [];
            foreach ($keysToUpdate as $oldKey => $newKey) {
                $search[] = 'url(' . $oldKey . ')';
                $replace[] = 'url(' . $newKey . ')';
                $search[] = trim(json_encode('url(' . $oldKey . ')'), '"');
                $replace[] = trim(json_encode('url(' . $newKey . ')'), '"');
            }
            foreach ($values as $name => $value) {
                $values[$name] = str_replace($search, $replace, $values[$name]);
            }
            foreach ($values as $name => $value) {
                if (isset($keysToUpdate[$value])) {
                    $values[$name] = $keysToUpdate[$value];
                }
            }
        }
        return $values;
    }

}
