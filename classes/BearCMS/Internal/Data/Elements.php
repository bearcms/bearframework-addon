<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal\Data;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Themes;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Elements
{

    /**
     * 
     * @var array
     */
    private static $disableOptimizeElementData = [];

    /**
     * 
     * @param string $sourceElementID
     * @param string $targetElementID
     * @return void
     */
    static function copyElement(string $sourceElementID, string $targetElementID): void
    {
        $app = App::get();
        $elementData = ElementsHelper::getElementData($sourceElementID);
        if ($elementData === null) {
            throw new \Exception('Source element (' . $sourceElementID . ') not found!');
        }
        $elementData['id'] = $targetElementID;
        $elementData['lastChangeTime'] = time();
        if (isset($elementData['type'])) {
            $componentName = array_search($elementData['type'], ElementsHelper::$elementsTypesCodes);
            if ($componentName !== false) {
                $options = ElementsHelper::$elementsTypesOptions[$componentName];
                if (isset($options['onDuplicate']) && is_callable($options['onDuplicate'])) {
                    $elementData['data'] = call_user_func($options['onDuplicate'], isset($elementData['data']) ? $elementData['data'] : []);
                }
            }
        }
        if (isset($elementData['style'])) {
            $fileKeys = Themes::getFilesInValues($elementData['style']);
            if (!empty($fileKeys)) {
                $filesKeysToUpdate = [];
                foreach ($fileKeys as $fileKey) {
                    if (substr($fileKey, 0, 5) === 'data:') {
                        $dataKay = substr($fileKey, 5);
                        if ($app->data->exists($dataKay)) {
                            $newDataKey = Data::generateNewFilename($dataKay);
                            $filesKeysToUpdate[$fileKey] = 'data:' . $newDataKey;
                            $app->data->duplicate($dataKay, $newDataKey);
                            UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        }
                    }
                }
                $elementData['style'] = Themes::updateFilesInValues($elementData['style'], $filesKeysToUpdate);
            }
        }
        $app->data->setValue('bearcms/elements/element/' . md5($elementData['id']) . '.json', json_encode($elementData));
    }

    /**
     * 
     * @param string $sourceContainerID
     * @param string $targetContainerID
     * @return void
     */
    static function copyContainer(string $sourceContainerID, string $targetContainerID): void
    {
        $app = App::get();
        $containerData = ElementsHelper::getContainerData($sourceContainerID);
        $newContainerData = $containerData;
        $newContainerData['id'] = $targetContainerID;
        $copiedElementIDs = [];
        $updateElementIDs = function ($elements) use (&$updateElementIDs, &$copiedElementIDs) {
            foreach ($elements as $index => $element) {
                if (isset($element['id'])) {
                    $oldItemID = $element['id'];
                    $newItemID = ElementsHelper::generateElementID('cc');
                    $elements[$index]['id'] = $newItemID;
                    $structuralElementData = ElementsHelper::getUpdatedStructuralElementData($element);
                    if ($structuralElementData !== null) {
                        if ($structuralElementData['type'] === 'floatingBox' || $structuralElementData['type'] === 'columns') {
                            if (isset($structuralElementData['elements'])) {
                                foreach ($structuralElementData['elements'] as $location => $locationElements) {
                                    $structuralElementData['elements'][$location] = $updateElementIDs($locationElements);
                                }
                                $elements[$index] = $structuralElementData;
                            }
                        } else if ($structuralElementData['type'] === 'flexibleBox') {
                            if (isset($structuralElementData['elements'])) {
                                $structuralElementData['elements'] = $updateElementIDs($structuralElementData['elements']);
                                $elements[$index] = $structuralElementData;
                            }
                        } else {
                            throw new \Exception('Unsupported type for an element');
                        }
                    } else {
                        $copiedElementIDs[$oldItemID] = $newItemID;
                    }
                } else {
                    throw new \Exception('Missing id for an element');
                }
            }
            return $elements;
        };
        $newContainerData['elements'] = $updateElementIDs($newContainerData['elements']);

        foreach ($copiedElementIDs as $sourceElementID => $targetElementID) {
            self::copyElement($sourceElementID, $targetElementID);
        }
        $app->data->setValue('bearcms/elements/container/' . md5($newContainerData['id']) . '.json', json_encode($newContainerData));
    }

    /**
     * 
     * @param string $containerID
     * @return integer
     */
    static function getContainerUploadsSize(string $containerID): int
    {
        $size = 0;
        $elementsIDs = ElementsHelper::getContainerElementsIDs($containerID);
        foreach ($elementsIDs as $elementID) {
            $size += self::getElementUploadsSize($elementID);
        }
        return $size;
    }

    /**
     * 
     * @param string $containerID
     * @return array
     */
    static function getContainerUploadsSizeItems(string $containerID): array
    {
        $result = [];
        $elementsIDs = ElementsHelper::getContainerElementsIDs($containerID);
        foreach ($elementsIDs as $elementID) {
            $result = array_merge($result, self::getElementUploadsSizeItems($elementID));
        }
        return $result;
    }

    /**
     * 
     * @param string $elementID
     * @return array
     */
    static function getElementUploadsSizeItems(string $elementID): array
    {
        $result = [];
        $elementData = ElementsHelper::getElementData($elementID);
        if ($elementData !== null) {
            if (isset($elementData['type'])) {
                $componentName = array_search($elementData['type'], ElementsHelper::$elementsTypesCodes);
                if ($componentName !== false) {
                    $options = ElementsHelper::$elementsTypesOptions[$componentName];
                    if (isset($options['getUploadsSizeItems']) && is_callable($options['getUploadsSizeItems'])) {
                        $result = array_merge($result, call_user_func($options['getUploadsSizeItems'], isset($elementData['data']) ? $elementData['data'] : []));
                    }
                }
            }
            if (isset($elementData['style'])) {
                $fileKeys = Themes::getFilesInValues($elementData['style']);
                foreach ($fileKeys as $fileKey) {
                    if (substr($fileKey, 0, 5) === 'data:') {
                        $result[] = substr($fileKey, 5);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $elementID
     * @return integer
     */
    static function getElementUploadsSize(string $elementID): int
    {
        $items = self::getElementUploadsSizeItems($elementID);
        $size = 0;
        foreach ($items as $key) {
            $size += (int) UploadsSize::getItemSize($key);
        }
        return $size;
    }

    /**
     * Optimizes the element's data
     * 
     * @return void
     */
    static function optimizeElementData(string $dataKey)
    {
        if (strpos($dataKey, 'bearcms/elements/element/') !== 0) {
            return;
        }
        if (isset(self::$disableOptimizeElementData[$dataKey])) {
            return;
        }
        $app = App::get();
        $rawData = $app->data->getValue($dataKey);
        if ($rawData === null) {
            return;
        }
        $data = ElementsHelper::decodeElementRawData($rawData);
        if ($data === null) {
            return;
        }
        if (isset($data['type'], $data['data'])) {
            $componentName = array_search($data['type'], ElementsHelper::$elementsTypesCodes);
            if ($componentName !== false) {
                $options = ElementsHelper::$elementsTypesOptions[$componentName];
                if (isset($options['optimizeData']) && is_callable($options['optimizeData'])) {
                    $result = call_user_func($options['optimizeData'], $data['data']);
                    if (is_array($result)) {
                        $data['data'] = $result;
                        $app->data->duplicate($dataKey, '.recyclebin/bearcms/update-' . str_replace('.', '-', microtime(true)) . '-' . str_replace('/', '-', $dataKey));
                        self::$disableOptimizeElementData[$dataKey] = true;
                        $app->data->setValue($dataKey, json_encode($data));
                        unset(self::$disableOptimizeElementData[$dataKey]);
                    }
                }
            }
        }
    }
}
