<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;
use BearCMS\Internal\CurrentTheme;

class Themes
{

    /**
     * Local cache
     * 
     * @var array 
     */
    private static $cache = [];

    /**
     * 
     * @param string $id
     * @param array|callable $options
     */
    public function add(string $id, $options = [])
    {
        $app = App::get();
        $currentThemeID = CurrentTheme::getID();
        $currentUserID = $app->bearCMS->currentUser->exists() ? $app->bearCMS->currentUser->getID() : null;
        $initialize = $id === $currentThemeID;
        if ($initialize && is_callable($options)) {
            $options = call_user_func($options);
        }
        if ($app->hooks->exists('bearCMSThemeAdd')) {
            if (is_callable($options)) {
                $options = call_user_func($options);
            }
            $app->hooks->execute('bearCMSThemeAdd', $id, $options);
        }

        \BearCMS\Internal\Themes::add($id, $options);

        // Initialize current theme
        if ($initialize && isset($options['initialize']) && is_callable($options['initialize'])) {
            call_user_func($options['initialize'], $this->getOptionsValues($currentThemeID, $currentUserID));
        }

        if ($app->hooks->exists('bearCMSThemeAdded')) {
            $app->hooks->execute('bearCMSThemeAdded', $id, $options);
        }

        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getIDs(): array
    {
        return array_keys(\BearCMS\Internal\Themes::$list);
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getManifest(string $id): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $result = \BearCMS\Internal\Themes::getManifest($id);
        $styles = \BearCMS\Internal\Themes::getStyles($id);
        $result['styles'] = [];
        foreach ($styles as $style) {
            $result['styles'][] = [
                'id' => $style['id'],
                'name' => $style['name'],
                'media' => $style['media']
            ];
        }
        return $result;
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getOptions(string $id): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        return \BearCMS\Internal\Themes::getOptions($id);
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getStyleValues(string $id, string $styleID): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $styles = \BearCMS\Internal\Themes::getStyles($id);
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
     */
    public function addDefault()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        require $context->dir . '/themes/universal/index.php';
    }

    /**
     * 
     * @param mixed $definition
     * @return $this Returns a reference to itself.
     */
    public function defineElementOption($definition)
    {
        \BearCMS\Internal\Themes::defineElementOption($definition);
        return $this;
    }

    /**
     * 
     * @return \BearCMS\Themes\OptionsDefinition
     */
    public function makeOptionsDefinition(): \BearCMS\Themes\OptionsDefinition
    {
        return new \BearCMS\Themes\OptionsDefinition();
    }

    /**
     * 
     * @return \BearCMS\Themes\Options
     */
    public function getOptionsValues(string $id, string $userID = null): ?\BearCMS\Themes\Options
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $localCacheKey = 'options-' . $id . '-' . $userID;
        if (!isset(self::$cache[$localCacheKey])) {
            $app = App::get();
            $cacheKey = \BearCMS\Internal\Themes::getCacheItemKey($id, $userID);
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
                    $userOptions = $app->bearCMS->data->themes->getUserOptions($id, $userID);
                    if (is_array($userOptions)) {
                        $currentValues = $userOptions;
                    }
                }
                if ($currentValues === null) {
                    $currentValues = $app->bearCMS->data->themes->getOptions($id);
                }
                $themeOptions = \BearCMS\Internal\Themes::getOptions($id);
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
                    $html = '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
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
                        $filename = $app->bearCMS->data->getRealFilename($key);
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
    public function export(string $id): string
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            throw new \Exception('Theme does not exists!');
        }
        $app = App::get();
        $optionsValues = $this->getOptionsValues($id);
        $values = $optionsValues->toArray();
        $filesToAttach = [];
        $filesInValues = \BearCMS\Internal\Themes::getFilesInValues($values);
        $filesKeysToUpdate = [];
        foreach ($filesInValues as $key) {
            $filename = $app->bearCMS->data->getRealFilename($key);
            if ($filename !== null) {
                $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($key, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
                $filesToAttach[$attachmentName] = $filename;
                $filesKeysToUpdate[$key] = 'data:' . $attachmentName;
            }
        }
        $values = \BearCMS\Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);

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
    public function import(string $fileDataKey, string $id, string $userID = null): void
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
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

            $filesInValues = \BearCMS\Internal\Themes::getFilesInValues($values);
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

            $values = \BearCMS\Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);
            if ($hasUser) {
                $app->bearCMS->data->themes->setUserOptions($id, $userID, $values);
            } else {
                $app->bearCMS->data->themes->setOptions($id, $values);
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
    public function applyUserValues(string $id, string $userID): void
    {
        $app = App::get();
        $values = $app->bearCMS->data->themes->getUserOptions($id, $userID);
        if (is_array($values)) {
            $filesInValues = \BearCMS\Internal\Themes::getFilesInValues($values);
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
            $values = \BearCMS\Internal\Themes::updateFilesInValues($values, $filesKeysToUpdate);
            $app->bearCMS->data->themes->setOptions($id, $values);
            $app->bearCMS->data->themes->discardUserOptions($id, $userID);
            self::$cache = [];
        }
    }

}
