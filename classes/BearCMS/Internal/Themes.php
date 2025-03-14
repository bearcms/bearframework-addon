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
            $hasCurrentUser = $app->bearCMS->currentUser->exists();
            $currentUserID = $hasCurrentUser ? $app->bearCMS->currentUser->getID() : null;
            $currentCustomizations = self::getCustomizations($id, $currentUserID, $hasCurrentUser);
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
                        "name" => $option->name,
                        "details" => $option->details,
                    ]);
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    $item = [
                        "type" => "group",
                        "name" => $option->name,
                        "description" => $option->description,
                        "details" => $option->details,
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
     * @param string|null $userID
     * @param boolean $includeEditorData
     * @return string|null
     */
    static private function getCustomizationsCacheKey(string $id, ?string $userID = null, bool $includeEditorData = false): ?string
    {
        return 'bearcms-theme-customizations-' . md5($id) . '-' . md5((string)$userID) . '-' . (int)$includeEditorData;
    }

    /**
     * 
     * @param string $id
     * @param string $userID
     * @return void
     */
    static function clearCustomizationsCache(string $id, ?string $userID = null): void
    {
        $app = App::get();
        $clearCache = function ($cacheKey) use ($app): void {
            $app->data->delete('.temp/bearcms/themes/customizations-cache-' . md5($cacheKey));
            $app->cache->delete($cacheKey);
        };
        $clearCache(self::getCustomizationsCacheKey($id, $userID, true));
        $clearCache(self::getCustomizationsCacheKey($id, $userID, false));
    }

    /**
     * 
     * @param string $id
     * @param string|null $userID
     * @param boolean $includeEditorData
     * @return \BearCMS\Themes\Theme\Customizations|null
     */
    static public function getCustomizations(string $id, ?string $userID = null, bool $includeEditorData = false): ?\BearCMS\Themes\Theme\Customizations
    {
        if (!isset(self::$registrations[$id])) {
            return null;
        }
        $localCacheKey = 'customizations-' . $id . '-' . $userID . '-' . (int)$includeEditorData;
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
            $envKey = md5(md5(serialize($elementsOptionsEnvKeyData)) . md5(serialize($pagesOptionsEnvKeyData)) . md5((string)$version) . md5('v18'));
            $resultData = null;
            if ($useCache) {
                $cacheKey = self::getCustomizationsCacheKey($id, $userID, $includeEditorData);
                $tempDataKey = '.temp/bearcms/themes/customizations-cache-' . md5($cacheKey);
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
                    $htmlData = self::getOptionsHTMLData($themeOptions->getList(), true, $userID === null, $includeEditorData);
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
                $attachmentName = 'files/' . (count($filesToAttach) + 1) . '.' . pathinfo($filenameWithoutOptions, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
                $filesToAttach[$realFilename] = $attachmentName;
            }
            $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $attachmentName, $filenameOptions);
            $filesToUpdate[$filename] = $newFilenameWithOptions;
        }
        $values = self::updateFilesInValues($values, $filesToUpdate);

        $manifest = [
            'type' => 'theme',
            'themeID' => $id,
            'exportDate' => date('c')
        ];

        $archiveFilename = $app->data->getFilename('.temp/bearcms/export/theme-export-' . str_replace('/', '-', $id) . '-' . date('Ymd-His') . '.zip');
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-export-' . uniqid() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename, \ZipArchive::CREATE) === true) {
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
            $zip->addFromString('values.json', json_encode($values, JSON_THROW_ON_ERROR));
            foreach ($filesToAttach as $filename => $attachmentName) {
                $content = is_file($filename) ? file_get_contents($filename) : null;
                if ($content === null) {
                    $content = $app->assets->getContent($filename); // Try in assets. May has a custom handler.
                }
                if ($content !== null) {
                    $zip->addFromString($attachmentName, $content);
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Cannot open zip archive (' . $tempArchiveFilename . ')');
        }
        copy($tempArchiveFilename, $archiveFilename);
        unlink($tempArchiveFilename);
        return $archiveFilename;
    }

    /**
     * 
     * @param string $filename The import file data key
     * @param string $id The theme ID
     * @param string $userID The user ID
     * @throws \Exception
     */
    static public function import(string $filename, string $id, ?string $userID = null): void
    {
        if (!isset(self::$registrations[$id])) {
            throw new \Exception('Theme does not exists!', 1);
        }
        $hasUser = $userID !== null && strlen($userID) > 0;
        $extractResult = self::extractExport($filename, !$hasUser);
        if ($extractResult['id'] !== $id) { // cannot import options to different theme
            throw new \Exception('The import file is for different theme (' . $extractResult['themeID'] . ')', 4);
        }
        $values = $extractResult['values'];
        if ($hasUser) {
            Internal2::$data2->themes->setUserOptions($id, $userID, $values);
        } else {
            Internal2::$data2->themes->setOptions($id, $values);
        }
        self::$cache = [];
    }

    /**
     * 
     * @param string $filename File to extract
     * @param boolean $extractFilesInActiveDir
     * @throws \Exception
     * @return array
     */
    static public function extractExport(string $filename, bool $extractFilesInActiveDir = false): array
    {
        $result = [];
        $app = App::get();
        if (!is_file($filename)) {
            throw new \Exception('Import file not found!', 2);
        }
        $tempArchiveFilename = sys_get_temp_dir() . '/bearcms-theme-import-' . uniqid() . '.zip';
        copy($filename, $tempArchiveFilename);
        $filename = null; // safe to use below
        $zip = new \ZipArchive();
        if ($zip->open($tempArchiveFilename) === true) {

            try {

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
                $result['id'] = $manifest['themeID'];

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
                            if (array_search($extension, ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp', 'avif']) === false) {
                                throw new \Exception('Invalid file (' . $filenameInArchive . ')!', 9);
                            }
                            $dataKey = (!$extractFilesInActiveDir ? '.temp/bearcms/files/themeimage/' : 'bearcms/files/themeimage/') . md5($filenameInArchive . '-' . uniqid()) . '.' . $extension;
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
                $result['values'] = $values;
                $zip->close();
                unlink($tempArchiveFilename);
                return $result;
            } catch (\Exception $e) {
                $zip->close();
                unlink($tempArchiveFilename);
                throw $e;
            }
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
        } else {
            $decodedValue = json_decode($value, true);
            if (!is_array($decodedValue)) {
                $result['value'] = $value;
            } else {
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
            }
        }

        if ($includeFiles) {
            $addFiles = function ($value) use (&$result): void {
                $isFilename = function ($value) {
                    return strpos($value, 'appdata://') === 0 || strpos($value, 'data:') === 0 || strpos($value, 'addon:') === 0;
                };
                if (is_array($value)) {
                    foreach ($value as $_value) {
                        if (is_string($_value)) {
                            if ($isFilename($_value)) {
                                $result['files'][] = $_value;
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
                        $result['files'][] = $value;
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
     * @param array $valueDetails
     * @return mixed
     */
    static function valueDetailsToString(array $valueDetails)
    {
        if (empty($valueDetails['states'])) {
            unset($valueDetails['states']);
        }
        if (!isset($valueDetails['states']) && !is_array($valueDetails['value'])) {
            return $valueDetails['value'];
        }
        return json_encode($valueDetails);
    }

    /**
     * 
     * @param mixed $value
     * @param string $propertyName
     * @return array
     */
    static function getValueCSSPropertyValues($value, string $propertyName): array
    {
        $valueDetails = self::getValueDetails($value);
        $result = [];
        $search = function (array $cssValues) use (&$result, $propertyName): void {
            if (isset($cssValues[$propertyName])) {
                $result[$cssValues[$propertyName]] = 1;
            }
        };
        if (is_array($valueDetails['value'])) {
            $search($valueDetails['value']);
        }
        foreach ($valueDetails['states'] as $state) {
            if (is_array($state[1])) {
                $search($state[1]);
            }
        }
        return array_keys($result);
    }

    /**
     * 
     * @param string $value
     * @param boolean $forAttributeSelector
     * @return string
     */
    static function escapeCSSValue(string $value, bool $forAttributeSelector = false): string
    {
        if ($forAttributeSelector) {
            $charsToEscape = ['"', '\\'];
        } else {
            $charsToEscape = ['!', ';', '{', '}'];
            $singleQuotesCount = substr_count($value, '\'');
            $doubleQuotesCount = substr_count($value, '"');
            if ($singleQuotesCount > 0 && $doubleQuotesCount > 0) { // Escape both to preserve them (Chrome converts single quotes to double quotes)
                $charsToEscape[] = '"';
                $charsToEscape[] = '\'';
            } elseif ($singleQuotesCount > 0) {
                if ($singleQuotesCount % 2 === 1) { // Uneven number
                    $charsToEscape[] = '\'';
                }
            } elseif ($doubleQuotesCount > 0) {
                if ($doubleQuotesCount % 2 === 1) { // Uneven number
                    $charsToEscape[] = '"';
                }
            }
        }
        foreach ($charsToEscape as $charToEscepe) {
            if (strpos($value, $charToEscepe) !== false) {
                $value = implode('\\' . $charToEscepe, explode($charToEscepe, $value));
            }
        }
        return $value;
    }

    /**
     * Parses a value in the following format: :state1(arg1=value1,arg2):state2:state3(arg3)
     * @param string $state
     * @return array Returns a value in the following format: [state1=>[arg1=>value1,arg2=>true], state2=>[], ...]
     */
    static function getStateCombinationDetails(string $state): array
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
                    if (count($argTextParts) === 2) {
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

    /**
     * 
     * @param array $options
     * @param boolean $includeDetails
     * @param boolean $optimizeForCompatibility
     * @param boolean $includeEditorData
     * @return array
     */
    static public function getOptionsHTMLData(array $options, bool $includeDetails = false, bool $optimizeForCompatibility = false, $includeEditorData = false): array
    {
        $app = App::get();

        $cssRules = [];
        $cssCode = '';
        $details = [];
        $linkTags = [];
        $elementsDefaultValues = [];

        $addAssetDetails = function (string $filename) use ($app, &$details, $includeDetails, $optimizeForCompatibility): void {
            if (!isset($details['assets'])) {
                $details['assets'] = [];
            }
            if ($includeDetails) {
                $realFilename = \BearCMS\Internal\Data::getRealFilename($filename, true);
                if ($realFilename === null) {
                    $assetDetails = ['width' => null, 'height' => null];
                } else {
                    $realFilenameWithoutOptions = \BearCMS\Internal\Data::removeFilenameOptions($realFilename);
                    $assetDetails = $app->assets->getDetails($realFilenameWithoutOptions, ['width', 'height']);
                }
                $details['assets'][$filename] = ['width' => $assetDetails['width'], 'height' => $assetDetails['height']];
            } else {
                $details['assets'][$filename] = [];
            }
            if ($optimizeForCompatibility) {
                if (strpos($filename, '.webp') !== false) {
                    $details['assets'][str_replace('.webp', '.webp.convert-to-png', $filename)] = $details['assets'][$filename];
                }
                if (strpos($filename, '.avif') !== false) {
                    $details['assets'][str_replace('.avif', '.avif.convert-to-png', $filename)] = $details['assets'][$filename];
                }
            }
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

        $supportedStates = [ // type => default id
            'size' => 'size',
            'screenSize' => 'screen-size',
            'pageType' => 'page-type',
            'hover' => 'hover',
            'focus' => 'focus',
            'active' => 'active',
            'firstChild' => 'first-child',
            'lastChild' => 'last-child',
            'checked' => 'checked',
            'visibility' => 'visibility',
            'tags' => 'tags'
        ];

        $updateFontFamily = function (string $fontName) use ($webSafeFonts, &$details, &$linkTags): string {
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

        $addCSSRule = function (string $mediaQuery, string $selector, string $value) use (&$cssRules): void {
            if ($value === '') {
                return;
            }
            if (!isset($cssRules[$mediaQuery])) {
                $cssRules[$mediaQuery] = [];
            }
            if (!isset($cssRules[$mediaQuery][$selector])) {
                $cssRules[$mediaQuery][$selector] = '';
            }
            $cssRules[$mediaQuery][$selector] .= $value;
        };

        $replaceVariables = function (string $content, $value, $defaultValue = '') use ($updateFontFamily) {
            if ($value === null) {
                $value = '';
            }
            if ($defaultValue === null) {
                $defaultValue = '';
            }
            $getCSSValueAsArray = function ($value) {
                return is_array($value) ? $value : ($value === '' ? [] : json_decode($value, true));
            };
            $search = [];
            $replace = [];
            for ($mode = 0; $mode < 2; $mode++) {
                $isEncodedMode = $mode === 1;
                $valueMatch = $isEncodedMode ? rawurlencode('{value}') : '{value}';
                if (strpos($content, $valueMatch) !== false) {
                    $search[] = $valueMatch;
                    $valueToSet = is_array($value) ? json_encode($value) : $value;
                    if ($valueToSet === '') {
                        $valueToSet = is_array($defaultValue) ? json_encode($defaultValue) : (string)$defaultValue;
                    }
                    $replace[] = $isEncodedMode ? rawurlencode($valueToSet) : $valueToSet;
                }
                $cssPropertyMatch = $isEncodedMode ? rawurlencode('{cssPropertyValue') : '{cssPropertyValue';
                if (strpos($content, $cssPropertyMatch) !== false) {
                    $valueAsArray = $getCSSValueAsArray($value);
                    $defaultValueAsArray = $getCSSValueAsArray($defaultValue);
                    $matches = [];
                    $expression = $isEncodedMode ? rawurlencode('{cssPropertyValue(') . '(.*?)' . rawurlencode(')}') : '{cssPropertyValue\((.*?)\)}';
                    preg_match_all('/' . $expression . '/', $content, $matches);
                    foreach ($matches[0] as $i => $match) {
                        $args = $matches[1][$i];
                        $args = explode(',', $isEncodedMode ? rawurldecode($args) : $args);
                        $propertyName = trim($args[0]);
                        $propertyDefaultValue = isset($args[1]) ? trim($args[1]) : '';
                        $propertyOptions = isset($args[2]) ? trim($args[2]) : '';
                        $search[] = $match;
                        $valueToSet = isset($valueAsArray[$propertyName]) ? $valueAsArray[$propertyName] : (isset($defaultValueAsArray[$propertyName]) ? $defaultValueAsArray[$propertyName] : $propertyDefaultValue);
                        if (isset($propertyOptions[0])) {
                            $propertyOptions = explode('|', $propertyOptions);
                            foreach ($propertyOptions as $propertyOption) {
                                if ($propertyOption === 'fontName') {
                                    $valueToSet = $updateFontFamily($valueToSet);
                                }
                            }
                        }
                        $valueToSet = self::escapeCSSValue($valueToSet);
                        $replace[] = $isEncodedMode ? rawurlencode($valueToSet) : $valueToSet;
                    }
                }
                $cssPropertyMatch = $isEncodedMode ? rawurlencode('{cssPropertyTransition') : '{cssPropertyTransition';
                if (strpos($content, $cssPropertyMatch) !== false) {
                    $valueAsArray = $getCSSValueAsArray($value);
                    $defaultValueAsArray = $getCSSValueAsArray($defaultValue);
                    $expression = $isEncodedMode ? rawurlencode('{cssPropertyTransition(') . '(.*?)' . rawurlencode(')}') : '{cssPropertyTransition\((.*?)\)}'; // list of properties
                    preg_match_all('/' . $expression . '/', $content, $matches);
                    foreach ($matches[0] as $i => $match) {
                        $transitionProperties = [];
                        $args = $matches[1][$i];
                        $args = explode(',', $isEncodedMode ? rawurldecode($args) : $args);
                        foreach ($args as $arg) {
                            $propertyName = 'transition--' . $arg;
                            $transitionValue = isset($valueAsArray[$propertyName]) ? $valueAsArray[$propertyName] : (isset($defaultValueAsArray[$propertyName]) ? $defaultValueAsArray[$propertyName] : '');
                            if ($transitionValue !== '') {
                                $transitionProperties[] = $arg . ' ' . $transitionValue;
                            }
                        }
                        $search[] = $match;
                        $valueToSet = !empty($transitionProperties) ? implode(',', $transitionProperties) : '';
                        $replace[] = $isEncodedMode ? rawurlencode($valueToSet) : $valueToSet;
                    }
                }
            }
            if (!empty($search)) {
                $content = str_replace($search, $replace, $content);
                if (strpos($content, ':;') !== false) { // has empty values after replace
                    $currentProperties = explode(';', $content);
                    $newProperties = [];
                    foreach ($currentProperties as $property) {
                        if (substr($property, -1, 1) !== ':') {
                            $newProperties[] = $property;
                        }
                    }
                    $content = implode(";", $newProperties);
                }
            }
            return $content;
        };

        $getCSSRuleValue = function ($value) use ($updateFontFamily): string {
            if (!is_array($value)) {
                return '';
            }
            $result = '';
            $transitionProperties = [];
            foreach ($value as $propertyName => $propertyValue) {
                if ($propertyName === 'font-family') {
                    $propertyValue = $updateFontFamily($propertyValue);
                }
                if (strpos($propertyName, 'transition--') === 0) {
                    $transitionProperties[] = substr($propertyName, 12) . ' ' . $propertyValue;
                    continue;
                }
                $result .= $propertyName . ':' . self::escapeCSSValue($propertyValue) . ';';
            }
            if (!empty($transitionProperties)) {
                $result .= 'transition:' . implode(',', $transitionProperties) . ';';
            }
            return $result;
        };

        $getCodeOptionCssRule = function (array $args, $value, int $optionIndex, ?int $stateIndex = null): string {
            if (is_string($value) && strlen($value) > 0) {
                return '--css-to-attribute-data-bearcms-element-event-' . $optionIndex . ($stateIndex !== null ? '-' . $stateIndex : '') . ':' . implode('+', $args) . ' call ' . self::escapeCSSValue($value) . ';';
            }
            return '';
        };

        $getCodeOptionStatesCssRules = function (array $states, array $statesTypes, int $optionIndex) use ($getCodeOptionCssRule) {
            $result = [];
            foreach ($states as $stateIndex => $stateData) {
                $stateDetails = self::getStateCombinationDetails($stateData[0]);
                foreach ($stateDetails as $stateName => $stateArgs) {
                    $stateValue = trim((string)$stateData[1]);
                    if (isset($stateValue[0], $statesTypes[$stateName])) {
                        $stateType = $statesTypes[$stateName];
                        if ($stateType === 'visibility') {
                            $result[] = $getCodeOptionCssRule(array_keys($stateArgs), $stateValue, $optionIndex, $stateIndex);
                        }
                    }
                }
            }
            return $result;
        };

        $replaceStateSelectorsInSelector = function (string $selector, array $statesNames, array &$replacedStateSelectors): string {
            if (strpos($selector, 'stateSelector') !== false) {
                $matches = [];
                $search = [];
                $replace = [];
                preg_match_all('/{stateSelector\((.*?)\)}/', $selector, $matches);
                foreach ($matches[0] as $i => $match) {
                    $stateName = $matches[1][$i];
                    $search[] = $match;
                    if (array_search($stateName, $statesNames) !== false) {
                        $replace[] = ':' . $stateName;
                        $replacedStateSelectors[] = $stateName;
                    } else {
                        $replace[] = '';
                    }
                }
                return str_replace($search, $replace, $selector);
            }
            return $selector;
        };

        $getStateCSSRules = function (string $state, string $value, array $statesTypes, string $selector, int $optionIndex, ?int $stateIndex = null) use ($replaceStateSelectorsInSelector, $optimizeForCompatibility): array {
            $cssMediaQueries = []; // array of array of strings
            $attributes = [];
            $cssTagStates = '';
            $unsupportedStates = '';
            $result = [];
            $stateParts = self::getStateCombinationDetails($state);

            $replacedStateSelectors = [];
            $selector = $replaceStateSelectorsInSelector($selector, array_keys($stateParts), $replacedStateSelectors);
            $selectorParts = explode(' ', $selector);
            $selectorLastPart = $selectorParts[count($selectorParts) - 1];
            $cssStates = $selector;

            foreach ($stateParts as $name => $args) {
                if (isset($statesTypes[$name])) {
                    $stateType = $statesTypes[$name];
                    if ($stateType === 'size') {
                        $deprecated = [
                            'minWidth' => 'min-width',
                            'maxWidth' => 'max-width',
                            'minHeight' => 'min-height',
                            'maxHeight' => 'max-height',
                        ];
                        foreach ($deprecated as $oldKey => $newKey) {
                            if (isset($args[$oldKey])) {
                                $args[$newKey] = $args[$oldKey];
                                unset($args[$oldKey]);
                            }
                        }
                        $responsiveAttributeRule = [];
                        if (isset($args['min-width'])) {
                            $responsiveAttributeRule[] = 'w>' . $args['min-width'];
                        }
                        if (isset($args['max-width'])) {
                            $responsiveAttributeRule[] = 'w<=' . $args['max-width'];
                        }
                        if (isset($args['min-height'])) {
                            $responsiveAttributeRule[] = 'h>' . $args['min-height'];
                        }
                        if (isset($args['max-height'])) {
                            $responsiveAttributeRule[] = 'h<=' . $args['max-height'];
                        }
                        if (!empty($responsiveAttributeRule)) {
                            $responsiveAttributeRule = implode('&&', $responsiveAttributeRule);
                            $attributeKey = 'bearcms-' . $optionIndex . '-' . $stateIndex . '-s';
                            $cssStates .= '[data-' . $attributeKey . ']';
                            $attributes['data-responsive-attributes-' . $attributeKey] = $responsiveAttributeRule . '=>data-' . $attributeKey;
                        }
                    } else if ($stateType === 'screenSize') {
                        $deprecated = [
                            'minWidth' => 'min-width',
                            'maxWidth' => 'max-width',
                            'minHeight' => 'min-height',
                            'maxHeight' => 'max-height',
                        ];
                        foreach ($deprecated as $oldKey => $newKey) {
                            if (isset($args[$oldKey])) {
                                $args[$newKey] = $args[$oldKey];
                                unset($args[$oldKey]);
                            }
                        }
                        $cssMediaQuery = [];
                        if (isset($args['small'])) {
                            $cssMediaQuery[] = '(max-width:600px)';
                        } elseif (isset($args['medium'])) {
                            $cssMediaQuery[] = '(min-width:600px)';
                            $cssMediaQuery[] = '(max-width:1000px)';
                        } elseif (isset($args['large'])) {
                            $cssMediaQuery[] = '(min-width:1000px)';
                        } else {
                            if (isset($args['min-width'])) {
                                $cssMediaQuery[] = '(min-width:' . $args['min-width'] . 'px)';
                            }
                            if (isset($args['max-width'])) {
                                $cssMediaQuery[] = '(max-width:' . $args['max-width'] . 'px)';
                            }
                            if (isset($args['min-height'])) {
                                $cssMediaQuery[] = '(min-height:' . $args['min-height'] . 'px)';
                            }
                            if (isset($args['max-height'])) {
                                $cssMediaQuery[] = '(max-height:' . $args['max-height'] . 'px)';
                            }
                        }
                        if (!empty($cssMediaQuery)) {
                            $cssMediaQueries[] = [implode(' and ', $cssMediaQuery)];
                        }
                    } else if ($stateType === 'pageType') {
                        $cssSelectors = [];
                        foreach ($args as $argName => $argValue) {
                            $cssSelectors[] = 'html[data-bearcms-page-type="' . $argName . '"]';
                        }
                        if (!empty($cssSelectors)) {
                            $cssStates = ':is(' . implode(',', $cssSelectors) . ') ' . $cssStates;
                        }
                    } else if ($stateType === 'tags') {
                        foreach ($args as $argName => $argValue) {
                            if ($argName[0] === '!') {
                                $argName = substr($argName, 1);
                                $cssTagStates .= ':not(:is([data-bearcms-tags~="' . $argName . '"] ' . $selectorLastPart . ', [data-bearcms-tags~="' . $argName . '"]' . $selectorLastPart . '))';
                            } else {
                                $cssTagStates .= ':is([data-bearcms-tags~="' . $argName . '"] ' . $selectorLastPart . ', [data-bearcms-tags~="' . $argName . '"]' . $selectorLastPart . ')';
                            }
                        }
                    } else if ($stateType === 'visibility') {
                        if (!empty($args)) {
                            $cssSelectors = [];
                            $counter = 0;
                            foreach ($args as $argName => $argValue) {
                                $attributeKey = $optionIndex . '-' . $stateIndex . '-' . $counter . '-v';
                                $attributeName = 'data-bearcms-' . $attributeKey;
                                $cssSelectors[] = '[' . $attributeName . ']';
                                $attributes['data-bearcms-element-event-' . $attributeKey] = $argName . ' attribute ' . $attributeName;
                                $counter++;
                            }
                            $cssStates .= implode('', $cssSelectors);
                        }
                    } else if (array_search($stateType, ['hover', 'focus', 'active', 'firstChild', 'lastChild', 'checked']) !== false) {
                        if (array_search($name, $replacedStateSelectors) === false) {
                            $cssStates .= ':' . ($name === 'focus' ? 'focus-visible' : $name);
                        }
                    }
                } else {
                    $unsupportedStates .= ':' . $name;
                }
            }

            $getCombinations = function (array $list) { // combines arrays of strings
                $result = [];
                foreach ($list as $items) {
                    $resultCount = count($result);
                    $itemsCount = count($items);
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

            $cssMediaQueriesCombinations = $getCombinations($cssMediaQueries);
            if (empty($cssMediaQueriesCombinations)) {
                $cssMediaQueriesCombinations[] = [];
            }
            foreach ($cssMediaQueriesCombinations as $cssMediaQueryCombinations) {
                $selectorRuleToSet = $cssStates . $unsupportedStates;
                if (isset($cssTagStates[0])) {
                    $selectorRuleToSet = ':is(' . $selectorRuleToSet . ')' . $cssTagStates;
                }
                if ($optimizeForCompatibility && strpos($selectorRuleToSet, ':is(') !== false) { // support for old browsers (Safari for example)
                    $result[] = [implode(' and ', $cssMediaQueryCombinations), implode(':matches(', explode(':is(', $selectorRuleToSet)), $value];
                }
                $result[] = [implode(' and ', $cssMediaQueryCombinations), $selectorRuleToSet, $value];
            }
            if (!empty($attributes)) {
                $attributesCSS = '';
                foreach ($attributes as $attributeName => $attributeValue) {
                    $attributesCSS .= '--css-to-attribute-' . $attributeName . ':' . self::escapeCSSValue($attributeValue) . ';';
                }
                $result[] = ['', $selector, $attributesCSS];
            }

            return $result;
        };

        $walkOptions = function ($options, &$optionsValues) use (&$addCSSRule, &$cssCode, &$walkOptions, &$addAssetDetails, &$replaceVariables, &$getCSSRuleValue, &$replaceStateSelectorsInSelector, &$elementsDefaultValues, $supportedStates, $getCodeOptionCssRule, $getCodeOptionStatesCssRules, $getStateCSSRules, $includeEditorData): void {
            $hasOptionsValues = $optionsValues !== null;
            foreach ($options as $optionIndex => $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    //$value = isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value'], JSON_THROW_ON_ERROR) : $option->details['value']) : null; // array not used ???
                    $value = isset($option->details['value']) ? $option->details['value'] : (isset($option->details['defaultValue']) ? $option->details['defaultValue'] : null);
                    if ($hasOptionsValues && $value !== null) {
                        $optionsValues[$option->id] = $value;
                    }
                    $valueDetails = self::getValueDetails($value, true);
                    $optionType = $option->type;
                    if ($optionType === 'cssCode') {
                        $cssCode .= trim((string)$valueDetails['value']);
                    } else {
                        $isCodeOptionType = $optionType === 'code';

                        if ($optionType === 'image' || $optionType === 'css' || $optionType === 'cssBackground') {
                            foreach ($valueDetails['files'] as $filename) {
                                $addAssetDetails($filename);
                            }
                        }

                        $statesTypes = [];
                        if (isset($option->details['cssOptions'])) {
                            $cssOptions = $option->details['cssOptions'];
                            foreach ($supportedStates as $supportedStateType => $supportedStateDefaultID) {
                                foreach ($cssOptions as $cssOption) {
                                    if (strpos($cssOption, '/' . $supportedStateType . 'State') !== false) {
                                        $statesTypes[$supportedStateDefaultID] = $supportedStateType;
                                    }
                                }
                            }
                        }
                        if (isset($option->details['states'])) {
                            foreach ($option->details['states'] as $stateData) {
                                if (isset($stateData['type'], $supportedStates[$stateData['type']])) {
                                    $statesTypes[isset($stateData['id']) ? $stateData['id'] : $supportedStates[$stateData['type']]] = $stateData['type'];
                                }
                            }
                        }

                        if (isset($option->details['cssOutput'])) {
                            foreach ($option->details['cssOutput'] as $outputDefinition) {
                                if (is_array($outputDefinition) && isset($outputDefinition[0])) {
                                    if ($outputDefinition[0] === 'selector' && isset($outputDefinition[1])) {
                                        $selector = $outputDefinition[1];
                                        $replacedStateSelectors = [];
                                        $replacedStatesSelector = $replaceStateSelectorsInSelector($selector, [], $replacedStateSelectors);
                                        $valueDefinition = isset($outputDefinition[2]) ? $outputDefinition[2] : null;
                                        if ($isCodeOptionType) {
                                            $cssRuleValue = $getCodeOptionCssRule(['load'], $valueDetails['value'], $optionIndex);
                                            $addCSSRule('', $replacedStatesSelector, $cssRuleValue);
                                            $codeOptionCssRules = $getCodeOptionStatesCssRules($valueDetails['states'], $statesTypes, $optionIndex);
                                            foreach ($codeOptionCssRules as $cssRuleValue) {
                                                $addCSSRule('', $selector, $cssRuleValue);
                                            }
                                        } else {
                                            $cssRuleValue = $valueDefinition !== null ? $replaceVariables($valueDefinition, $valueDetails['value']) : $getCSSRuleValue($valueDetails['value']);
                                            $addCSSRule('', $replacedStatesSelector, $cssRuleValue);
                                            foreach ($valueDetails['states'] as $stateIndex => $stateData) {
                                                $stateValue = $stateData[1];
                                                $cssRuleValue = $valueDefinition !== null ? $replaceVariables($valueDefinition, $stateValue, $valueDetails['value']) : $getCSSRuleValue($stateValue);
                                                if ($cssRuleValue !== '') {
                                                    $stateCssRules = $getStateCSSRules($stateData[0], $cssRuleValue, $statesTypes, $selector, $optionIndex, $stateIndex);
                                                    foreach ($stateCssRules as $cssRule) {
                                                        $addCSSRule($cssRule[0], $cssRule[1], $cssRule[2]);
                                                    }
                                                }
                                            }
                                        }
                                    } elseif ($outputDefinition[0] === 'rule' && isset($outputDefinition[1], $outputDefinition[2])) {
                                        $addCSSRule('', $outputDefinition[1], $outputDefinition[2]);
                                    }
                                }
                            }
                        }
                    }
                } elseif ($option instanceof \BearCMS\Themes\Theme\Options\Group) {
                    $hasElementSelector = isset($option->details['internalElementSelector']);
                    $groupOptionValues = $includeEditorData && ($hasElementSelector || $hasOptionsValues) ? [] : null;
                    $walkOptions($option->getList(), $groupOptionValues);
                    if ($includeEditorData && $hasElementSelector) {
                        $elementSelectorData = $option->details['internalElementSelector'];
                        $idPrefix = $elementSelectorData[0];
                        $temp = [];
                        foreach ($groupOptionValues as $id => $value) {
                            $temp[str_replace($idPrefix, '', $id)] = $value;
                        }
                        $groupOptionValues = $temp;
                        $elementsDefaultValues[$elementSelectorData[1]] = $groupOptionValues;
                    }
                    if ($hasOptionsValues) {
                        $optionsValues = array_merge($optionsValues, $groupOptionValues);
                    }
                }
            }
        };

        $optionsValues = null; // not needed
        $walkOptions($options, $optionsValues);

        $hasResponsiveAttributes = false;
        $hasEventAttributes = false;
        $hasRepeaterAttributes = false;

        //$repeaterWidths = [];
        $style = '';
        foreach ($cssRules as $mediaQuery => $mediaQueryRules) {
            if ($mediaQuery !== '') {
                $style .= '@media ' . $mediaQuery . '{';
            }
            foreach ($mediaQueryRules as $selector => $value) {
                $valueHasResponsiveAttributes = false;
                if (strpos($value, '--css-to-attribute-data-responsive-attributes') !== false) {
                    $value .= '--css-to-attribute-data-responsive-attributes:*;';
                    $hasResponsiveAttributes = true;
                    $valueHasResponsiveAttributes = true;
                }
                if (strpos($value, '--css-to-attribute-data-bearcms-element-event') !== false) {
                    $value .= '--css-to-attribute-data-bearcms-element-event:*;';
                    $hasEventAttributes = true;
                }
                if (strpos($value, '--css-to-attribute-data-bearcms-repeater-type') !== false) {
                    if (!$valueHasResponsiveAttributes) {
                        $value .= '--css-to-attribute-data-responsive-attributes:*;';
                    }
                    $repeaterWidths = [
                        'small' => 300,
                        'medium' => 430,
                        'large' => 600
                    ];
                    foreach ($repeaterWidths as $repeaterWidthName => $repeaterWidthValue) {
                        $repeaterWidthExpression = [];
                        for ($i = 0; $i <= 100; $i++) {
                            $startWidth = $i * $repeaterWidthValue;
                            $endWidth =  ($i + 1) * $repeaterWidthValue;
                            $attributeExpression = 'data-bearcms-repeater-' . $repeaterWidthName . '=' . ($i + 1);
                            if ($endWidth > 3000) {
                                $repeaterWidthExpression[] = 'w>' . $startWidth . '=>' . $attributeExpression;
                                break;
                            } else {
                                $repeaterWidthExpression[] = 'w>' . $startWidth . '&&w<' . $endWidth . '=>' . $attributeExpression;
                            }
                        }
                        $value .= '--css-to-attribute-data-responsive-attributes-bearcms-repeater-' . $repeaterWidthName . ':' . implode(',', $repeaterWidthExpression) . ';';
                    }
                    $hasRepeaterAttributes = true;
                }
                // if (strpos($value, '--css-to-attribute-data-bearcms-repeater-widths') !== false) {
                //     $matches = [];
                //     preg_match_all('/\-\-css\-to\-attribute\-data\-bearcms\-repeater\-widths\:(.*?)\;/', $value, $matches);
                //     if (isset($matches[1], $matches[1][0])) {
                //         $repeaterWidths[] = $matches[1][0];
                //     }
                // }
                if ($optimizeForCompatibility && (strpos($value, '.webp') !== false || strpos($value, '.avif') !== false)) { // support for old browsers
                    $optimizedValue = $value;
                    $optimizedValue = implode('.webp.convert-to-png', explode('.webp', $optimizedValue));
                    $optimizedValue = implode('.avif.convert-to-png', explode('.avif', $optimizedValue));
                    $style .= $selector . '{' . $optimizedValue . '}';
                    $style .= '@supports(rotate:0deg){' . $selector . '{' . $value . '}}';
                } else {
                    $style .= $selector . '{' . $value . '}';
                }
            }
            if ($mediaQuery !== '') {
                $style .= '}';
            }
        }

        $html = '';
        if (!empty($linkTags)) {
            $html .= implode('', $linkTags);
        }
        if ($style !== '') {
            $html .= '<style>' . $style . '</style>';
        }
        if ($cssCode !== '') {
            $html .= '<style>' . $cssCode . '</style>'; // Positioned in different style tag just in case it's invalid
        }
        if (strpos($style, '--css-to-attribute-') !== false) {
            $html .= '<link rel="client-packages-embed" name="cssToAttributes">';
        }
        if ($hasResponsiveAttributes) {
            $html .= '<link rel="client-packages-embed" name="responsiveAttributes">';
        }
        if ($hasEventAttributes) {
            $html .= '<link rel="client-packages-embed" name="-bearcms-element-events">';
        }
        if ($hasRepeaterAttributes) {
            $html .= '<link rel="client-packages-embed" name="-bearcms-repeater">';
        }

        if ($includeEditorData) {
            if (!empty($elementsDefaultValues)) {
                $html .= '<script type="bearcms-editor-elements-default-values">' . json_encode($elementsDefaultValues) . '</script>';
            }
            if (!empty($details['googleFonts'])) {
                $html .= '<script type="bearcms-editor-google-fonts">' . json_encode(array_keys($details['googleFonts'])) . '</script>';
            }
        }

        if ($html !== '') {
            $html = '<html><head>' . $html . '</head></html>';
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
                    if ($filename === '') {
                        continue;
                    }
                    try {
                        $options = ['cacheMaxAge' => 999999999];
                        $filenameOptions = Internal\Data::getFilenameOptions($filename);
                        $filenameWithoutOptions = Internal\Data::removeFilenameOptions($filename);
                        if (strpos($filenameWithoutOptions, '.webp.convert-to-png') !== false) {
                            $filenameWithoutOptions = str_replace('.webp.convert-to-png', '.webp', $filenameWithoutOptions);
                            $options['outputType'] = 'png';
                        }
                        if (strpos($filenameWithoutOptions, '.avif.convert-to-png') !== false) {
                            $filenameWithoutOptions = str_replace('.avif.convert-to-png', '.avif', $filenameWithoutOptions);
                            $options['outputType'] = 'png';
                        }
                        $realFilename = \BearCMS\Internal\Data::getRealFilename($filenameWithoutOptions, true);
                        if (!empty($filenameOptions)) {
                            $options = array_merge($options, Internal\Assets::convertFileOptionsToAssetOptions($filenameOptions));
                        }
                        $replace[] = $realFilename !== null ? $appAssets->getURL($realFilename, $options) : '';
                        $search[] = $filename; // Must be after getURL, because there may be an exception
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
}
