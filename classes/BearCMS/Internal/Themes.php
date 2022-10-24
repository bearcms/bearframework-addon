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

    static $elementsOptions = []; // [type=>callback or type=>[version,callback]]
    static $pagesOptions = []; // [type=>callback or type=>[version,callback]]
    static $registrations = [];
    static $cache = [];

    const OPTIONS_CONTEXT_THEME = 1;
    const OPTIONS_CONTEXT_ELEMENT = 2;

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
        if (Config::$defaultThemeID !== null && strlen(Config::$defaultThemeID) > 0) {
            return Config::$defaultThemeID;
        }
        return 'none';
    }

    /**
     * 
     * @param string $id
     * @return void
     */
    static function setActiveThemeID(string $id): void
    {
        $app = App::get();
        $app->data->setValue('bearcms/themes/active.json', json_encode(['id' => $id], JSON_THROW_ON_ERROR));
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
        $theme = self::get($id);
        if ($theme instanceof \BearCMS\Themes\Theme && is_callable($theme->initialize)) {
            $app = App::get();
            $currentUserID = $app->bearCMS->currentUser->exists() ? $app->bearCMS->currentUser->getID() : null;
            $currentCustomizations = self::getCustomizations($id, $currentUserID);
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
            $context = $app->contexts->get(__DIR__);
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
        return self::optionsToArray($options);
    }

    /**
     * 
     * @param \BearCMS\Themes\Theme\Options $options
     * @return array
     */
    static function optionsToArray(\BearCMS\Themes\Theme\Options $options): array
    {
        $walkOptions = function (array $options) use (&$walkOptions) {
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
     * @param boolean $updateMediaFilenames
     * @return array|null
     * @throws \Exception
     */
    static function getStyles(string $id, bool $updateMediaFilenames = true): ?array
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
                if ($style->id === null || strlen($style->id) === 0) {
                    $style->id = 'style' . $j;
                }
                if ($style->name == null) {
                    $style->name = '';
                }
                $result[] = $style->toArray();
            }
            if ($updateMediaFilenames) {
                $app = App::get();
                $context = $app->contexts->get(__DIR__);
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
        $styles = self::getStyles($id);
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
     * @return string|null
     */
    static private function getCustomizationsCacheKey(string $id, string $userID = null): ?string
    {
        return 'bearcms-theme-customizations-' . md5($id) . '-' . md5((string)$userID);
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return void
     */
    static function clearCustomizationsCache(string $id, string $userID = null): void
    {
        $cacheKey = self::getCustomizationsCacheKey($id, $userID);
        $app = App::get();
        $app->data->delete('.temp/bearcms/theme-customizations-' . md5($cacheKey));
        $app->cache->delete($cacheKey);
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
        $localCacheKey = 'customizations-' . $id . '-' . $userID;
        if (!isset(self::$cache[$localCacheKey])) {
            $app = App::get();
            $version = self::getVersion($id);
            $useCache = $version !== null;
            $elementsOptionsEnvKeyData = [];
            foreach (self::$elementsOptions as $key => $value) {
                $elementsOptionsEnvKeyData[] = $key . (is_array($value) ? '$' . $value[0] : '');
            }
            $pagesOptionsEnvKeyData = [];
            foreach (self::$pagesOptions as $key => $value) {
                $pagesOptionsEnvKeyData[] = $key . (is_array($value) ? '$' . $value[0] : '');
            }
            $envKey = md5(md5(serialize($elementsOptionsEnvKeyData)) . md5(serialize($pagesOptionsEnvKeyData)) . md5((string)$version) . md5('v9'));
            $resultData = null;
            if ($useCache) {
                $cacheKey = self::getCustomizationsCacheKey($id, $userID);
                $tempDataKey = '.temp/bearcms/theme-customizations-' . md5($cacheKey);
                $saveToCache = false;
                $saveToTempData = false;
                $resultData = $app->cache->getValue($cacheKey);
                if ($resultData !== null) {
                    $resultData = json_decode($resultData, true);
                } else {
                    $saveToCache = true;
                }
                if ($resultData === null) {
                    $resultData = $app->data->getValue($tempDataKey);
                    if ($resultData !== null) {
                        $resultData = json_decode($resultData, true);
                    } else {
                        $saveToTempData = true;
                    }
                }
            }
            if ($resultData === null || !isset($resultData[2]) || $resultData[2] !== $envKey) {
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
                $themeOptions = self::getOptions($id);
                if ($themeOptions === null) {
                    $values = [];
                    $htmlData = [];
                } else {
                    $themeOptions->setValues($currentValues);
                    $values = $themeOptions->getValues(true);
                    $htmlData = self::getOptionsHTMLData($themeOptions->getList(), true);
                }
                $resultData = [$values, $htmlData, $envKey];
                if ($useCache) {
                    $saveToCache = true;
                    $saveToTempData = true;
                }
            }
            if ($useCache) {
                if ($saveToCache) {
                    $app->cache->set($app->cache->make($cacheKey, json_encode($resultData, JSON_THROW_ON_ERROR)));
                }
                if ($saveToTempData) {
                    $app->data->setValue($tempDataKey, json_encode($resultData, JSON_THROW_ON_ERROR));
                }
            }
            $values = $resultData[0];
            $htmlData = $resultData[1];
            $html = self::processOptionsHTMLData($htmlData);

            self::$cache[$localCacheKey] = new \BearCMS\Themes\Theme\Customizations($values, $html, isset($htmlData['details']) ? $htmlData['details'] : []);
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
        $filesInValues = self::getFilesInValues($values, true);
        $filesToUpdate = [];
        foreach ($filesInValues as $filename) {
            $filenameOptions = Internal\Data::getFilenameOptions($filename);
            $filenameWithoutOptions = Internal\Data::removeFilenameOptions($filename);
            $realFilename = Internal\Data::getRealFilename($filenameWithoutOptions);
            if (isset($filesToAttach[$realFilename])) {
                $attachmentName = $filesToAttach[$realFilename];
            } else {
                $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($filenameWithoutOptions, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
                $filesToAttach[$realFilename] = $attachmentName;
            }
            $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $attachmentName, $filenameOptions);
            $filesToUpdate[$filename] = $newFilenameWithOptions;
        }
        $values = self::updateFilesInValues($values, $filesToUpdate);

        $manifest = [
            'themeID' => $id,
            'exportDate' => date('c')
        ];

        $archiveFileDataKey = '.temp/bearcms/themeexport/theme-export-' . md5(uniqid()) . '.zip';
        $archiveFilename = $app->data->getFilename($archiveFileDataKey);
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-export-' . uniqid() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename, \ZipArchive::CREATE) === true) {
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
            $zip->addFromString('values.json', json_encode($values, JSON_THROW_ON_ERROR));
            foreach ($filesToAttach as $filename => $attachmentName) {
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
        $hasUser = $userID !== null && strlen($userID) > 0;
        $archiveFilename = $app->data->getFilename($fileDataKey);
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-import-' . uniqid() . '.zip';
        copy($archiveFilename, $tempArchiveFilename);
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename) === true) {

            $getManifest = function () use ($zip) {
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

            $getValues = function () use ($zip) {
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

            $filesInValues = self::getFilesInValues($values, true);
            $filesToUpdate = [];
            $importedDataKeys = [];
            foreach ($filesInValues as $filename) {
                if (strpos($filename, 'data:files/') !== 0) {
                    throw new \Exception('Invalid file (' . $filename . ')!', 6);
                }
                $filenameOptions = Internal\Data::getFilenameOptions($filename);
                $filenameWithoutOptions = Internal\Data::removeFilenameOptions($filename);
                $filenameInArchive = substr($filenameWithoutOptions, 5); // remove data:
                $data = $zip->getFromName($filenameInArchive);
                if ($data !== false) {
                    $extension = pathinfo($filenameInArchive, PATHINFO_EXTENSION);
                    if (isset($importedDataKeys[$filenameInArchive])) {
                        $dataKey = $importedDataKeys[$filenameInArchive];
                    } else {
                        if (array_search($extension, ['jpg', 'jpeg', 'gif', 'png', 'svg']) === false) {
                            throw new \Exception('Invalid file (' . $filenameInArchive . ')!', 9);
                        }
                        $dataKey = ($hasUser ? '.temp/bearcms/files/themeimage/' : 'bearcms/files/themeimage/') . md5($filenameInArchive . '-' . uniqid()) . '.' . $extension;
                        $app->data->setValue($dataKey, $data);
                        $importedDataKeys[$filenameInArchive] = $dataKey;
                    }
                    $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $dataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                    $isInvalid = false;
                    if ($extension !== 'svg') {
                        try {
                            $assetDetails = $app->assets->getDetails($app->data->getFilename($dataKey), ['width', 'height']);
                            $size = [$assetDetails['width'], $assetDetails['height']];
                            if ($size[0] <= 0 || $size[1] <= 0) {
                                $isInvalid = true;
                            }
                        } catch (\Exception $e) {
                            $isInvalid = true;
                        }
                    }

                    if ($isInvalid) {
                        foreach ($filesToUpdate as $newFilename) { // remove previously added files
                            $newFilenameDataKey = Internal\Data::getFilenameDataKey($newFilename);
                            $app->data->delete($newFilenameDataKey);
                        }
                        throw new \Exception('Invalid file (' . $filename . ')!', 7);
                    }
                }
            }

            $values = self::updateFilesInValues($values, $filesToUpdate);
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
            $duplicatedDataKeys = [];
            $filesInValues = self::getFilesInValues($values, true);
            $filesToUpdate = [];
            foreach ($filesInValues as $filename) {
                $filenameOptions = Internal\Data::getFilenameOptions($filename);
                $dataKey = Internal\Data::getFilenameDataKey($filename);
                if ($dataKey !== null && strpos($dataKey, '.temp/bearcms/files/themeimage/') === 0) {
                    $newDataKey = 'bearcms/files/themeimage/' . pathinfo($dataKey, PATHINFO_BASENAME);
                    if (!isset($duplicatedDataKeys[$dataKey])) {
                        $app->data->duplicate($dataKey, $newDataKey); // setValues() will remove the files in the user options 
                        $duplicatedDataKeys[$dataKey] = true;
                    }
                    $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                }
            }
            $values = self::updateFilesInValues($values, $filesToUpdate);
            Internal2::$data2->themes->setOptions($id, $values);
            Internal2::$data2->themes->discardUserOptions($id, $userID);
            self::$cache = [];
        }
    }

    /**
     * 
     * @param array $values
     * @param boolean $includeOptions If TRUE the filenames will be in format FILENAME?options, else the ?options will be removed.
     * @return array
     */
    static public function getFilesInValues(array $values, bool $includeOptions = false): array
    {
        $result = [];
        foreach ($values as $value) {
            $valueDetails = self::getValueDetails($value, true);
            $files = $valueDetails['files'];
            if (!$includeOptions) {
                foreach ($files as $index => $filename) {
                    $files[$index] = Internal\Data::removeFilenameOptions($filename);
                }
            }
            $result = array_merge($result, $files);
        }
        return array_values(array_unique($result));
    }

    /**
     * 
     * @param array $values
     * @param array $keysToUpdate [oldKey=>newKey, oldKey=>newKey]
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
                $search[] = trim(json_encode('url(' . $oldKey . ')', JSON_THROW_ON_ERROR), '"');
                $replace[] = trim(json_encode('url(' . $newKey . ')', JSON_THROW_ON_ERROR), '"');
            }
            foreach ($values as $name => $value) {
                if (!is_string($value) || strlen($value) === 0) {
                    continue;
                }
                $values[$name] = str_replace($search, $replace, $values[$name]);
            }
            foreach ($values as $name => $value) {
                if (!is_string($value) || strlen($value) === 0) {
                    continue;
                }
                if (isset($keysToUpdate[$value])) {
                    $values[$name] = $keysToUpdate[$value];
                }
            }
        }
        return $values;
    }

    /**
     * 
     * @param mixed $value Assumes that if the value is a JSON, it's in the following formats: ['color':'','color:hover':''], [value=>..., states=>...]. JSON in other options is not allowed (lists, etc.), only in [value=>...].
     * @param boolean $includeFiles
     * @return array
     */
    static function getValueDetails($value, bool $includeFiles = false): array
    {
        $result = ['value' => null, 'states' => []];
        if ($includeFiles) {
            $result['files'] = [];
        }
        if ($value === null || !is_string($value) || !isset($value[0])) {
            $result['value'] = $value;
            return $result;
        }
        $decodedValue = json_decode($value, true);
        if (!is_array($decodedValue)) {
            $result['value'] = $value;
            return $result;
        }
        $hasValue = isset($decodedValue['value']);
        $hasStates = isset($decodedValue['states']);
        if ($hasValue || $hasStates) { // Format: [value=>..., states=>...]
            if ($hasValue) {
                $result['value'] = $decodedValue['value'];
            }
            if ($hasStates) {
                $result['states'] = $decodedValue['states'];
            }
        } else { // Old CSS format: ['color':'','color:hover':'']
            $newValue = [];
            $newStates = [];
            foreach ($decodedValue as $_key => $_value) {
                $colonIndex = strpos($_key, ':');
                if ($colonIndex !== false) {
                    $state = substr($_key, $colonIndex);
                    $propertyName = substr($_key, 0, $colonIndex);
                    if (!isset($newStates[$state])) {
                        $newStates[$state] = [];
                    }
                    $newStates[$state][$propertyName] = $_value;
                } else {
                    $newValue[$_key] = $_value;
                }
            }
            $result['value'] = $newValue;
            foreach ($newStates as $state => $stateValue) {
                $result['states'][] = [$state, $stateValue];
            }
        }

        if ($includeFiles) {
            $addFiles = function ($value) use (&$result) {
                $isFilename = function ($value) {
                    return strpos($value, 'appdata://') === 0 || strpos($value, 'data:') === 0 || strpos($value, 'addon:') === 0;
                };
                if (is_array($value)) {
                    foreach ($value as $_value) {
                        if (is_string($_value)) {
                            if ($isFilename($_value)) {
                                $result['files'] = $_value;
                            } else {
                                $matches = [];
                                preg_match_all('/url\((.*?)\)/', $_value, $matches);
                                if (!empty($matches[1])) {
                                    $result['files'] = array_merge($result['files'], $matches[1]);
                                }
                            }
                        }
                    }
                } elseif (is_string($value)) {
                    if ($isFilename($value)) {
                        $result['files'] = $value;
                    }
                }
            };
            $addFiles($result['value']);
            foreach ($result['states'] as $stateData) {
                $addFiles($stateData[1]);
            }
            $result['files'] = array_values(array_unique($result['files']));
        }
        return $result;
    }

    /**
     * 
     * @param string $state
     * @return array
     */
    static function parseStateCombination(string $state): array
    {
        $result = [];
        $parts = explode(':', trim($state, ':'));
        foreach ($parts as $part) {
            $matches = [];
            preg_match('/^([a-zA-Z0-9\-]*)\((.*?)\)$/', $part, $matches);
            if (isset($matches[1], $matches[2])) {
                $args = [];
                $argTexts = explode(',', $matches[2]);
                foreach ($argTexts as $argText) {
                    $argText = trim($argText);
                    $argTextParts = explode('=', $argText, 2);
                    if (sizeof($argTextParts) === 2) {
                        $args[trim($argTextParts[0])] = $argTextParts[1];
                    } else {
                        $args[$argText] = true;
                    }
                }
                $result[$matches[1]] = $args;
            } else {
                $matches = [];
                preg_match('/^[a-zA-Z0-9\-]*$/', $part, $matches);
                if (isset($matches[0])) {
                    $result[$matches[0]] = [];
                }
            }
        }
        return $result;
    }

    //static function 

    /**
     * 
     * @param array $options
     * @param boolean $includeDetails
     * @return array
     */
    static public function getOptionsHTMLData(array $options, bool $includeDetails = false): array
    {
        $app = App::get();

        $cssRules = [];
        $cssCode = '';
        $details = [];
        $linkTags = [];

        $addAssetDetails = function (string $filename) use ($app, &$details, $includeDetails) {
            if (!isset($details['assets'])) {
                $details['assets'] = [];
            }
            if ($includeDetails) {
                $realFilename = \BearCMS\Internal\Data::getRealFilename($filename, true);
                if ($realFilename === null) {
                    $assetDetails = ['width' => null, 'height' => null];
                } else {
                    $assetDetails = $app->assets->getDetails($realFilename, ['width', 'height']);
                }
                $details['assets'][$filename] = ['width' => $assetDetails['width'], 'height' => $assetDetails['height']];
            } else {
                $details['assets'][$filename] = [];
            }
        };

        $addValueDetails = function (string $id, string $name, $value) use (&$details) {
            if (!isset($details['values'])) {
                $details['values'] = [];
            }
            if (!isset($details['values'][$id])) {
                $details['values'][$id] = [];
            }
            $details['values'][$id][$name] = $value;
        };

        $webSafeFonts = [
            'Arial' => 'Arial,Helvetica,sans-serif',
            'Arial Black' => '"Arial Black",Gadget,sans-serif',
            'Comic Sans' => '"Comic Sans MS",cursive,sans-serif',
            'Courier' => '"Courier New",Courier,monospace',
            'Georgia' => 'Georgia,serif',
            'Impact' => 'Impact,Charcoal,sans-serif',
            'Lucida' => '"Lucida Sans Unicode","Lucida Grande",sans-serif',
            'Lucida Console' => '"Lucida Console",Monaco,monospace',
            'Palatino' => '"Palatino Linotype","Book Antiqua",Palatino,serif',
            'Tahoma' => 'Tahoma,Geneva,sans-serif',
            'Times New Roman' => '"Times New Roman",Times,serif',
            'Trebuchet' => '"Trebuchet MS",Helvetica,sans-serif',
            'Verdana' => 'Verdana,Geneva,sans-serif'
        ];

        $updateFontFamily = function (string $fontName) use ($webSafeFonts, &$details, &$linkTags) {
            if (isset($webSafeFonts[$fontName])) {
                return $webSafeFonts[$fontName];
            } elseif (strpos($fontName, 'googlefonts:') === 0) {
                $googleFontName = substr($fontName, strlen('googlefonts:'));
                if (!isset($details['googleFonts'])) {
                    $details['googleFonts'] = [];
                }
                if (!isset($details['googleFonts'][$googleFontName])) {
                    $updateKey = 'googlefont:' . md5($googleFontName);
                    $details['googleFonts'][$googleFontName] = [];
                    $linkTags[] = '<link href="' . htmlentities($updateKey) . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"><noscript><link href="' . htmlentities($updateKey) . '" rel="stylesheet"></noscript>';
                }
                return $googleFontName;
            }
            return $fontName;
        };

        $getStateOutputCode = function (string $state, array $statesTypes, string $selector): array {
            $cssMediaQueries = []; // array of strings
            $additionalCSSSelectors = []; // array of strings
            $responsiveAttributesRules = [];
            $unsupportedStates = '';
            $result = ['cssRules' => [], 'responsiveAttributes' => []];
            $parts = self::parseStateCombination($state);
            foreach ($parts as $name => $args) {
                if (isset($statesTypes[$name])) {
                    $stateType = $statesTypes[$name];
                    if ($stateType === 'screenSize') {
                        $tempCssValue = [];
                        $tempResponsiveAttributesValue = [];
                        if (isset($args['small'])) {
                            $tempCssValue[] = '(max-width:600px)';
                            $tempResponsiveAttributesValue[] = 'vw<=600';
                        } elseif (isset($args['medium'])) {
                            $tempCssValue[] = '(min-width:600px)';
                            $tempCssValue[] = '(max-width:1000px)';
                            $tempResponsiveAttributesValue[] = 'vw>600';
                            $tempResponsiveAttributesValue[] = 'vw<=1000';
                        } elseif (isset($args['large'])) {
                            $tempCssValue[] = '(min-width:1000px)';
                            $tempResponsiveAttributesValue[] = 'vw>1000';
                        } else {
                            if (isset($args['minWidth'])) {
                                $tempCssValue[] = '(min-width:' . $args['minWidth'] . 'px)';
                                $tempResponsiveAttributesValue[] = 'vw>' . $args['minWidth'];
                            }
                            if (isset($args['maxWidth'])) {
                                $tempCssValue[] = '(max-width:' . $args['maxWidth'] . 'px)';
                                $tempResponsiveAttributesValue[] = 'vw<=' . $args['maxWidth'];
                            }
                            if (isset($args['minHeight'])) {
                                $tempCssValue[] = '(min-height:' . $args['minHeight'] . 'px)';
                                $tempResponsiveAttributesValue[] = 'vh>' . $args['minHeight'];
                            }
                            if (isset($args['maxHeight'])) {
                                $tempCssValue[] = '(max-height:' . $args['maxHeight'] . 'px)';
                                $tempResponsiveAttributesValue[] = 'vh<=' . $args['maxHeight'];
                            }
                        }
                        if (!empty($tempCssValue)) {
                            $cssMediaQueries[] = [implode(' and ', $tempCssValue)];
                        }
                        if (!empty($tempResponsiveAttributesValue)) {
                            $responsiveAttributesRules[] = [implode('&&', $tempResponsiveAttributesValue)];
                        }
                    } else if ($stateType === 'pageType') {
                        $tempCssValue = [];
                        $tempResponsiveAttributesValue = [];
                        foreach ($args as $argName => $argValue) {
                            $tempCssValue[] = 'html[data-bearcms-page-type="' . $argName . '"]';
                            $tempResponsiveAttributesValue[] = 'q(html[data-bearcms-page-type="' . $argName . '"])';
                        }
                        if (!empty($tempCssValue)) {
                            $additionalCSSSelectors[] = $tempCssValue;
                        }
                        if (!empty($tempResponsiveAttributesValue)) {
                            $responsiveAttributesRules[] = $tempResponsiveAttributesValue;
                        }
                    }
                } else {
                    $unsupportedStates .= ':' . $name;
                }
            }

            $getCombinations = function (array $list) { // combines arrays of strings
                $result = [];
                foreach ($list as $items) {
                    $resultCount = sizeof($result);
                    $itemsCount = sizeof($items);
                    if ($itemsCount > 1) {
                        $resultClone = $result;
                        for ($i = 1; $i < $itemsCount; $i++) {
                            $result = array_merge($result, $resultClone);
                        }
                    }
                    for ($i = 0; $i < $itemsCount * ($resultCount > 0 ? $resultCount : 1); $i++) {
                        $itemIndex = $resultCount > 0 ? floor($i / $resultCount) : $i;
                        if (!isset($result[$i])) {
                            $result[$i] = [];
                        }
                        $result[$i][] = $items[$itemIndex];
                    }
                }
                return $result;
            };

            // cssRules
            $cssMediaQueriesCombinations = $getCombinations($cssMediaQueries);
            if (empty($cssMediaQueriesCombinations)) {
                $cssMediaQueriesCombinations[] = [];
            }
            $additionalCSSSelectorsCombinations = $getCombinations($additionalCSSSelectors);
            if (empty($additionalCSSSelectorsCombinations)) {
                $additionalCSSSelectorsCombinations[] = [];
            }
            foreach ($cssMediaQueriesCombinations as $cssMediaQueryCombinations) {
                foreach ($additionalCSSSelectorsCombinations as $additionalCssSelectorCombinations) {
                    $result['cssRules'][] = [implode(' and ', $cssMediaQueryCombinations), (!empty($additionalCssSelectorCombinations) ? implode(' ', $additionalCssSelectorCombinations) . ' ' : '') . $selector . $unsupportedStates];
                }
            }

            // responsiveAttributes
            $responsiveAttributesRulesCombinations = $getCombinations($responsiveAttributesRules);
            foreach ($responsiveAttributesRulesCombinations as $responsiveAttributesRulesCombination) {
                $result['responsiveAttributes'][] = implode('&&', $responsiveAttributesRulesCombination);
            }

            return $result;
        };

        $addCSSRule = function (string $mediaQuery, string $selector, string $value) use (&$cssRules) {
            if (!isset($cssRules[$mediaQuery])) {
                $cssRules[$mediaQuery] = [];
            }
            if (!isset($cssRules[$mediaQuery][$selector])) {
                $cssRules[$mediaQuery][$selector] = '';
            }
            $cssRules[$mediaQuery][$selector] .= $value;
        };

        $walkOptions = function ($options) use (&$addCSSRule, &$cssCode, &$walkOptions, &$addAssetDetails, &$addValueDetails, $updateFontFamily, $getStateOutputCode, $includeDetails) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    //$value = isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value'], JSON_THROW_ON_ERROR) : $option->details['value']) : null; // array not used ???
                    $value = isset($option->details['value']) ? $option->details['value'] : (isset($option->details['defaultValue']) ? $option->details['defaultValue'] : null);
                    $valueDetails = self::getValueDetails($value, true);
                    $optionType = $option->type;
                    if ($optionType === 'cssCode') {
                        $cssCode .= $valueDetails['value'];
                    } else {
                        $isCssOptionType = strpos($optionType, 'css') === 0;
                        if ($optionType === 'image' || $optionType === 'css' || $optionType === 'cssBackground') {
                            foreach ($valueDetails['files'] as $filename) {
                                $addAssetDetails($filename);
                            }
                        }
                        $statesTypes = [];
                        if (isset($option->details['cssOptions'])) {
                            $cssOptions = $option->details['cssOptions'];
                            $addNonCssState = function (string $id, string $type) use ($cssOptions, &$statesTypes) {
                                foreach ($cssOptions as $cssOption) {
                                    if (strpos($cssOption, '/' . $type . 'State') !== false) {
                                        $statesTypes[$id] = $type;
                                        return;
                                    }
                                }
                            };
                            $addNonCssState('screen-size', 'screenSize');
                            $addNonCssState('page-type', 'pageType');
                        }
                        if (isset($option->details['states'])) {
                            foreach ($option->details['states'] as $stateData) {
                                if (isset($stateData['id'], $stateData['type'])) {
                                    if (array_search($stateData['type'], ['screenSize', 'pageType']) !== false) {
                                        $statesTypes[$stateData['id']] = $stateData['type'];
                                    }
                                }
                            }
                        }
                        if ($includeDetails && !$isCssOptionType && !empty($statesTypes)) {
                            $statesResponsiveAttributes = [];
                            foreach ($valueDetails['states'] as $stateIndex => $stateData) {
                                $stateOutputCode = $getStateOutputCode($stateData[0], $statesTypes, '');
                                if (!empty($stateOutputCode['responsiveAttributes'])) {
                                    $statesResponsiveAttributes[$stateIndex] = $stateOutputCode['responsiveAttributes'];
                                }
                            }
                            $addValueDetails($option->id, 'statesResponsiveAttributes', $statesResponsiveAttributes);
                        }
                        if (isset($option->details['cssOutput'])) {
                            foreach ($option->details['cssOutput'] as $outputDefinition) {
                                if (is_array($outputDefinition)) {
                                    if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                        $selector = $outputDefinition[1];
                                        if ($isCssOptionType) {
                                            if (isset($outputDefinition[2])) { // has selector value specified
                                                $ruleValue = $outputDefinition[2];
                                                $valuesToSearch = [];
                                                $valuesToReplace = [];
                                                $matches = [];
                                                preg_match_all('/{cssPropertyValue\((.*?)\)}/', $ruleValue, $matches);
                                                foreach ($matches[0] as $i => $match) {
                                                    $valuesToSearch[] = $match;
                                                    $cssPropertyName = $matches[1][$i];
                                                    $valueToSet = isset($valueDetails['value'][$cssPropertyName]) ? $valueDetails['value'][$cssPropertyName] : '';
                                                    if ($cssPropertyName === 'font-family') {
                                                        $valueToSet = $updateFontFamily($valueToSet);
                                                    }
                                                    $valuesToReplace[] = $valueToSet;
                                                }
                                                $matches = [];
                                                preg_match_all('/' . rawurlencode('{cssPropertyValue(') . '(.*?)' . rawurlencode(')}') . '/', $ruleValue, $matches);
                                                foreach ($matches[0] as $i => $match) {
                                                    $valuesToSearch[] = $match;
                                                    $cssPropertyName = $matches[1][$i];
                                                    $valueToSet = isset($valueDetails['value'][$cssPropertyName]) ? $valueDetails['value'][$cssPropertyName] : '';
                                                    if ($cssPropertyName === 'font-family') {
                                                        $valueToSet = $updateFontFamily($valueToSet);
                                                    }
                                                    $valuesToReplace[] = rawurlencode($valueToSet);
                                                }
                                                $addCSSRule('', $selector, str_replace($valuesToSearch, $valuesToReplace, $ruleValue));
                                            } else {
                                                $getStateValueAsString = function ($values) use ($updateFontFamily): string {
                                                    if ($values === null || $values === '') {
                                                        return '';
                                                    }
                                                    $result = '';
                                                    foreach ($values as $name => $value) {
                                                        if ($name === 'font-family') {
                                                            $value = $updateFontFamily($value);
                                                        }
                                                        $result .= $name . ':' . $value . ';';
                                                    }
                                                    return $result;
                                                };
                                                $stateValueAsString = $getStateValueAsString($valueDetails['value']);
                                                if ($stateValueAsString !== '') {
                                                    $addCSSRule('', $selector, $stateValueAsString);
                                                }
                                                foreach ($valueDetails['states'] as $stateData) {
                                                    $stateValueAsString = $getStateValueAsString($stateData[1]);
                                                    if ($stateValueAsString !== '') {
                                                        $stateOutputCode = $getStateOutputCode($stateData[0], $statesTypes, $selector);
                                                        foreach ($stateOutputCode['cssRules'] as $cssRuleToAdd) {
                                                            $addCSSRule($cssRuleToAdd[0], $cssRuleToAdd[1], $stateValueAsString);
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (isset($outputDefinition[2])) { // has selector value specified
                                                //$defaultValue = '';
                                                // if ($optionType === 'htmlUnit') {
                                                //     $defaultValue = '0';
                                                // }
                                                $valueToSet = $value !== null && strlen($value) > 0 ? $value : '';
                                                $valuesToSearch = ['{value}', rawurlencode('{value}')];
                                                $valuesToReplace = [$valueToSet, rawurlencode($valueToSet)];
                                                if ($optionType === 'font') {
                                                    $valueAsFontName = $updateFontFamily($valueToSet);
                                                    $valuesToSearch[] = '{valueAsFontName}';
                                                    $valuesToReplace[] = $valueAsFontName;
                                                    $valuesToSearch[] = rawurlencode('{valueAsFontName}');
                                                    $valuesToReplace[] = rawurlencode($valueAsFontName);
                                                }
                                                $addCSSRule('', $selector, str_replace($valuesToSearch, $valuesToReplace, $outputDefinition[2]));
                                            }
                                        }
                                    } elseif (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                                        $addCSSRule('', $outputDefinition[1], $outputDefinition[2]);
                                    }
                                }
                            }
                        }
                        if ($optionType === 'list') {
                            if (isset($option->details['values']) && is_array($option->details['values'])) {
                                foreach ($option->details['values'] as $listItemData) {
                                    if (is_array($listItemData) && isset($listItemData['cssRules'], $listItemData['value']) && is_array($listItemData['cssRules'])) {
                                        if ($listItemData['value'] === $valueDetails['value']) {
                                            foreach ($listItemData['cssRules'] as $cssRuleSelector => $cssRuleValue) {
                                                if (is_string($cssRuleSelector) && is_string($cssRuleValue)) {
                                                    $addCSSRule('', $cssRuleSelector, $cssRuleValue);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    $walkOptions($option->getList());
                }
            }
        };
        $walkOptions($options);

        $style = '';
        foreach ($cssRules as $mediaQuery => $mediaQueryRules) {
            if ($mediaQuery !== '') {
                $style .= '@media ' . $mediaQuery . '{';
            }
            foreach ($mediaQueryRules as $key => $value) {
                if (strpos($value, '--rotation') !== false) {
                    $value = 'transform:rotate(var(--rotation));' . $value;
                }
                $style .= $key . '{' . $value . '}';
            }
            if ($mediaQuery !== '') {
                $style .= '}';
            }
        }

        $cssCode = trim($cssCode); // Positioned in different style tag just in case it's invalid

        if (!empty($linkTags) || $style !== '' || $cssCode !== '') {
            $html = '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
        } else {
            $html = '';
        }

        return [
            'html' => $html,
            'details' => $details
        ];
    }

    /**
     * 
     *
     * @param array $data
     * @return string
     */
    static public function processOptionsHTMLData(array $data): string
    {
        if (!isset($data['html'])) {
            return '';
        }
        $html = $data['html'];
        $details = isset($data['details']) ? $data['details'] : [];
        if (!empty($details)) {
            $app = App::get();
            $search = [];
            $replace = [];
            if (isset($details['assets'])) {
                $appAssets = $app->assets;
                foreach ($details['assets'] as $filename => $assetDetails) {
                    try {
                        $filenameOptions = Internal\Data::getFilenameOptions($filename);
                        $filenameWithoutOptions = Internal\Data::removeFilenameOptions($filename);
                        $realFilename = \BearCMS\Internal\Data::getRealFilename($filenameWithoutOptions, true);
                        $search[] = $filename;
                        $options = ['cacheMaxAge' => 999999999];
                        if (!empty($filenameOptions)) {
                            $options = array_merge($options, Internal\Assets::convertFileOptionsToAssetOptions($filenameOptions));
                        }
                        $replace[] = $appAssets->getURL($realFilename, $options);
                    } catch (\Exception $e) { // May be file in an invalid dir
                        $search[] = $filename;
                        $replace[] = '';
                    }
                }
            }
            if (isset($details['googleFonts'])) {
                $googleFontsEmbed = $app->googleFontsEmbed;
                foreach ($details['googleFonts'] as $googleFontName => $googleFontDetails) {
                    $updateKey = 'googlefont:' . md5($googleFontName);
                    $search[] = $updateKey;
                    $replace[] = htmlentities($googleFontsEmbed->getURL($googleFontName));
                }
            }
            uasort($search, function ($a, $b) { // prevents replacing menu.svg before menu.svg?f=123456
                return strlen($b) - strlen($a);
            });
            $temp = [];
            foreach ($search as $index => $text) {
                $temp[] = $replace[$index];
            }
            $search = array_values($search);
            $replace = $temp;
            $html = str_replace($search, $replace, $html);
        }
        return $html;
    }

    /**
     * 
     * @param string $themeID
     * @param string $elementType
     * @return array
     */
    static function getElementsOptionsSelectors(string $themeID, string $elementType): array
    {
        $result = [];
        $themeOptions = self::getOptions($themeID);
        if ($themeOptions !== null) {
            $walkOptions = function ($options) use (&$result, &$walkOptions, $elementType) {
                foreach ($options as $option) {
                    if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                        if (isset($option->details['cssOutput'], $option->details['elementType']) && $option->details['elementType'] === $elementType) {
                            foreach ($option->details['cssOutput'] as $outputDefinition) {
                                if (is_array($outputDefinition)) {
                                    if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                        $optionID = $option->id;
                                        if (!isset($result[$optionID])) {
                                            $result[$optionID] = [];
                                        }
                                        $result[$optionID][] = $outputDefinition[1];
                                    }
                                }
                            }
                        }
                    } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                        $walkOptions($option->getList());
                    }
                }
            };
            $walkOptions($themeOptions->getList());
        }
        return $result;
    }
}
