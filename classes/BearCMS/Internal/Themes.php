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
 */
class Themes
{

    static $elementsOptions = [];
    static $pagesOptions = [];
    static $announcements = [];
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
        return array_keys(self::$announcements);
    }

    /**
     * 
     * @param string $id
     * @return \BearCMS\Themes\Theme|null
     */
    static function get(string $id): ?\BearCMS\Themes\Theme
    {
        if (isset(self::$announcements[$id])) {
            if (is_callable(self::$announcements[$id])) {
                $app = App::get();
                $theme = new \BearCMS\Themes\Theme($id);
                call_user_func(self::$announcements[$id], $theme);
                $app->hooks->execute('bearCMSThemeRequested', $theme);
                self::$announcements[$id] = $theme;
            }
            return self::$announcements[$id];
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
            $currentThemeOptions = Internal\Themes::getOptions($id, $currentUserID);
            call_user_func($theme->initialize, $currentThemeOptions);
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
            $context = $app->context->get(__FILE__);
            $result = call_user_func($theme->manifest);
            if (!is_array($result)) {
                throw new \Exception('Invalid theme manifest value for theme ' . $id . '!');
            }
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
//        if (!isset(self::$announcements[$id])) {
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
     * @return \BearCMS\Themes\Options\Schema|null
     * @throws \Exception
     */
    static function getOptionsSchema(string $id): ?\BearCMS\Themes\Options\Schema
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        if (is_callable($theme->optionsSchema)) {
            $schema = call_user_func($theme->optionsSchema);
            if ($schema !== null && !($schema instanceof \BearCMS\Themes\Options\Schema)) {
                throw new \Exception('Invalid theme options value for theme ' . $id . '!');
            }
            return $schema;
        }
        return null;
    }

    /**
     * 
     * @param string $id
     * @return array
     * @throws \Exception
     */
    static function getOptionsSchemaAsArray(string $id): array
    {
        $schema = self::getOptionsSchema($id);
        if ($schema === null) {
            return null;
        }
        $walkOptions = function(array $options) use (&$walkOptions) {
            $result = [];
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Options\OptionSchema) {
                    $item = array_merge($option->details, [
                        "id" => $option->id,
                        "type" => $option->type,
                        "name" => $option->name
                    ]);
                } elseif ($option instanceof \BearCMS\Themes\Options\GroupSchema) {
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
        return $walkOptions($schema->getList());
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
            $result = call_user_func($theme->styles);
            if (!is_array($result)) {
                throw new \Exception('Invalid theme styles value for theme ' . $id . '!');
            }
            foreach ($result as $j => $style) {
                if (isset($result[$j]['id'])) {
                    $result[$j]['id'] = (string) $result[$j]['id'];
                } else {
                    $result[$j]['id'] = 'style' . $j;
                }
                if (isset($result[$j]['name'])) {
                    $result[$j]['name'] = (string) $result[$j]['name'];
                } else {
                    $result[$j]['name'] = '';
                }
                if (isset($result[$j]['media'])) {
                    if (!is_array($result[$j]['media'])) {
                        throw new \Exception('');
                    }
                } else {
                    $result[$j]['media'] = [];
                }
            }
            if ($updateMediaFilenames) {
                $app = App::get();
                $context = $app->context->get(__FILE__);
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
        if (!isset(self::$announcements[$id])) {
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
     * @return \BearCMS\Themes\Options|null
     */
    static public function getOptions(string $id, string $userID = null): ?\BearCMS\Themes\Options
    {
        if (!isset(self::$announcements[$id])) {
            return null;
        }
        $localCacheKey = 'options-' . $id . '-' . $userID;
        if (!isset(self::$cache[$localCacheKey])) {
            $app = App::get();
            $cacheKey = Internal\Themes::getCacheItemKey($id, $userID);
            $envKey = md5(serialize(array_keys(self::$elementsOptions)) . serialize(array_keys(self::$pagesOptions)));
            $useCache = $cacheKey !== null;
            $resultData = null;
            if ($useCache) {
                $resultData = $app->cache->getValue($cacheKey);
                if ($resultData !== null) {
                    $resultData = json_decode($resultData, true);
                }
            }
            if ($resultData === null || $resultData[2] !== $envKey) {
                $values = [];
                $html = '';
                $currentValues = null;
                if ($userID !== null) {
                    $userOptions = Internal2::$data2->themes->getUserOptions($id, $userID);
                    if (is_array($userOptions)) {
                        $currentValues = $userOptions;
                    }
                }
                if ($currentValues === null) {
                    $currentValues = Internal2::$data2->themes->getOptions($id);
                }
                $themeOptions = Internal\Themes::getOptionsSchema($id);
                if ($themeOptions !== null) {
                    $walkOptions = function(array $options) use (&$walkOptions, $currentValues, &$values) {
                        foreach ($options as $option) {
                            if ($option instanceof \BearCMS\Themes\Options\OptionSchema) {
                                $optionID = $option->id;
                                $optionType = $option->type;
                                $value = isset($currentValues[$optionID]) ? $currentValues[$optionID] : (isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value']) : $option->details['value']) : null);
                                if (strlen($value) > 0) {
                                    if ($optionType === 'image') {
                                        $newValue = Internal2::$data2->getRealFilename($value);
                                        if ($newValue !== null) {
                                            $value = $newValue;
                                        }
                                    } elseif ($optionType === 'css' || $optionType === 'cssBackground') {
                                        $temp = json_decode($value, true);
                                        if (is_array($temp)) {
                                            foreach ($temp as $key => $_value) {
                                                $matches = [];
                                                preg_match_all('/url\((.*?)\)/', $_value, $matches);
                                                if (!empty($matches[1])) {
                                                    $matches[1] = array_unique($matches[1]);
                                                    $search = [];
                                                    $replace = [];
                                                    foreach ($matches[1] as $filename) {
                                                        $newFileName = Internal2::$data2->getRealFilename($filename);
                                                        if ($newFileName !== null) {
                                                            $search[] = $filename;
                                                            $replace[] = $newFileName;
                                                        }
                                                    }
                                                    $temp[$key] = str_replace($search, $replace, $_value);
                                                }
                                            }
                                            $value = json_encode($temp);
                                        }
                                    }
                                }
                                $values[$optionID] = $value;
                            } elseif ($option instanceof \BearCMS\Themes\Options\GroupSchema) {
                                $walkOptions($option->getList());
                            }
                        }
                    };
                    $walkOptions($themeOptions->getList());
                    $themeOptions->setValues($values);
                    $html = $themeOptions->getHTML();
                }
                $resultData = [$values, $html, $envKey];
                if ($useCache) {
                    $app->cache->set($app->cache->make($cacheKey, json_encode($resultData)));
                }
            }

            $applyImageUrls = function($text) use ($app) {
                $matches = [];
                preg_match_all('/url\((.*?)\)/', $text, $matches);
                if (!empty($matches[1])) {
                    $matches[1] = array_unique($matches[1]);
                    $search = [];
                    $replace = [];
                    foreach ($matches[1] as $filename) {
                        $search[] = $filename;
                        $replace[] = $app->assets->getUrl($filename, ['cacheMaxAge' => 999999999]);
                    }
                    $text = str_replace($search, $replace, $text);
                }
                return $text;
            };
            $resultData[1] = $applyImageUrls($resultData[1]);
            self::$cache[$localCacheKey] = new \BearCMS\Themes\Options($resultData[0], $resultData[1]);
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
        if (!isset(self::$announcements[$id])) {
            throw new \Exception('Theme does not exists!');
        }
        $app = App::get();
        $optionsValues = self::getOptions($id);
        $values = $optionsValues->getValues();
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

        $archiveFileDataKey = '.temp/bearcms/theme-export-' . md5(uniqid()) . '.zip';
        $archiveFilename = $app->data->getFilename($archiveFileDataKey);
        $app->data->setValue($archiveFileDataKey . '_', 'temp'); // needed to make the dir for the archive file
        $zip = new \ZipArchive();
        if ($zip->open($archiveFilename, \ZipArchive::CREATE) === true) {
            $zip->addFromString('manifest.json', json_encode($manifest));
            $zip->addFromString('values.json', json_encode($values));
            foreach ($filesToAttach as $attachmentName => $filename) {
                $zip->addFile($filename, $attachmentName);
            }
            $zip->close();
        } else {
            throw new \Exception('Cannot open zip archive (' . $archiveFilename . ')');
        }
        $app->data->delete($archiveFileDataKey . '_'); // remove the temp file
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
        if (!isset(self::$announcements[$id])) {
            throw new \Exception('Theme does not exists!', 1);
        }
        $app = App::get();
        if (!$app->data->exists($fileDataKey)) {
            throw new \Exception('Import file not found!', 2);
        }
        $hasUser = strlen($userID) > 0;
        $archiveFilename = $app->data->getFilename($fileDataKey);
        $zip = new \ZipArchive();
        if ($zip->open($archiveFilename) === true) {

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
                        $size = $app->images->getSize($app->data->getFilename($dataKey));
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
                    $app->data->makePublic($dataKey);
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
        } else {
            throw new \Exception('Cannot open zip archive (' . $archiveFilename . ')', 8);
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
        return 'bearcms-theme-options-' . Config::$dataCachePrefix . '-' . md5($id) . '-' . md5($version) . '-' . md5($userID) . '-6';
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
            if (strpos($value, 'data:') === 0 || strpos($value, 'app:') === 0 || strpos($value, 'addon:') === 0) {
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
