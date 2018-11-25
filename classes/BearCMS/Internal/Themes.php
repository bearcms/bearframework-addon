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

class Themes
{

    static $elementsOptions = [];
    static $pagesOptions = [];
    static $announcements = [];
    static $cache = [];

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

    static public function getIDs(): array
    {
        return array_keys(self::$announcements);
    }

    static function get(string $id): ?\BearCMS\Themes\Theme
    {
        if (isset(self::$announcements[$id])) {
            if (is_callable(self::$announcements[$id])) {
                $theme = new \BearCMS\Themes\Theme($id);
                call_user_func(self::$announcements[$id], $theme);
                self::$announcements[$id] = $theme;
            }
            return self::$announcements[$id];
        }
        return null;
    }

    static function getVersion(string $id): ?string
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        return $theme->version;
    }

    static function getManifest(string $id, $updateMediaFilenames = true): array
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

    static function getOptionsSchema(string $id): ?array
    {
        $theme = self::get($id);
        if ($theme === null) {
            return null;
        }
        if (is_callable($theme->optionsSchema)) {
            $result = call_user_func($theme->optionsSchema);
            if ($result instanceof \BearCMS\Themes\OptionsSchema) {
                $result = $result->toArray();
            }
            if (!is_array($result)) {
                throw new \Exception('Invalid theme options value for theme ' . $id . '!');
            }
            return $result;
        }
        return [];
    }

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
        }
        return [];
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
//    public function getStyleValues(string $id, string $styleID): ?array
//    {
//        if (!isset(self::$announcements[$id])) {
//            return null;
//        }
//        $styles = Internal\Themes::getStyles($id);
//        foreach ($styles as $style) {
//            if ($style['id'] === $styleID) {
//                if (isset($style['values'])) {
//                    return $style['values'];
//                }
//            }
//        }
//        return [];
//    }

    /**
     * 
     * @return \BearCMS\Themes\Options
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
            $useCache = $cacheKey !== null;
            $resultData = null;
            if ($useCache) {
                $resultData = $app->cache->getValue($cacheKey);
                if ($resultData !== null) {
                    $resultData = json_decode($resultData, true);
                }
            }
            if ($resultData === null) {
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
                if (!empty($themeOptions)) {
                    $cssRules = [];
                    $cssCode = '';
                    $walkOptions = function($options) use (&$values, &$cssRules, &$cssCode, $currentValues, &$walkOptions) {
                        foreach ($options as $option) {
                            if (isset($option['id'])) {
                                $optionID = $option['id'];
                                if (isset($currentValues[$optionID])) {
                                    $value = $currentValues[$optionID];
                                } else {
                                    $value = isset($option['defaultValue']) ? (is_array($option['defaultValue']) ? json_encode($option['defaultValue']) : $option['defaultValue']) : null;
                                }
                                $values[$optionID] = $value;

                                if (isset($option['type'])) {
                                    $optionType = $option['type'];
                                    if ($optionType === 'cssCode') {
                                        $cssCode .= $value;
                                    } else {
                                        if (isset($option['cssOutput'])) {
                                            foreach ($option['cssOutput'] as $outputDefinition) {
                                                if (is_array($outputDefinition)) {
                                                    if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                                        $selector = $outputDefinition[1];
                                                        $selectorVariants = ['', '', ''];
                                                        if ($optionType === 'css' || $optionType === 'cssText' || $optionType === 'cssTextShadow' || $optionType === 'cssBackground' || $optionType === 'cssPadding' || $optionType === 'cssMargin' || $optionType === 'cssBorder' || $optionType === 'cssRadius' || $optionType === 'cssShadow' || $optionType === 'cssSize' || $optionType === 'cssTextAlign') {
                                                            $temp = isset($value[0]) ? json_decode($value, true) : [];
                                                            if (is_array($temp)) {
                                                                foreach ($temp as $key => $value) {
                                                                    $pseudo = substr($key, -6);
                                                                    if ($pseudo === ':hover') {
                                                                        $selectorVariants[1] .= substr($key, 0, -6) . ':' . $value . ';';
                                                                    } else if ($pseudo === 'active') { // optimization
                                                                        if (substr($key, -7) === ':active') {
                                                                            $selectorVariants[2] .= substr($key, 0, -7) . ':' . $value . ';';
                                                                        } else {
                                                                            $selectorVariants[0] .= $key . ':' . $value . ';';
                                                                        }
                                                                    } else {
                                                                        $selectorVariants[0] .= $key . ':' . $value . ';';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if ($selectorVariants[0] !== '') {
                                                            if (!isset($cssRules[$selector])) {
                                                                $cssRules[$selector] = '';
                                                            }
                                                            $cssRules[$selector] .= $selectorVariants[0];
                                                        }
                                                        if ($selectorVariants[1] !== '') {
                                                            if (!isset($cssRules[$selector . ':hover'])) {
                                                                $cssRules[$selector . ':hover'] = '';
                                                            }
                                                            $cssRules[$selector . ':hover'] .= $selectorVariants[1];
                                                        }
                                                        if ($selectorVariants[2] !== '') {
                                                            if (!isset($cssRules[$selector . ':active'])) {
                                                                $cssRules[$selector . ':active'] = '';
                                                            }
                                                            $cssRules[$selector . ':active'] .= $selectorVariants[2];
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
                                }
                            }
                            if (isset($option['options'])) {
                                $walkOptions($option['options']);
                            }
                        }
                    };
                    $walkOptions($themeOptions);
                    $style = '';
                    foreach ($cssRules as $key => $value) {
                        $style .= $key . '{' . $value . '}';
                    }
                    $linkTags = [];
                    $applyFontNames = function($text) use (&$linkTags) {
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

                        $matches = [];
                        preg_match_all('/font\-family\:(.*?);/', $text, $matches);
                        foreach ($matches[0] as $i => $match) {
                            $fontName = $matches[1][$i];
                            if (isset($webSafeFonts[$fontName])) {
                                $text = str_replace($match, 'font-family:' . $webSafeFonts[$fontName] . ';', $text);
                            } elseif (strpos($fontName, 'googlefonts:') === 0) {
                                $googleFontName = substr($fontName, strlen('googlefonts:'));
                                $text = str_replace($match, 'font-family:\'' . $googleFontName . '\';', $text);
                                if (!isset($linkTags[$googleFontName])) {
                                    $linkTags[$googleFontName] = '<link href="//fonts.googleapis.com/css?family=' . urlencode($googleFontName) . '" rel="stylesheet" type="text/css" />';
                                }
                            }
                        }
                        return $text;
                    };
                    $style = $applyFontNames($style);
                    $cssCode = trim($cssCode); // Positioned in different style tag just in case it's invalid
                    if (!empty($linkTags) || $cssCode !== '') {
                        $html = '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
                    } else {
                        $html = '';
                    }
                }
                $resultData = [$values, $html];
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
                    foreach ($matches[1] as $key) {
                        $filename = Internal2::$data2->getRealFilename($key);
                        if ($filename !== null) {
                            $search[] = $key;
                            $replace[] = $app->assets->getUrl($filename, ['cacheMaxAge' => 999999999]);
                        }
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
        $values = $optionsValues->toArray();
        $filesToAttach = [];
        $filesInValues = Internal\Themes::getFilesInValues($values);
        $filesKeysToUpdate = [];
        foreach ($filesInValues as $key) {
            $filename = Internal2::$data2->getRealFilename($key);
            if ($filename !== null) {
                $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($key, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
                $filesToAttach[$attachmentName] = $filename;
                $filesKeysToUpdate[$key] = 'data:' . $attachmentName;
            }
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
     * @param string $id The theme ID
     * @param string $userID The user ID
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

    static public function getCacheItemKey(string $id, $userID = null)
    {
        $version = self::getVersion($id);
        if ($version === null) {
            return null;
        }
        return 'bearcms-theme-options-' . Config::$dataCachePrefix . '-' . md5($id) . '-' . md5($version) . '-' . md5($userID) . '-3';
    }

    /**
     * 
     * @param type $values
     * @return array
     */
    static public function getFilesInValues($values): array
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
     * @param type $values
     * @param type $keysToUpdate
     * @return array
     */
    static public function updateFilesInValues($values, $keysToUpdate): array
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
