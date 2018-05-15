<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
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
            $value = null;
            if ($useCache) {
                $value = $app->cache->getValue($cacheKey);
                if ($value !== null) {
                    $value = json_decode($value, true);
                }
            }
            if ($value === null) {
                $optionsData = $this->getOptionsData($id, $userID);
                $values = [];
                foreach ($optionsData as $name => $optionData) {
                    $values[$name] = $optionData[0];
                }
                $value = [$values, $this->getOptionsHtml($optionsData)];
                if ($useCache) {
                    $app->cache->set($app->cache->make($cacheKey, json_encode($value)));
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

            $value[1] = $applyImageUrls($value[1]);
            self::$cache[$localCacheKey] = new \BearCMS\Themes\Options($value[0], $value[1]);
        }
        return self::$cache[$localCacheKey];
    }

    /**
     * Returns HTML code generated by the options
     * 
     * @return string The HTML code for the options
     */
    private function getOptionsHtml($optionsData): string
    {
        $linkTags = [];
        $result = [];
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

        $cssCode = '';
        foreach ($optionsData as $optionData) {
            $optionValue = (string) $optionData[0];
            $optionDefinition = $optionData[1];
            $optionType = $optionDefinition['type'];
            if ($optionType === 'cssCode') {
                $cssCode .= $optionValue;
            } else {
                if (isset($optionDefinition['cssOutput'])) {
                    foreach ($optionDefinition['cssOutput'] as $outputDefinition) {
                        if (is_array($outputDefinition)) {
                            if (isset($outputDefinition[0], $outputDefinition[1]) && $outputDefinition[0] === 'selector') {
                                $selector = $outputDefinition[1];
                                $selectorVariants = ['', '', ''];
                                if ($optionType === 'css' || $optionType === 'cssText' || $optionType === 'cssTextShadow' || $optionType === 'cssBackground' || $optionType === 'cssPadding' || $optionType === 'cssMargin' || $optionType === 'cssBorder' || $optionType === 'cssRadius' || $optionType === 'cssShadow' || $optionType === 'cssSize' || $optionType === 'cssTextAlign') {
                                    $temp = isset($optionValue[0]) ? json_decode($optionValue, true) : [];
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
                                    if (!isset($result[$selector])) {
                                        $result[$selector] = '';
                                    }
                                    $result[$selector] .= $selectorVariants[0];
                                }
                                if ($selectorVariants[1] !== '') {
                                    if (!isset($result[$selector . ':hover'])) {
                                        $result[$selector . ':hover'] = '';
                                    }
                                    $result[$selector . ':hover'] .= $selectorVariants[1];
                                }
                                if ($selectorVariants[2] !== '') {
                                    if (!isset($result[$selector . ':active'])) {
                                        $result[$selector . ':active'] = '';
                                    }
                                    $result[$selector . ':active'] .= $selectorVariants[2];
                                }
                            } elseif (isset($outputDefinition[0], $outputDefinition[1], $outputDefinition[2]) && $outputDefinition[0] === 'rule') {
                                $selector = $outputDefinition[1];
                                if (!isset($result[$selector])) {
                                    $result[$selector] = '';
                                }
                                $result[$selector] .= $outputDefinition[2];
                            }
                        }
                    }
                }
            }
        }
        $style = '';
        foreach ($result as $key => $value) {
            $style .= $key . '{' . $value . '}';
        }
        $style = $applyFontNames($style);
        $cssCode = trim($cssCode); // Positioned in different style tag just in case it's invalid
        return '<html><head>' . implode('', $linkTags) . '<style>' . $style . '</style>' . ($cssCode !== '' ? '<style>' . $cssCode . '</style>' : '') . '</head></html>';
    }

    /**
     * 
     * @return array
     */
    private function getOptionsData(string $id, string $userID = null): array
    {
        $localCacheKey = 'options-data-' . $id . '-' . $userID;
        $app = App::get();
        if (!isset(self::$cache[$localCacheKey])) {
            $result = [];
            $values = null;
            if ($userID !== null) {
                $userOptions = $app->bearCMS->data->themes->getTempOptions($id, $userID);
                if (is_array($userOptions)) {
                    $values = $userOptions;
                }
            }
            if ($values === null) {
                $values = $app->bearCMS->data->themes->getOptions($id);
            }
            $themeOptions = \BearCMS\Internal\Themes::getOptions($id);
            if (!empty($themeOptions)) {
                $walkOptions = function($options) use (&$result, $values, &$walkOptions) {
                    foreach ($options as $option) {
                        if (isset($option['id'])) {
                            if (isset($values[$option['id']])) {
                                $value = $values[$option['id']];
                            } else {
                                $value = isset($option['defaultValue']) ? (is_array($option['defaultValue']) ? json_encode($option['defaultValue']) : $option['defaultValue']) : null;
                            }
                            $result[$option['id']] = [$value, $option];
                        }
                        if (isset($option['options'])) {
                            $walkOptions($option['options']);
                        }
                    }
                };
                $walkOptions($themeOptions);
            }
            self::$cache[$localCacheKey] = $result;
        }
        return self::$cache[$localCacheKey];
    }

    private function getFilesFromValues($values)
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
        }
        return array_unique($result);
    }

    private function updateFilesInValues($values, $changes)
    {
        $search = [];
        $replace = [];
        foreach ($changes as $oldKey => $newKey) {
            $search[] = 'url(' . $oldKey . ')';
            $replace[] = 'url(' . $newKey . ')';
            $search[] = trim(json_encode('url(' . $oldKey . ')'), '"');
            $replace[] = trim(json_encode('url(' . $newKey . ')'), '"');
        }
        foreach ($values as $name => $value) {
            $values[$name] = str_replace($search, $replace, $values[$name]);
        }
        return $values;
    }

    public function export(string $id)
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $app = App::get();
        $values = [];
        $optionsData = $this->getOptionsData($id);
        foreach ($optionsData as $name => $optionData) {
            $values[$name] = $optionData[0];
        }
        $filesToAttach = [];
        $filesInValues = $this->getFilesFromValues($values);
        $filesValuesToUpdate = [];
        foreach ($filesInValues as $key) {
            $filename = $app->bearCMS->data->getRealFilename($key);
            if ($filename !== null) {
                $attachmentName = 'files/' . (sizeof($filesToAttach) + 1) . '.' . pathinfo($key, PATHINFO_EXTENSION); // the slash helps in import (shows if the value is encoded)
                $attachmentName = rtrim($attachmentName, '.');
                $filesToAttach[$attachmentName] = $filename;
                $filesValuesToUpdate[$key] = $attachmentName;
            }
        }
        $values = $this->updateFilesInValues($values, $filesValuesToUpdate);

        $manifest = [
            'themeID' => $id,
            'exportDate' => date('c')
        ];

        $archiveFileKey = '.temp/bearcms/theme-export-' . md5(uniqid()) . '.zip';
        $archiveFilename = $app->data->getFilename($archiveFileKey);
        $app->data->setValue($archiveFileKey . '_', 'temp'); // needed to make the dir for the archive file
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
        $app->data->delete($archiveFileKey . '_');
        return $archiveFileKey;
    }

    public function import(string $fileDataKey, string $id, string $userID = null)
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

            $changes = $this->getFilesFromValues($values);
            $filesValuesToUpdate = [];
            foreach ($changes as $key) {
                if (strpos($key, 'files/') !== 0) {
                    throw new \Exception('Invalid file (' . $key . ')!', 6);
                }
                $data = $zip->getFromName($key);
                if ($data !== false) {
                    $extension = pathinfo($key, PATHINFO_EXTENSION);
                    if(array_search($extension, ['jpg', 'jpeg', 'gif', 'png']) === false){
                        throw new \Exception('Invalid file (' . $key . ')!', 9);
                    }
                    $dataKey = ($hasUser ? '.temp/bearcms/files/themeimage/' : 'bearcms/files/themeimage/') . md5($key . '-' . uniqid()) . '.' . $extension;
                    $app->data->setValue($dataKey, $data);
                    $filesValuesToUpdate[$key] = 'data:' . $dataKey;
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
                        foreach ($filesValuesToUpdate as $dataKeyWithPrefix) {
                            $app->data->delete(substr($dataKeyWithPrefix, 5));
                        }
                        throw new \Exception('Invalid file (' . $key . ')!', 7);
                    }
                    $app->data->makePublic($dataKey);
                }
            }

            $dataKeysInDelete = [];
            $values = $this->updateFilesInValues($values, $filesValuesToUpdate);

            $optionsData = $this->getOptionsData($id, $userID);
            $currentValues = [];
            foreach ($optionsData as $name => $optionData) {
                $currentValues[$name] = $optionData[0];
            }
            $currentFilesInValues = $this->getFilesFromValues($currentValues);
            foreach ($currentFilesInValues as $key) {
                if (strpos($key, 'data:') === 0) {
                    $dataKeysInDelete[] = substr($key, 5);
                }
            }

            $dataToSet = [];
            $dataToSet['id'] = $id;
            if ($hasUser) {
                $dataToSet['userID'] = $userID;
            }
            $dataToSet['options'] = $values;
            $dataKey = $hasUser ? '.temp/bearcms/userthemeoptions/' . md5($userID) . '/' . md5($id) . '.json' : 'bearcms/themes/theme/' . md5($id) . '.json';
            $app->data->setValue($dataKey, json_encode($dataToSet));
            \BearCMS\Internal\Data::setChanged($dataKey);

            foreach ($dataKeysInDelete as $dataKeyInDelete) {
                if ($app->data->exists($dataKeyInDelete)) {
                    $app->data->rename($dataKeyInDelete, '.recyclebin/' . $dataKeyInDelete . '-' . uniqid());
                }
            }

            $zip->close();

            self::$cache = [];
            $cacheItemKey = $hasUser ? \BearCMS\Internal\Themes::getCacheItemKey($id, $userID) : \BearCMS\Internal\Themes::getCacheItemKey($id);
            if ($cacheItemKey !== null) {
                $app->cache->delete($cacheItemKey);
            }
        } else {
            throw new \Exception('Cannot open zip archive (' . $archiveFilename . ')', 8);
        }
    }

}
