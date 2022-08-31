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
        $app->data->setValue('bearcms/themes/active.json', json_encode(['id' => $id]));
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
            $envKey = md5(md5(serialize($elementsOptionsEnvKeyData)) . md5(serialize($pagesOptionsEnvKeyData)) . md5((string)$version) . md5('v8'));
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
                    $values = $themeOptions->getValues();
                    $htmlData = self::getOptionsHTMLData($themeOptions->getList(), true);
                }
                $resultData = [$values, $htmlData, $envKey];
            }
            if ($useCache) {
                if ($saveToCache) {
                    $app->cache->set($app->cache->make($cacheKey, json_encode($resultData)));
                }
                if ($saveToTempData) {
                    $app->data->setValue($tempDataKey, json_encode($resultData));
                }
            }
            $values = $resultData[0];
            $htmlData = $resultData[1];
            $assetsDetails = [];
            if (isset($htmlData['updates'], $htmlData['updates']['assets'])) {
                foreach ($htmlData['updates']['assets'] as $key => $details) {
                    $assetsDetails[$key] = $details;
                }
            }
            $html = self::processOptionsHTMLData($htmlData);

            self::$cache[$localCacheKey] = new \BearCMS\Themes\Theme\Customizations($values, $html, $assetsDetails);
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
        $filesInValues = self::getFilesInValues($values);
        $filesKeysToUpdate = [];
        foreach ($filesInValues as $filename) {
            $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($filename, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
            $filesToAttach[$attachmentName] = $filename;
            $filesKeysToUpdate[$filename] = 'data:' . $attachmentName;
        }
        $values = self::updateFilesInValues($values, $filesKeysToUpdate);

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
                $filename = Internal2::$data2->fixFilename($filename);
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

            $filesInValues = self::getFilesInValues($values);
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

            $values = self::updateFilesInValues($values, $filesKeysToUpdate);
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
            $filesInValues = self::getFilesInValues($values);
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
            $values = self::updateFilesInValues($values, $filesKeysToUpdate);
            Internal2::$data2->themes->setOptions($id, $values);
            Internal2::$data2->themes->discardUserOptions($id, $userID);
            self::$cache = [];
        }
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
            if (!is_string($value) || strlen($value) === 0) {
                continue;
            }
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
     * @param array $options
     * @param boolean $includeAssetsDetails
     * @return array
     */
    static public function getOptionsHTMLData(array $options, bool $includeAssetsDetails = false): array
    {
        $app = App::get();

        $cssRules = [];
        $cssCode = '';
        $updates = [];
        $linkTags = [];

        $addAssetUpdate = function (string $value) use ($app, &$updates, $includeAssetsDetails) {
            if (!isset($updates['assets'])) {
                $updates['assets'] = [];
            }
            if ($includeAssetsDetails) {
                $filename = Internal2::$data2->getRealFilename($value);
                if ($filename === null) {
                    $assetDetails = ['width' => null, 'height' => null];
                } else {
                    $assetDetails = $app->assets->getDetails($filename, ['width', 'height']);
                }
                $updates['assets'][$value] = ['width' => $assetDetails['width'], 'height' => $assetDetails['height']];
            } else {
                $updates['assets'][$value] = [];
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

        $updateFontFamily = function (string $fontName) use ($webSafeFonts, &$updates, &$linkTags) {
            // $matches = [];
            // preg_match_all('/font\-family\:(.*?);/', $text, $matches);
            // foreach ($matches[0] as $i => $match) {
            //    $fontName = $matches[1][$i];
            // }
            if (isset($webSafeFonts[$fontName])) {
                return $webSafeFonts[$fontName];
            } elseif (strpos($fontName, 'googlefonts:') === 0) {
                $googleFontName = substr($fontName, strlen('googlefonts:'));
                if (!isset($updates['googleFonts'])) {
                    $updates['googleFonts'] = [];
                }
                $updateKey = 'googlefont:' . md5($googleFontName);
                if (!isset($updates['googleFonts'][$updateKey])) {
                    $updates['googleFonts'][$updateKey] = $googleFontName;
                    $linkTags[] = '<link href="' . htmlentities($updateKey) . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"><noscript><link href="' . htmlentities($updateKey) . '" rel="stylesheet"></noscript>';
                }
                return $googleFontName;
            }
            return $fontName;
        };

        $walkOptions = function ($options) use (&$cssRules, &$cssCode, &$walkOptions, &$addAssetUpdate, $updateFontFamily) {
            foreach ($options as $option) {
                if ($option instanceof \BearCMS\Themes\Theme\Options\Option) {
                    $value = isset($option->details['value']) ? (is_array($option->details['value']) ? json_encode($option->details['value']) : $option->details['value']) : null;
                    $optionType = $option->type;
                    if ($optionType === 'cssCode') {
                        $cssCode .= $value;
                    } else {
                        if ($value !== null && strlen($value) > 0) {
                            if ($optionType === 'image') {
                                $addAssetUpdate($value);
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
                                                    $addAssetUpdate($_value2);
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
                        if (isset($option->details['cssOutput'])) {
                            foreach ($option->details['cssOutput'] as $outputDefinition) {
                                if (is_array($outputDefinition)) {
                                    if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                        $selector = $outputDefinition[1];
                                        $selectorStates = [];
                                        $addSelectorState = function (string $cssSelector, string $rule) use (&$selectorStates) {
                                            if (!isset($selectorStates[$cssSelector])) {
                                                $selectorStates[$cssSelector] = '';
                                            }
                                            $selectorStates[$cssSelector] .= $rule;
                                        };
                                        if ($optionType === 'css' || $optionType === 'cssText' || $optionType === 'cssTextShadow' || $optionType === 'cssBackground' || $optionType === 'cssPadding' || $optionType === 'cssMargin' || $optionType === 'cssBorder' || $optionType === 'cssRadius' || $optionType === 'cssShadow' || $optionType === 'cssSize' || $optionType === 'cssTextAlign' || $optionType === 'cssOpacity' || $optionType === 'cssRotation') {
                                            $temp = isset($value[0]) ? json_decode($value, true) : [];
                                            if (is_array($temp)) {
                                                if (isset($outputDefinition[2])) { // has selector value specified
                                                    $ruleValue = $outputDefinition[2];
                                                    $valuesToSearch = [];
                                                    $valuesToReplace = [];
                                                    $matches = [];
                                                    preg_match_all('/{cssPropertyValue\((.*?)\)}/', $ruleValue, $matches);
                                                    foreach ($matches[0] as $i => $match) {
                                                        $valuesToSearch[] = $match;
                                                        $cssPropertyName = $matches[1][$i];
                                                        $valueToSet = isset($temp[$cssPropertyName]) ? $temp[$cssPropertyName] : '';
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
                                                        $valueToSet = isset($temp[$cssPropertyName]) ? $temp[$cssPropertyName] : '';
                                                        if ($cssPropertyName === 'font-family') {
                                                            $valueToSet = $updateFontFamily($valueToSet);
                                                        }
                                                        $valuesToReplace[] = rawurlencode($valueToSet);
                                                    }
                                                    $addSelectorState('', str_replace($valuesToSearch, $valuesToReplace, $ruleValue));
                                                } else {
                                                    foreach ($temp as $key => $_value) {
                                                        if ($key === 'font-family') {
                                                            $_value = $updateFontFamily($_value);
                                                        }
                                                        $colonIndex = strpos($key, ':');
                                                        if ($colonIndex !== false) {
                                                            $addSelectorState(substr($key, $colonIndex), substr($key, 0, $colonIndex) . ':' . $_value . ';');
                                                        } else {
                                                            $addSelectorState('', $key . ':' . $_value . ';');
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (isset($outputDefinition[2])) { // has selector value specified
                                                $defaultValue = '';
                                                if ($optionType === 'htmlUnit') {
                                                    $defaultValue = '0';
                                                }
                                                $valueToSet = $value !== null && strlen($value) > 0 ? $value : $defaultValue;
                                                $valuesToSearch = ['{value}', rawurlencode('{value}')];
                                                $valuesToReplace = [$valueToSet, rawurlencode($valueToSet)];
                                                if ($optionType === 'font') {
                                                    $valueAsFontName = $updateFontFamily($valueToSet);
                                                    $valuesToSearch[] = '{valueAsFontName}';
                                                    $valuesToReplace[] = $valueAsFontName;
                                                    $valuesToSearch[] = rawurlencode('{valueAsFontName}');
                                                    $valuesToReplace[] = rawurlencode($valueAsFontName);
                                                }
                                                $addSelectorState('', str_replace($valuesToSearch, $valuesToReplace, $outputDefinition[2]));
                                            }
                                        }
                                        foreach ($selectorStates as $cssSelector => $stateValue) {
                                            $ruleName = $selector . $cssSelector;
                                            if (!isset($cssRules[$ruleName])) {
                                                $cssRules[$ruleName] = '';
                                            }
                                            $cssRules[$ruleName] .= $stateValue;
                                        }
                                    } elseif (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                                        $selector = $outputDefinition[1];
                                        if (!isset($cssRules[$selector])) {
                                            $cssRules[$selector] = '';
                                        }
                                        $cssRules[$selector] .= $outputDefinition[2];
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
        foreach ($cssRules as $key => $value) {
            if (strpos($value, '--rotation') !== false) {
                $value = 'transform:rotate(var(--rotation));' . $value;
            }
            $style .= $key . '{' . $value . '}';
        }

        $cssCode = trim($cssCode); // Positioned in different style tag just in case it's invalid

        if (!empty($linkTags) || $style !== '' || $cssCode !== '') {
            $html = '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
        } else {
            $html = '';
        }
        return [
            'html' => $html,
            'updates' => $updates
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
        $updates = isset($data['updates']) ? $data['updates'] : [];
        if (!empty($updates)) {
            $app = App::get();
            $search = [];
            $replace = [];
            if (isset($updates['assets'])) {
                $appAssets = $app->assets;
                foreach ($updates['assets'] as $updateKey => $assetDetails) {
                    try {
                        $filename = Internal2::$data2->getRealFilename($updateKey);
                        $search[] = $updateKey;
                        $replace[] = $appAssets->getURL($filename, ['cacheMaxAge' => 999999999]);
                    } catch (\Exception $e) { // May be file in an invalid dir
                        $search[] = $updateKey;
                        $replace[] = '';
                    }
                }
            }
            if (isset($updates['googleFonts'])) {
                $googleFontsEmbed = $app->googleFontsEmbed;
                foreach ($updates['googleFonts'] as $updateKey => $fontName) {
                    $search[] = $updateKey;
                    $replace[] = htmlentities($googleFontsEmbed->getURL($fontName));
                }
            }
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
