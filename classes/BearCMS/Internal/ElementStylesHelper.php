<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Themes as InternalThemes;
use BearCMS\Internal\Data\ElementsSharedStyles;
use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\Data\UploadsSize;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementStylesHelper
{

    /**
     * 
     */
    static $cache = [];

    /**
     * 
     * @param string|null $styleID
     * @param array|null $styleValue
     * @param array|null $elementDefaultValue
     * @return string
     */
    static function getElementRealStyleID(?string $elementStyleID = null, ?array $elementStyleValue = null, ?array $elementDefaultValue = null): string
    {
        if ($elementStyleID !== null) {
            return $elementStyleID;
        }
        if ($elementStyleValue === null || empty($elementStyleValue)) {
            return 'default';
        }
        if ($elementStyleValue !== null && $elementDefaultValue !== null && serialize($elementStyleValue) === serialize($elementDefaultValue)) {
            return 'default';
        }
        return 'custom';
    }

    /**
     * 
     * @param string|null $elementStyleID
     * @param array|null $elementStyleValue
     * @param array|null $elementDefaultValue
     * @return array [realStyleID, realStyleValue]
     */
    static function getElementRealStyleData(?string $elementStyleID = null, ?array $elementStyleValue = null, ?array $elementDefaultValue = null): array
    {
        $realStyleID = self::getElementRealStyleID($elementStyleID, $elementStyleValue, $elementDefaultValue);
        if ($realStyleID === 'default') {
            return [$realStyleID, null];
        } elseif ($realStyleID === 'custom') {
            return [$realStyleID, $elementStyleValue];
        }
        $cacheKey = 'shared-style-value-' . $realStyleID;
        if (!array_key_exists($cacheKey, self::$cache)) {
            $sharedStyleData = ElementsSharedStyles::get($realStyleID);
            self::$cache[$cacheKey] = is_array($sharedStyleData) && isset($sharedStyleData['style']) ? $sharedStyleData['style'] : null;
        }
        return [$realStyleID, self::$cache[$cacheKey]];
    }

    /**
     * 
     * @param string $elementID
     * @param string $styleID
     * @return string|null
     */
    static function getElementStyleClassName(string $elementID, string $styleID): ?string
    {
        if ($styleID === 'custom') {
            return 'bearcms-element-style-' . substr(base_convert(md5($elementID), 16, 36), 0, 10);
        } elseif ($styleID !== 'default') {
            return 'bearcms-element-style-id-' . substr(base_convert(md5($styleID), 16, 36), 0, 10);
        }
        return null;
    }

    /**
     * 
     * @param string $elementID
     * @param string $styleID
     * @return string|null
     */
    static function getElementStyleSelector(string $elementID, string $styleID): ?string
    {
        $className = self::getElementStyleClassName($elementID, $styleID);
        if ($className === null) {
            return null;
        }
        return str_repeat('.' . $className, 3); // todo improve. Maybe use @layer; increase $times if conflicts
    }

    /**
     * 
     * @param string|null $type
     * @return array
     */
    static function getSharedStylesList(?string $type = null): array
    {
        $list = ElementsSharedStyles::getList();
        if ($type === null) {
            return $list;
        }
        return array_values(array_filter($list, function ($styleData) use ($type) {
            return $styleData['type'] === $type;
        }));
    }

    /**
     * 
     * @param string $suffix
     * @param [type] $context
     * @param boolean $checkIfExists
     * @return string
     */
    static function generateSharedStyleID(string $suffix = '', $context = null, bool $checkIfExists = true): string
    {
        $generateID = function (string $data) use ($suffix) {
            return base_convert(md5($data), 16, 36) . $suffix;
        };
        if ($context !== null) {
            $id = $generateID(serialize($context));
            if ($checkIfExists && ElementsSharedStyles::get($id) !== null) {
                throw new \Exception('The element ID generated exists (' . $id . ')');
            }
            return $id;
        }
        for ($i = 0; $i < 100; $i++) {
            $id = $generateID(uniqid('', true));
            if ($checkIfExists && ElementsSharedStyles::get($id) !== null) {
                continue;
            }
            return $id;
        }
        throw new \Exception('Too much retries!');
    }

    /**
     * 
     * @param string $type
     * @param string $name
     * @param array $data
     * @return string
     */
    static function addSharedStyle(string $type, string $name, array $data = []): string
    {
        $styleID = self::generateSharedStyleID();
        $styleData = [];
        $styleData['id'] = $styleID;
        $styleData['type'] = $type;
        $styleData['name'] = $name;
        $styleData['lastChangeTime'] = time();
        $styleData['style'] = $data;
        ElementsSharedStyles::set($styleID, $styleData);
        return $styleID;
    }

    /**
     * 
     * @param string $styleID
     * @param string $name
     * @return void
     */
    static function setSharedStyleName(string $styleID, string $name): void
    {
        $styleData = ElementsSharedStyles::get($styleID);
        if (!is_array($styleData)) {
            throw new \Exception('Shared style (' . $styleID . ') not found!');
        }
        $styleData['name'] = $name;
        $styleData['lastChangeTime'] = time();
        ElementsSharedStyles::set($styleID, $styleData);
    }

    /**
     * 
     * @param string $styleID
     * @return string
     */
    static function duplicateSharedStyle(string $styleID): string
    {
        $styleData = ElementsSharedStyles::get($styleID);
        if (!is_array($styleData)) {
            throw new \Exception('Shared style (' . $styleID . ') not found!');
        }
        Localization::setAdminLocale();
        $styleData['name'] =  sprintf(__('bearcms.elementStyle.CopyOfStyle'), $styleData['name']);
        Localization::restoreLocale();
        $styleID = self::generateSharedStyleID();
        $styleData['id'] = $styleID;
        $styleData['lastChangeTime'] = time();
        $styleData['style'] = ElementStylesHelper::duplicateStyleValues($styleData['style']);
        ElementsSharedStyles::set($styleID, $styleData);
        return $styleID;
    }

    /**
     * 
     * @param string $styleID
     * @return void
     */
    static function deleteSharedStyle(string $styleID): void
    {
        $styleData = ElementsSharedStyles::get($styleID);
        if (is_array($styleData) && isset($styleData['style']) && isset($styleData['style'])) {
            $filesInValues = InternalThemes::getFilesInValues($styleData['style']);
            ElementsDataHelper::deleteElementStyleFiles($filesInValues);
        }
        ElementsSharedStyles::delete($styleID);
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return string
     */
    static function createSharedStyleFromCustom(string $elementID, ?string $containerID = null): string
    {
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        if ($elementData === null) {
            throw new \Exception('Element not found (' . $elementID . ')!');
        }
        $styleValues = isset($elementData['style']) ? ElementStylesHelper::duplicateStyleValues($elementData['style']) : [];
        Localization::setAdminLocale();
        $name = __('bearcms.elementStyle.CopyOfCustom');
        Localization::restoreLocale();
        return self::addSharedStyle($elementData['type'], $name, $styleValues);
    }

    /**
     * 
     * @param string $elementType
     * @param array $values
     * @param string $selector
     * @return array|null
     */
    static function getOptions(string $elementType, array $values, string $selector): ?array
    {
        if (isset(InternalThemes::$elementsOptions[$elementType])) {
            Localization::setAdminLocale();
            $options = new \BearCMS\Themes\Theme\Options();
            $callback = InternalThemes::$elementsOptions[$elementType];
            if (is_array($callback)) {
                $callback = $callback[1];
            }
            call_user_func($callback, $options, '', $selector, InternalThemes::OPTIONS_CONTEXT_ELEMENT, []);
            $editorValues = [];
            foreach ($values as $name => $value) {
                $editorValues[$name] = $value;
            }
            Localization::restoreLocale();
            return [
                'definition' => InternalThemes::optionsToArray($options),
                'values' => $editorValues
            ];
        }
        return null;
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @param string|null $styleID
     * @return void
     */
    static function setElementStyleID(string $elementID, ?string $containerID = null, ?string $styleID = null): void
    {
        $elementData = ElementsDataHelper::getElement($elementID, $containerID);
        if ($elementData === null) {
            throw new \Exception('Element not found (' . $elementID . ')!');
        }
        if ($styleID === null) {
            $styleID = 'default';
        }
        $elementData['styleID'] = $styleID;
        if (!isset($elementData['style']) && $styleID === 'default') { // todo move to optimizeElement
            unset($elementData['styleID']);
        }
        ElementsDataHelper::setElement($elementData, $containerID);
    }

    /**
     * 
     * @param string $elementID
     * @param string $containerID
     * @param string $styleID
     * @param array $values
     * @return void
     */
    static function setElementStyleValues(string $elementID, string $containerID, string $styleID, array $values): void
    {
        $filesToDelete = [];

        $getUpdateValues = function ($oldValues, $newValues) use (&$filesToDelete) {
            $app = App::get();
            $filesInOldStyle = InternalThemes::getFilesInValues($oldValues);
            $newElementStyle = [];
            foreach ($newValues as $name => $value) {
                $value = trim((string)$value);
                if (strlen($value) > 0) {
                    if (!isset($newElementStyle[$name]) || $newElementStyle[$name] !== $value) {
                        $newElementStyle[$name] = $value;
                    }
                }
            }
            $filesInNewStyle = InternalThemes::getFilesInValues($newElementStyle, true);
            $filesToUpdate = [];
            $duplicatedDataKeys = [];
            $filesToKeep = [];
            foreach ($filesInNewStyle as $filename) {
                $filenameOptions = InternalData::getFilenameOptions($filename);
                $filenameWithoutOptions = InternalData::removeFilenameOptions($filename);
                $dataKey = InternalData::getFilenameDataKey($filenameWithoutOptions);
                if ($dataKey !== null && strpos($dataKey, '.temp/bearcms/files/elementstyleimage/') === 0) {
                    $newDataKey = 'bearcms/files/elementstyleimage/' . pathinfo($dataKey, PATHINFO_BASENAME);
                    if (!isset($duplicatedDataKeys[$dataKey])) {
                        $app->data->duplicate($dataKey, $newDataKey);
                        UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        $duplicatedDataKeys[$dataKey] = true;
                    }
                    $newFilenameWithOptions = InternalData::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                    $filesToDelete[] = $filenameWithoutOptions;
                } else {
                    if (array_search($filenameWithoutOptions, $filesInOldStyle) === false) {
                        $realFilename = InternalData::getRealFilename($filenameWithoutOptions);
                        $newDataKey = 'bearcms/files/elementstyleimage/temp.' . pathinfo($realFilename, PATHINFO_EXTENSION);
                        $newDataKey = InternalData::generateNewFilename($newDataKey);
                        $newFilename = $app->data->getFilename($newDataKey);
                        copy($realFilename, $newFilename);
                        UploadsSize::add($newDataKey, filesize($newFilename));
                        $newFilenameWithOptions = InternalData::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                        $filesToUpdate[$filename] = $newFilenameWithOptions;
                    } else {
                        $filesToKeep[] = $filenameWithoutOptions;
                    }
                }
            }
            $filesToDelete = array_merge($filesToDelete, array_diff($filesInOldStyle, $filesToKeep));
            $newElementStyle = InternalThemes::updateFilesInValues($newElementStyle, $filesToUpdate);
            return $newElementStyle;
        };

        if ($styleID === 'custom') {
            $oldStyleValues = null;
            $isStructuralElement = false;
            $elementData = InternalDataElements::getElement($elementID);
            if (is_array($elementData) && isset($elementData['type'])) {
                $oldStyleValues = isset($elementData['style']) ? $elementData['style'] : [];
            } else {
                $containerData = InternalDataElements::getContainer($containerID);
                if (is_array($containerData)) {
                    $elementData = ElementsDataHelper::getContainerDataElement($containerData, $elementID, 'structural');
                    if (is_array($elementData)) {
                        $oldStyleValues = isset($elementData['style']) ? $elementData['style'] : [];
                        $isStructuralElement = true;
                    }
                }
            }

            if ($oldStyleValues !== null) {
                $elementData['style'] = $getUpdateValues($oldStyleValues, $values);
                if (empty($elementData['style'])) {
                    unset($elementData['style']);
                }
                if (!isset($elementData['style']) && isset($elementData['styleID']) && $elementData['styleID'] === 'default') { // todo move to optimizeElement // can reset the custom style while selected the default one
                    unset($elementData['styleID']);
                }
                if ($isStructuralElement) {
                    $containerData = ElementsDataHelper::setContainerDataElement($containerData, $elementData);
                    InternalDataElements::setContainer($containerID, $containerData);
                    InternalDataElements::dispatchContainerChangeEvent($containerID);
                } else {
                    InternalDataElements::setElement($elementID, $elementData);
                    InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
                }
            }
        } else { // is shared style
            $sharedStyleData = ElementsSharedStyles::get($styleID);
            if ($sharedStyleData !== null) {
                $oldStyleValues = isset($sharedStyleData['style']) ? $sharedStyleData['style'] : [];
                $sharedStyleData['style'] = $getUpdateValues($oldStyleValues, $values);
                $sharedStyleData['lastChangeTime'] = time();
                ElementsSharedStyles::set($styleID, $sharedStyleData);
            }
        }

        ElementsDataHelper::deleteElementStyleFiles($filesToDelete);
    }

    /**
     * 
     * @param array $values
     * @return array
     */
    static function duplicateStyleValues(array $values): array
    {
        $filenames = InternalThemes::getFilesInValues($values, true);
        if (!empty($filenames)) {
            $app = App::get();
            $duplicatedDataKeys = [];
            $filesToUpdate = [];
            foreach ($filenames as $filename) { // it's not expected to have 'addon:' here
                $filenameOptions = InternalData::getFilenameOptions($filename);
                $filenameDataKey = InternalData::getFilenameDataKey($filename);
                if ($filenameDataKey !== null && $app->data->exists($filenameDataKey)) {
                    if (isset($duplicatedDataKeys[$filenameDataKey])) {
                        $newDataKey = $duplicatedDataKeys[$filenameDataKey];
                    } else {
                        $newDataKey = InternalData::generateNewFilename($filenameDataKey);
                        $app->data->duplicate($filenameDataKey, $newDataKey);
                        UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        $duplicatedDataKeys[$filenameDataKey] = $newDataKey;
                    }
                    $newFilenameWithOptions = InternalData::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                }
            }
            return InternalThemes::updateFilesInValues($values, $filesToUpdate);
        }
        return $values;
    }

    /**
     * 
     * @param string $styleID
     * @return array
     */
    static function getSharedStyleUploadsSizeItems(string $styleID): array
    {
        $result = [];
        $styleData = ElementsSharedStyles::get($styleID);
        if (is_array($styleData) && isset($styleData['style'])) {
            $filenames = InternalThemes::getFilesInValues($styleData['style']);
            foreach ($filenames as $filename) {
                $dataKey = InternalData::getFilenameDataKey($filename);
                if ($dataKey !== null) {
                    $result[] = $dataKey;
                }
            }
        }
        return $result;
    }
}
