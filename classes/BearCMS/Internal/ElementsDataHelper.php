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
use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearCMS\Internal\Data\ElementsSharedStyles;
use BearCMS\Internal\Data\UploadsSize;
use BearCMS\Internal\ImportExport\ImportContext;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsDataHelper
{

    /**
     * 
     * @param array $elementData
     * @return string|null
     */
    static private function getElementTypeFromElementData(array $elementData): ?string
    {
        $type = null;
        if (isset($elementData['type'])) {
            $type = $elementData['type'];
        } elseif (isset($elementData['data'], $elementData['data']['type'])) { // old format
            $type = $elementData['data']['type'];
        }
        if ($type === 'column') {
            $type = 'columns';
        }
        return $type;
    }

    /**
     * 
     * @return array
     */
    static private function getStructuralElementsTypes(): array
    {
        return ['columns', 'floatingBox', 'flexibleBox', 'slider'];
    }

    /**
     * 
     * @param array $elementData
     * @return boolean
     */
    static function isColumnsElementContainerData(array $elementData): bool
    {
        return self::getElementTypeFromElementData($elementData) === 'columns';
    }

    /**
     * 
     * @param array $elementData
     * @return boolean
     */
    static function isFloatingBoxElementContainerData(array $elementData): bool
    {
        return self::getElementTypeFromElementData($elementData) === 'floatingBox';
    }

    /**
     * 
     * @param array $elementData
     * @return boolean
     */
    static function isFlexibleBoxElementContainerData(array $elementData): bool
    {
        return self::getElementTypeFromElementData($elementData) === 'flexibleBox';
    }

    /**
     * 
     * @param array $elementData
     * @return boolean
     */
    static function isSliderElementContainerData(array $elementData): bool
    {
        return self::getElementTypeFromElementData($elementData) === 'slider';
    }

    /**
     * 
     * @param array $elementData
     * @return boolean
     */
    static function isStructuralElementData(array $elementData): bool
    {
        return array_search(self::getElementTypeFromElementData($elementData), self::getStructuralElementsTypes()) !== false;
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return array|null
     */
    static function getElement(string $elementID, string $containerID = null): ?array
    {
        $elementData = InternalDataElements::getElement($elementID);
        if ($elementData !== null) {
            return $elementData;
        }
        if ($containerID !== null) {
            $containerData = InternalDataElements::getContainer($containerID);
            if ($containerData !== null) {
                $elementData = self::getContainerDataElement($containerData, $elementID);
                if ($elementData !== null && self::isStructuralElementData($elementData)) { // non structural items cannot be in the container
                    return $elementData;
                }
            }
        }
        return null;
    }

    /**
     * 
     * @param array $elementData
     * @param string|null $containerID
     * @return void
     */
    static function setElement(array $elementData, string $containerID = null): void
    {
        $elementID = $elementData['id'];
        if (self::isStructuralElementData($elementData)) {
            if ($containerID !== null) {
                $containerData = InternalDataElements::getContainer($containerID, true);
                $containerData = self::setContainerDataElement($containerData, $elementData, 'addLastIfNotFound');
                self::setLastChangeTime($containerData);
                InternalDataElements::setContainer($containerID, $containerData);
                InternalDataElements::dispatchContainerChangeEvent($containerID);
            } else {
                throw new \Exception('Container ID missing!');
            }
        } else {
            self::setLastChangeTime($elementData);
            InternalDataElements::setElement($elementID, $elementData);
            InternalDataElements::optimizeElement($elementID, $containerID);
            InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
        }
    }

    /**
     * 
     * @param string $type
     * @param array $data
     * @param string $containerID
     * @param array $target
     * @param array $options
     * @return string
     */
    static function addElement(string $type, array $data, string $containerID, array $target, array $options = []): string
    {
        $elementData = [];
        $elementData['type'] = $type;
        $elementData['data'] = $data;
        $elementID = self::generateElementID();
        $elementData['id'] = $elementID;
        $defaultElementStyle = self::getDefaultElementStyle($type);
        if (!empty($defaultElementStyle)) {
            $elementData['style'] = $defaultElementStyle;
        }
        $containerData = isset($options['containerData']) ? $options['containerData'] : InternalDataElements::getContainer($containerID, true);
        if (self::isStructuralElementData($elementData)) {
            $containerData['elements'][] = $elementData;
            $containerData = self::moveContainerDataElement($containerData, $elementID, $target);
            self::setLastChangeTime($containerData);
            InternalDataElements::setContainer($containerID, $containerData);
        } else {
            $containerData['elements'][] = ['id' => $elementID];
            $containerData = self::moveContainerDataElement($containerData, $elementID, $target);
            self::setLastChangeTime($elementData);
            self::setLastChangeTime($containerData);
            InternalDataElements::setElement($elementID, $elementData);
            InternalDataElements::setContainer($containerID, $containerData);
            InternalDataElements::optimizeElement($elementID, $containerID);
            InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
        }
        InternalDataElements::dispatchContainerChangeEvent($containerID);
        return $elementID;
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @param array $options
     * @return void
     */
    static function deleteElement(string $elementID, string $containerID = null, array $options = []): void
    {
        $updateContainer = isset($options['updateContainer']) ? $options['updateContainer'] : true;
        $recursivelyDeleteStructuralElementChildren = isset($options['recursivelyDeleteStructuralElementChildren']) ? $options['recursivelyDeleteStructuralElementChildren'] : true;
        $skipStructuralTypeCheck = isset($options['skipStructuralTypeCheck']) ? $options['skipStructuralTypeCheck'] : false;
        $elementData = InternalDataElements::getElement($elementID);
        $containerData = !$skipStructuralTypeCheck && $containerID !== null ? (isset($options['containerData']) ? $options['containerData'] : InternalDataElements::getContainer($containerID)) : null;
        $elementDataInContainer = null;
        if ($containerData !== null) {
            $elementDataInContainer = self::getContainerDataElement($containerData, $elementID);
        }
        $deleteResources = function (array $elementData) {
            if (isset($elementData['type'])) {
                $elementTypeDefinition = ElementsHelper::getElementTypeDefinition($elementData['type']);
                if ($elementTypeDefinition !== null && is_callable($elementTypeDefinition->onDelete)) {
                    call_user_func($elementTypeDefinition->onDelete, isset($elementData['data']) ? $elementData['data'] : []);
                }
            }
            if (isset($elementData['style'])) {
                self::deleteElementStyleFiles(InternalThemes::getFilesInValues($elementData['style']));
            }
        };
        if ($recursivelyDeleteStructuralElementChildren && $elementDataInContainer !== null && self::isStructuralElementData($elementDataInContainer)) {
            $childrenElementsData = self::getStructuralElementDataChildrenData($elementDataInContainer, 'all');
            foreach ($childrenElementsData as $childElementData) {
                if (self::isStructuralElementData($childElementData)) {
                    $deleteResources($childElementData);
                } else {
                    self::deleteElement($childElementData['id'], $containerID, ['updateContainer' => false, 'skipStructuralTypeCheck' => true]);
                }
            }
        }
        if ($elementData !== null) {
            $deleteResources($elementData);
        }
        if ($elementDataInContainer !== null) {
            $deleteResources($elementDataInContainer);
        }
        if ($elementData !== null) {
            InternalDataElements::deleteElement($elementID);
        }
        if ($updateContainer && $elementDataInContainer !== null) {
            $containerData = self::removeContainerDataElement($containerData, $elementDataInContainer['id']);
            self::setLastChangeTime($containerData);
            InternalDataElements::setContainer($containerID, $containerData);
            InternalDataElements::dispatchContainerChangeEvent($containerID);
        }
    }

    /**
     * 
     * @param string $containerID
     * @return void
     */
    static function deleteContainer(string $containerID): void
    {
        $containerData = InternalDataElements::getContainer($containerID);
        if ($containerData === null) {
            return;
        }
        $elementsIDs = self::getContainerDataElementsIDs($containerData, 'all');
        foreach ($elementsIDs as $elementID) {
            self::deleteElement($elementID, $containerID, ['updateContainer' => false, 'recursivelyDeleteStructuralElementChildren' => false, 'containerData' => $containerData]);
        }
        InternalDataElements::deleteContainer($containerID);
    }

    /**
     * 
     * @param array $filenames
     * @return void
     */
    static function deleteElementStyleFiles(array $filenames): void
    {
        if (!empty($filenames)) {
            $app = App::get();
            $recycleBinPrefix = '.recyclebin/bearcms/element-style-changes-' . str_replace('.', '-', microtime(true)) . '/';
            foreach ($filenames as $filename) {
                $filenameDataKey = InternalData::getFilenameDataKey($filename);
                if ($filenameDataKey !== null && (strpos($filenameDataKey, '.temp/bearcms/files/elementstyleimage/') === 0 || strpos($filenameDataKey, 'bearcms/files/elementstyleimage/') === 0)) {
                    if ($app->data->exists($filenameDataKey)) {
                        $app->data->rename($filenameDataKey, $recycleBinPrefix . $filenameDataKey);
                    }
                    UploadsSize::remove($filenameDataKey);
                }
            }
        }
    }

    /**
     * 
     * @param array $target
     * @return void
     */
    static private function setLastChangeTime(array &$target): void
    {
        $target['lastChangeTime'] = time();
    }

    /**
     * 
     * @param string $containerID
     * @return integer|null
     */
    static function getLastChangeTime(string $containerID): ?int
    {
        $dates = [];
        $containerData = InternalDataElements::getContainer($containerID);
        if (is_array($containerData)) {
            if (isset($containerData['lastChangeTime'])) {
                $dates[] = (int)$containerData['lastChangeTime'];
            }
            $elementsIDs = self::getContainerElementsIDs($containerID, 'nonStructural');
            foreach ($elementsIDs as $elementID) {
                $elementData = InternalDataElements::getElement($elementID);
                if (is_array($elementData) && isset($elementData['lastChangeTime'])) {
                    $dates[] = (int)$elementData['lastChangeTime'];
                }
            }
        }
        return empty($dates) ? null : max($dates);
    }

    /**
     * Returns a list of element IDs in rendered order (from top left)
     * 
     * @param string $containerID
     * @param string $type Available values: all, structural, nonStructural, image, text, ...
     * @return array
     */
    static function getContainerElementsIDs(string $containerID, string $type): array
    {
        $containerData = InternalDataElements::getContainer($containerID);
        if (!is_array($containerData)) {
            return [];
        }
        return self::getContainerDataElementsIDs($containerData, $type);
    }

    /**
     * Returns a list of element IDs in the rendered order (from top left)
     * 
     * @param array $containerData Elements container data or Structural element data
     * @param string $type Available values: all, structural, nonStructural
     * @return array
     */
    static function getContainerDataElementsIDs(array $containerData, string $type): array
    {
        $result = [];
        $elements = self::getContainerDataElementsData($containerData, $type);
        foreach ($elements as $element) {
            $result[] = $element['id'];
        }
        return $result;
    }

    /**
     * Returns a list of element IDs in the rendered order (from top left)
     * 
     * @param array $containerData Elements container data or Structural element data
     * @param string $type Available values: all, structural, nonStructural
     * @return array
     */
    static private function getContainerDataElementsData(array $containerData, string $type): array
    {
        $result = [];
        $structuralElementsTypes = self::getStructuralElementsTypes();
        self::walkContainerDataElements($containerData, function (array $elementData) use (&$result, $type, $structuralElementsTypes) {
            if (isset($elementData['id'])) {
                $elementType = isset($elementData['type']) ? $elementData['type'] : null; // nonStructural are outside (for now)
                $add = false;
                if ($type === 'all') {
                    $add = true;
                } elseif ($type === 'structural') {
                    if (array_search($elementType, $structuralElementsTypes) !== false) {
                        $add = true;
                    }
                } elseif ($type === 'nonStructural') {
                    if (array_search($elementType, $structuralElementsTypes) === false) {
                        $add = true;
                    }
                }
                if ($add) {
                    $result[] = $elementData;
                }
            }
        });
        return $result;
    }

    /**
     * Returns a list of element IDs in the rendered order (from top left)
     * 
     * @param array $elementData Structural element data
     * @param string $type Available values: all, structural, nonStructural
     * @return array
     */
    static function getStructuralElementDataChildrenIDs(array $elementData, string $type): array
    {
        $result = [];
        $elements = self::getStructuralElementDataChildrenData($elementData, $type);
        foreach ($elements as $element) {
            $result[] = $element['id'];
        }
        return $result;
    }

    /**
     * Returns a list of children element IDs in the rendered order (from top left)
     * 
     * @param array $elementData Structural element data
     * @param string $type Available values: all, structural, nonStructural
     * @return array
     */
    static private function getStructuralElementDataChildrenData(array $elementData, string $type): array
    {
        $result = [];
        $temp = ['elements' => [$elementData]];
        $children = self::getContainerDataElementsData($temp, $type);
        foreach ($children as $child) {
            if ($child['id'] !== $elementData['id']) {
                $result[] = $child;
            }
        }
        return $result;
    }

    /**
     * 
     * @param array $containerData
     * @param string $elementID
     * @param string|null $expectedType
     * @return array|null
     */
    static function getContainerDataElement(array $containerData, string $elementID, string $expectedType = null): ?array
    {
        $result = null;
        $structuralElementsTypes = self::getStructuralElementsTypes();
        self::walkContainerDataElements($containerData, function (array $elementData) use (&$result, $elementID, $expectedType, $structuralElementsTypes) {
            if ($elementData['id'] == $elementID) {
                if ($expectedType !== null) {
                    $isOK = false;
                    $elementType = isset($elementData['type']) ? $elementData['type'] : '';
                    if ($expectedType === 'structural' && array_search($elementType, $structuralElementsTypes) !== false) {
                        $isOK = true;
                    } elseif ($elementType === $expectedType) {
                        $isOK = true;
                    }
                    if (!$isOK) {
                        throw new \Exception('The element type (' . $elementType . ') does not match the expected one (' . $expectedType . ')!');
                    }
                }
                $result = $elementData;
                return -2; // stop
            }
        });
        return $result;
    }

    /**
     * 
     * @param array $containerData
     * @param array $newElementData
     * @param string $mode Available values: setIfExists, addLastIfNotFound
     * @return array
     */
    static function setContainerDataElement(array $containerData, array $newElementData, string $mode = 'setIfExists'): array
    {
        $targetElementID = $newElementData['id'];
        $hasChange = false;
        $containerData = self::walkContainerDataElements($containerData, function (array $elementData) use ($targetElementID, $newElementData, &$hasChange) {
            if ($elementData['id'] === $targetElementID) {
                $hasChange = true;
                return $newElementData;
            }
        });
        if (!$hasChange && $mode === 'addLastIfNotFound') {
            $containerData['elements'][] = $newElementData;
        }
        return $containerData;
    }

    /**
     * 
     * @param array $containerData
     * @param string $elementID
     * @return array
     */
    static private function removeContainerDataElement(array $containerData, string $elementID): array
    {
        return self::walkContainerDataElements($containerData, function (array $elementData) use ($elementID) {
            if ($elementData['id'] === $elementID) {
                return -1; // remove
            }
        });
    }

    /**
     * 
     * @param array $elementContainerData
     * @return array|null
     */
    static function getUpdatedStructuralElementData(array $elementContainerData): ?array
    {
        $result = [];
        $result['id'] = isset($elementContainerData['id']) ? $elementContainerData['id'] : '';

        if (isset($elementContainerData['type'])) {
            $result['type'] = $elementContainerData['type'];
        } else {
            $result['type'] = isset($elementContainerData['data'], $elementContainerData['data']['type']) ? $elementContainerData['data']['type'] : null;
        }

        if ($result['type'] === 'column') {
            $result['type'] === 'columns';
        }

        if ($result['type'] === null || array_search($result['type'], self::getStructuralElementsTypes()) === false) {
            return null;
        }

        $getAutoVerticalWidthState = function (string $autoVerticalWidth, $value) {
            $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;
            if ($autoVerticalWidthInPx !== null) {
                return [':size(maxWidth=' . $autoVerticalWidthInPx . ')', $value];
            }
            return null;
        };

        if ($result['type'] === 'columns') {
            if (isset($elementContainerData['elements'])) {
                $result['elements'] = $elementContainerData['elements'];
            } else {
                $result['elements'] = isset($elementContainerData['data'], $elementContainerData['data']['elements']) ? $elementContainerData['data']['elements'] : [];
            }
            if (isset($elementContainerData['style'])) {
                $result['style'] = $elementContainerData['style'];
            } else {
                $result['style'] = [];
                if (isset($elementContainerData['data'])) {
                    if (isset($elementContainerData['data']['mode'])) {
                        $columnsSizes = explode(':', $elementContainerData['data']['mode']);
                        $columnsCount = sizeof($columnsSizes);
                        $totalSize = array_sum($columnsSizes);
                        if ($totalSize === $columnsCount) {
                            $newValue = str_repeat(',', $columnsCount - 1);
                        } else {
                            $newValue = [];
                            for ($i = 0; $i < $columnsCount - 1; $i++) {
                                $newValue[$i] = floor(100 * $columnsSizes[$i] / $totalSize);
                            }
                            $newValue[$columnsCount - 1] = 100 - array_sum($newValue);
                            $newValue = implode('%,', $newValue) . '%';
                        }
                        $result['style']['widths'] = $newValue;
                    }
                    if (isset($elementContainerData['data']['responsive']) && (int) $elementContainerData['data']['responsive'] > 0) {
                        $result['style']['autoVerticalWidth'] = '500px';
                    } else {
                        $result['style']['autoVerticalWidth'] = 'none';
                    }
                } else {
                    $result['style']['layout'] = "{\"value\":{\"direction\":\"horizontal\",\"widths\":\";\"},\"states\":[[\":size(maxWidth=500)\",{\"direction\":\"vertical\"}]]}";
                }
            }
            if (!isset($result['style']['layout'])) {
                $layout = ['value' => [], 'states' => []];
                if (isset($result['style']['widths'])) {
                    $layout['value']['direction'] = 'horizontal';
                    $layout['value']['widths'] = str_replace(',', ';', $result['style']['widths']);
                    unset($result['style']['widths']);

                    if (isset($result['style']['elementsSpacing']) && strlen($result['style']['elementsSpacing']) > 0) {
                        $layout['value']['spacing'] = $result['style']['elementsSpacing'];
                        unset($result['style']['elementsSpacing']);
                    }

                    if (isset($result['style']['autoVerticalWidth']) && strlen($result['style']['autoVerticalWidth']) > 0) {
                        $state = $getAutoVerticalWidthState($result['style']['autoVerticalWidth'], ['direction' => 'vertical']);
                        if ($state !== null) {
                            $layout['states'][] = $state;
                        }
                        unset($result['style']['autoVerticalWidth']);
                    }
                }
                if (!empty($layout['value']) || !empty($layout['states'])) {
                    $result['style']['layout'] = InternalThemes::valueDetailsToString($layout);
                }
            }
        } elseif ($result['type'] === 'floatingBox') {
            if (isset($elementContainerData['elements'])) {
                $result['elements'] = $elementContainerData['elements'];
            } else {
                $result['elements'] = isset($elementContainerData['data'], $elementContainerData['data']['elements']) ? $elementContainerData['data']['elements'] : [];
            }

            if (isset($elementContainerData['style'])) {
                $result['style'] = $elementContainerData['style'];
            } else {
                $result['style'] = [];
                if (isset($elementContainerData['data'])) {
                    if (isset($elementContainerData['data']['position'])) {
                        $result['style']['position'] = $elementContainerData['data']['position'];
                    }
                    if (isset($elementContainerData['data']['width']) && strlen($elementContainerData['data']['width']) > 0 && $elementContainerData['data']['width'] !== 'auto') {
                        $result['style']['width'] = $elementContainerData['data']['width'];
                    }
                    if (isset($elementContainerData['data']['responsive']) && (int) $elementContainerData['data']['responsive'] > 0) {
                        $result['style']['autoVerticalWidth'] = '500px';
                    } else {
                        $result['style']['autoVerticalWidth'] = 'none';
                    }
                } else {
                    $result['style']['layout'] = "{\"value\":{\"position\":\"left\",\"width\":\"50%\"},\"states\":[[\":size(maxWidth=500)\",{\"position\":\"above\"}]]}";
                }
            }
            if (!isset($result['style']['layout'])) {
                $layout = ['value' => [], 'states' => []];
                if (isset($result['style']['position'])) {
                    $layout['value']['position'] = $result['style']['position'];
                    unset($result['style']['position']);

                    if (isset($result['style']['width'])) {
                        $layout['value']['width'] = $result['style']['width'];
                        unset($result['style']['width']);
                    } else {
                        $layout['value']['width'] = '50%';
                    }

                    if (isset($result['style']['elementsSpacing']) && strlen($result['style']['elementsSpacing']) > 0) {
                        $layout['value']['spacing'] = $result['style']['elementsSpacing'];
                        unset($result['style']['elementsSpacing']);
                    }

                    if (isset($result['style']['autoVerticalWidth']) && strlen($result['style']['autoVerticalWidth']) > 0) {
                        $state = $getAutoVerticalWidthState($result['style']['autoVerticalWidth'], ['position' => 'above']);
                        if ($state !== null) {
                            $layout['states'][] = $state;
                        }
                        unset($result['style']['autoVerticalWidth']);
                    }
                }
                if (!empty($layout['value']) || !empty($layout['states'])) {
                    $result['style']['layout'] = InternalThemes::valueDetailsToString($layout);
                }
            }
        } elseif ($result['type'] === 'flexibleBox') {
            if (isset($elementContainerData['elements'])) {
                $result['elements'] = $elementContainerData['elements'];
            } else {
                $result['elements'] = isset($elementContainerData['data'], $elementContainerData['data']['elements']) ? $elementContainerData['data']['elements'] : [];
            }

            if (isset($elementContainerData['style'])) {
                $result['style'] = $elementContainerData['style'];
            }

            if (isset($result['style']) && is_array($result['style'])) {
                if (!isset($result['style']['layout'])) {
                    $layout = ['value' => [], 'states' => []];
                    if (isset($result['style']['direction'])) {
                        $direction = 'vertical';
                        if ($result['style']['direction'] === 'row') { // old values: row, column
                            $direction = 'horizontal';
                        }
                        $layout['value']['direction'] = $direction;
                        unset($result['style']['direction']);

                        if (isset($result['style']['rowAlignment']) && strlen($result['style']['rowAlignment']) > 0) { // left, center, right
                            $layout['value']['alignment'] = $result['style']['rowAlignment'];
                            if ($layout['value']['alignment'] === 'left') {
                                $layout['value']['alignment'] = 'start';
                            } elseif ($layout['value']['alignment'] === 'right') {
                                $layout['value']['alignment'] = 'end';
                            }
                            unset($result['style']['rowAlignment']);
                        }

                        if (isset($result['style']['elementsSpacing']) && strlen($result['style']['elementsSpacing']) > 0) {
                            $layout['value']['elementsSpacing'] = $result['style']['elementsSpacing'];
                            unset($result['style']['elementsSpacing']);
                        }

                        if (isset($result['style']['autoVerticalWidth']) && strlen($result['style']['autoVerticalWidth']) > 0) {
                            $state = $getAutoVerticalWidthState($result['style']['autoVerticalWidth'], ['direction' => 'vertical']);
                            if ($state !== null) {
                                $layout['states'][] = $state;
                            }
                            unset($result['style']['autoVerticalWidth']);
                        }
                    }
                    if (!empty($layout['value']) || !empty($layout['states'])) {
                        $result['style']['layout'] = InternalThemes::valueDetailsToString($layout);
                    }
                }
                if (isset($result['style']['layout'])) {
                    $layout = InternalThemes::getValueDetails($result['style']['layout']);
                    $updateDirection = function (array $value): array {
                        if (isset($value['direction'])) {
                            if ($value['direction'] === 'verticalReverse') {
                                $value['direction'] = 'vertical-reverse';
                            } elseif ($value['direction'] === 'horizontalReverse') {
                                $value['direction'] = 'horizontal-reverse';
                            }
                        }
                        return $value;
                    };
                    $updateElementsSpacing = function (array $value): array {
                        if (isset($value['elementsSpacing'])) {
                            $value['spacing'] = $value['elementsSpacing'];
                            unset($value['elementsSpacing']);
                        }
                        return $value;
                    };
                    if (is_array($layout['value'])) {
                        $layout['value'] = $updateDirection($layout['value']);
                        $layout['value'] = $updateElementsSpacing($layout['value']);
                    }
                    foreach ($layout['states'] as $i => $stateData) {
                        if (is_array($stateData[1])) {
                            $stateData[1] = $updateDirection($stateData[1]);
                            $stateData[1] = $updateElementsSpacing($stateData[1]);
                            $layout['states'][$i][1] = $stateData[1];
                        }
                    }
                    $result['style']['layout'] = InternalThemes::valueDetailsToString($layout);
                }
            }
        } elseif ($result['type'] === 'slider') {
            $result['elements'] = isset($elementContainerData['elements']) ? $elementContainerData['elements'] : [];
            if (isset($elementContainerData['style'])) {
                $result['style'] = $elementContainerData['style'];
            }
        }
        if (isset($result['style']) && empty($result['style'])) {
            unset($result['style']);
        }
        if (isset($elementContainerData['data'])) { // added for the flexibleBox url option
            $result['data'] = $elementContainerData['data'];
        }
        if (isset($elementContainerData['styleID'])) {
            $result['styleID'] = $elementContainerData['styleID'];
        }
        if (isset($elementContainerData['tags'])) {
            $result['tags'] = $elementContainerData['tags'];
        }
        if (isset($result['data']) && empty($result['data'])) {
            unset($result['data']);
        }
        return $result;
    }

    /**
     * 
     * @param string $suffix
     * @param mixed|null $context If provided it will be used to create a persistent ID, but it will generate error if the element exists
     * @param bool $checkIfExists
     * @return string
     */
    static function generateElementID(string $suffix = '', $context = null, bool $checkIfExists = true): string
    {
        $generateID = function (string $data) use ($suffix) {
            return base_convert(md5($data), 16, 36) . $suffix;
        };
        if ($context !== null) {
            $id = $generateID(serialize($context));
            if ($checkIfExists && InternalDataElements::getElementRawData($id) !== null) {
                throw new \Exception('The element ID generated exists (' . $id . ')');
            }
            return $id;
        }
        for ($i = 0; $i < 100; $i++) {
            $id = $generateID(uniqid('', true));
            if ($checkIfExists && InternalDataElements::getElementRawData($id) !== null) {
                continue;
            }
            return $id;
        }
        throw new \Exception('Too much retries!');
    }

    /**
     * 
     * @param string $elementID
     * @param string $sourceContainerID
     * @param string $targetContainerID
     * @param array $target Available values: ['beforeElement', 'id'], ['afterElement', 'id'], ['insideContainer'], ['insideColumn', 'id', 'index'], ['insideFloatingBox', 'id', 'inside|outside'], ['insideFlexibleBox', 'id'], ['insideSlider', 'id']
     * @return void
     */
    static function moveElement(string $elementID, string $sourceContainerID, string $targetContainerID, array $target): void
    {
        $isSameTarget = $sourceContainerID === $targetContainerID;
        $sourceContainerData = $sourceContainerID !== null ? InternalDataElements::getContainer($sourceContainerID) : null;
        if ($sourceContainerData === null) {
            throw new \Exception('Cannot find source container (' . $sourceContainerID . ')!');
        }
        if (!$isSameTarget) {
            $targetContainerData = $targetContainerID !== null ? InternalDataElements::getContainer($targetContainerID, true) : null;
            if ($targetContainerData === null) {
                throw new \Exception('Cannot find source container (' . $targetContainerID . ')!');
            }
        }

        if ($isSameTarget) {
            $newSourceContainerData = self::moveContainerDataElement($sourceContainerData, $elementID, $target);
            self::setLastChangeTime($newSourceContainerData);
            InternalDataElements::setContainer($sourceContainerID, $newSourceContainerData);
            InternalDataElements::dispatchContainerChangeEvent($sourceContainerID);
        } else {
            $elementData = self::getContainerDataElement($sourceContainerData, $elementID);
            if ($elementData === null) {
                throw new \Exception('Source element (' . $elementID . ') not found!');
            }
            $newSourceContainerData = self::removeContainerDataElement($sourceContainerData, $elementID);
            $newTargetContainerData = $targetContainerData;
            $newTargetContainerData['elements'][] = $elementData;
            $newTargetContainerData = self::moveContainerDataElement($newTargetContainerData, $elementID, $target);
            self::setLastChangeTime($newSourceContainerData);
            self::setLastChangeTime($newTargetContainerData);
            InternalDataElements::setContainer($sourceContainerID, $newSourceContainerData);
            InternalDataElements::setContainer($targetContainerID, $newTargetContainerData);
            InternalDataElements::dispatchContainerChangeEvent($sourceContainerID);
            InternalDataElements::dispatchContainerChangeEvent($targetContainerID);
        }
    }

    /**
     * 
     * @param array $containerData
     * @param string $elementID
     * @param array $target Available values: ['beforeElement', 'id'], ['afterElement', 'id'], ['insideContainer'], ['insideColumn', 'id', 'index'], ['insideFloatingBox', 'id', 'inside|outside'], ['insideFlexibleBox', 'id'], ['insideSlider', 'id']
     * @throws \Exception
     * @return array
     */
    static function moveContainerDataElement(array $containerData, string $elementID, array $target): array
    {
        if (!isset($target[0])) {
            throw new \Exception('Target type (index 0) not found!');
        }
        $targetType = $target[0];
        $targetID = null;
        $targetArg = null;
        if (array_search($targetType, ['beforeElement', 'afterElement', 'insideColumn', 'insideFloatingBox', 'insideFlexibleBox', 'insideSlider']) !== false) {
            if (!isset($target[1])) {
                throw new \Exception('Target id (index 1) not found!');
            }
            $targetID = $target[1];
            if ($targetType === 'insideColumn') {
                $targetArg = isset($target[2]) ? (int)$target[2] : 0;
            }
            if ($targetType === 'insideFloatingBox') {
                $targetArg = isset($target[2]) && $target[2] === 'inside' ? 'inside' : 'outside';
            }
            if ($targetType === 'insideSlider') {
                $targetArg = isset($target[2]) ? (int)$target[2] : 0;
            }
        } elseif (array_search($targetType, ['insideContainer']) !== false) {
            // ok
        } else {
            throw new \Exception('Invalid target type (' . $targetType . ')!');
        }
        $elementData = self::getContainerDataElement($containerData, $elementID);
        if ($elementData === null) {
            throw new \Exception('Element not found (' . $elementID . ')!');
        }
        $containerData = self::removeContainerDataElement($containerData, $elementID); // must be before target ID check to handle the case when targetID is as the elementID
        if ($targetID !== null) {
            $targetElementData = self::getContainerDataElement($containerData, $targetID);
            if ($targetElementData === null) {
                throw new \Exception('Target element not found (' . $targetID . ')!');
            }
        }
        $added = false; // Just in case check
        if ($targetType === 'insideContainer') {
            $containerData['elements'][] = $elementData;
            $added = true;
        } elseif ($targetType === 'beforeElement' || $targetType === 'afterElement') {
            $containerData = self::walkContainerDataElements($containerData, function (array $elemList) use ($targetID, $targetType, $elementData, &$added) {
                $result = [];
                foreach ($elemList as $elemData) {
                    $isTargetItem = $elemData['id'] === $targetID;
                    if ($isTargetItem && $targetType === 'beforeElement') {
                        $result[] = $elementData;
                        $added = true;
                    }
                    $result[] = $elemData;
                    if ($isTargetItem && $targetType === 'afterElement') {
                        $result[] = $elementData;
                        $added = true;
                    }
                }
                return $result;
            }, 'elementsList');
        } elseif ($targetType === 'insideColumn' || $targetType === 'insideFloatingBox' || $targetType === 'insideFlexibleBox' || $targetType === 'insideSlider') {
            $containerData = self::walkContainerDataElements($containerData, function (array $elemData) use ($targetID, $targetArg, $elementData, &$added) {
                if ($elemData['id'] === $targetID) {
                    if (self::isColumnsElementContainerData($elemData)) {
                        if (!isset($elemData['elements'][$targetArg])) {
                            $elemData['elements'][$targetArg] = [];
                        }
                        $elemData['elements'][$targetArg][] = $elementData;
                        $added = true;
                    } elseif (self::isFloatingBoxElementContainerData($elemData)) {
                        if (!isset($elemData['elements'][$targetArg])) {
                            $elemData['elements'][$targetArg] = [];
                        }
                        $elemData['elements'][$targetArg][] = $elementData;
                        $added = true;
                    } elseif (self::isFlexibleBoxElementContainerData($elemData)) {
                        $elemData['elements'][] = $elementData;
                        $added = true;
                    } elseif (self::isSliderElementContainerData($elemData)) {
                        if (!isset($elemData['elements'][$targetArg])) {
                            $elemData['elements'][$targetArg] = [];
                        }
                        $elemData['elements'][$targetArg][] = $elementData;
                        $added = true;
                    } else {
                        throw new \Exception('Invalid target structural item!');
                    }
                    return $elemData;
                }
            });
        }
        if (!$added) {
            throw new \Exception('Removed element not added!');
        }
        return $containerData;
    }

    /**
     * Duplicates data and styles only (no id or lastChangeTime update)
     * 
     * @param array $elementData
     * @return array
     */
    static private function duplicateElementData(array $elementData): array
    {
        if (isset($elementData['type'])) {
            $elementTypeDefinition = ElementsHelper::getElementTypeDefinition($elementData['type']);
            if ($elementTypeDefinition !== null && is_callable($elementTypeDefinition->onDuplicate)) {
                $elementData['data'] = call_user_func($elementTypeDefinition->onDuplicate, isset($elementData['data']) ? $elementData['data'] : []);
            }
        }
        if (isset($elementData['style'])) {
            $elementData['style'] = ElementStylesHelper::duplicateStyleValues($elementData['style']);
        }
        return $elementData;
    }

    /**
     * 
     * @param string $elementID
     * @param string $containerID
     * @return string
     */
    static function duplicateElement(string $elementID, string $containerID): string
    {
        $elementData = InternalDataElements::getElement($elementID);
        $containerData = $containerID !== null ? (isset($options['containerData']) ? $options['containerData'] : InternalDataElements::getContainer($containerID)) : null;
        $elementDataInContainer = null;
        if ($containerData !== null) {
            $elementDataInContainer = self::getContainerDataElement($containerData, $elementID);
        }
        $isStructuralElement = false;
        if ($elementData !== null) {
            $newElementData = $elementData;
        } elseif ($elementDataInContainer !== null && self::isStructuralElementData($elementDataInContainer)) {
            $newElementData = $elementDataInContainer;
            $isStructuralElement = true;
        } else {
            throw new \Exception('Source element (' . $elementID . ') not found!');
        }

        $addToContainer = function (array $elementContainerData) use ($containerData, $elementID, $containerID) {
            $containerData['elements'][] = $elementContainerData;
            $containerData = self::moveContainerDataElement($containerData, $elementContainerData['id'], ['afterElement', $elementID]);
            self::setLastChangeTime($containerData);
            InternalDataElements::setContainer($containerID, $containerData);
        };

        $newElementID = self::generateElementID('ed');

        $elementChangeEventsToDispatch = [];
        $newElementData['id'] = $newElementID;
        $newElementData = self::duplicateElementData($newElementData);
        if ($isStructuralElement) {
            $updatedIDs = [];
            $newElementData = self::walkStructuralElementChildren($newElementData, function (array $elementData) use (&$updatedIDs) {
                $oldElementID = $elementData['id'];
                $newElementID = self::generateElementID('ed');
                $elementData['id'] = $newElementID;
                $updatedIDs[$newElementID] = $oldElementID;
                if (self::isStructuralElementData($elementData)) {
                    $elementData = self::duplicateElementData($elementData);
                }
                return $elementData;
            });
            $childrenElements = self::getStructuralElementDataChildrenData($newElementData, 'nonStructural');
            foreach ($childrenElements as $childElementContainerData) {
                $newChildElementID = $childElementContainerData['id'];
                $oldChildElementID = $updatedIDs[$newChildElementID];
                $oldChildElementData = InternalDataElements::getElement($oldChildElementID);
                if ($oldChildElementData !== null) {
                    $newChildElementData = $oldChildElementData;
                    $newChildElementData['id'] = $newChildElementID;
                    $newChildElementData = self::duplicateElementData($newChildElementData);
                    self::setLastChangeTime($newChildElementData);
                    InternalDataElements::setElement($newChildElementID, $newChildElementData);
                    $elementChangeEventsToDispatch[] = $newChildElementID;
                }
            }
            $addToContainer($newElementData);
        } else {
            self::setLastChangeTime($newElementData);
            InternalDataElements::setElement($newElementID, $newElementData);
            $elementChangeEventsToDispatch[] = $newElementID;
            $addToContainer(['id' => $newElementID]);
        }
        foreach ($elementChangeEventsToDispatch as $eventElementID) {
            InternalDataElements::dispatchElementChangeEvent($eventElementID, $containerID);
        }
        InternalDataElements::dispatchContainerChangeEvent($containerID);
        return $newElementID;
    }

    /**
     * 
     * @param string $sourceContainerID
     * @param string $targetContainerID
     * @return void
     */
    static function duplicateContainer(string $sourceContainerID, string $targetContainerID): void
    {
        if ($sourceContainerID === $targetContainerID) {
            throw new \Exception('The targetContainerID must be different from sourceContainerID!');
        }
        $sourceContainerData = InternalDataElements::getContainer($sourceContainerID, true);
        $targetContainerData = $sourceContainerData;
        $targetContainerData['id'] = $targetContainerID;

        $elementChangeEventsToDispatch = [];
        $targetContainerData = self::walkContainerDataElements($targetContainerData, function (array $elementData) use (&$elementChangeEventsToDispatch) {
            $originalID = $elementData['id'];
            $newID = self::generateElementID('cc');
            $elementData['id'] = $newID;
            if (self::isStructuralElementData($elementData)) {
                $elementData = self::duplicateElementData($elementData);
            } else {
                $originalElementData = InternalDataElements::getElement($originalID);
                if ($originalElementData !== null) {
                    $newElementData = $originalElementData;
                    $newElementData['id'] = $newID;
                    $newElementData = self::duplicateElementData($newElementData);
                    self::setLastChangeTime($newElementData);
                    InternalDataElements::setElement($newID, $newElementData);
                    $elementChangeEventsToDispatch[] = $newID;
                }
            }
            return $elementData;
        });
        self::setLastChangeTime($targetContainerData);
        self::deleteContainer($targetContainerID);
        InternalDataElements::setContainer($targetContainerID, $targetContainerData);
        foreach ($elementChangeEventsToDispatch as $eventElementID) {
            InternalDataElements::dispatchElementChangeEvent($eventElementID, $targetContainerID);
        }
        InternalDataElements::dispatchContainerChangeEvent($targetContainerID);
    }

    /**
     * Walks the structural item children
     * 
     * @param array $elementData
     * @param callable $callback Return the new value, -1 to delete, -2 to stop or nothing to leave unchanged
     * @return array
     */
    static private function walkStructuralElementChildren(array $elementData, callable $callback): array
    {
        $temp = ['elements' => [$elementData]];
        $elementID = $elementData['id'];
        $temp = self::walkContainerDataElements($temp, function (array $childElementData) use ($elementID, $callback) {
            if ($childElementData['id'] !== $elementID) {
                return $callback($childElementData);
            }
        });
        return $temp['elements'][0];
    }

    /**
     * Walks the container elements
     * 
     * @param array $containerData
     * @param callable $callback Return the new value, -1 to delete (in elementData mode), -2 to stop (in elementData mode) or nothing to leave unchanged
     * @param string $mode Type of values that will be pass to the callback. Available values: elementData, elementsList
     * @return array
     */
    static private function walkContainerDataElements(array $containerData, callable $callback, string $mode = 'elementData'): array
    {
        if ($mode === 'elementData') {
            $walkElements = function (array $elements) use (&$walkElements, $callback) {
                $result = [];
                foreach ($elements as $elementData) {
                    $deleteElement = false;
                    $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                    $isStructuralElement = $structuralElementData !== null;
                    $currentElementData = $isStructuralElement ? $structuralElementData : $elementData;
                    $newElementData = $callback($currentElementData);
                    if (is_array($newElementData)) {
                        // updated
                    } elseif ($newElementData === -1) { // delete
                        $deleteElement = true;
                    } elseif ($newElementData === -2) { // stop
                        return -2;
                    } else {
                        $newElementData = $currentElementData;
                    }
                    if (!$deleteElement) {
                        if ($isStructuralElement) {
                            $elementType = $newElementData['type'];
                            if ($elementType === 'columns') {
                                if (isset($newElementData['elements'])) {
                                    foreach ($newElementData['elements'] as $columnIndex => $columnElements) {
                                        $walkElementsResult = $walkElements($columnElements);
                                        if ($walkElementsResult === -2) {
                                            return -2;
                                        }
                                        $newElementData['elements'][$columnIndex] = $walkElementsResult;
                                    }
                                }
                            } elseif ($elementType === 'floatingBox') {
                                if (isset($newElementData['elements'])) {
                                    foreach (['inside', 'outside'] as $location) {
                                        if (isset($newElementData['elements'][$location])) {
                                            $walkElementsResult = $walkElements($newElementData['elements'][$location]);
                                            if ($walkElementsResult === -2) {
                                                return -2;
                                            }
                                            $newElementData['elements'][$location] = $walkElementsResult;
                                        }
                                    }
                                }
                            } elseif ($elementType === 'flexibleBox') {
                                if (isset($newElementData['elements'])) {
                                    $walkElementsResult = $walkElements($newElementData['elements']);
                                    if ($walkElementsResult === -2) {
                                        return -2;
                                    }
                                    $newElementData['elements'] = $walkElementsResult;
                                }
                            } elseif ($elementType === 'slider') {
                                if (isset($newElementData['elements'])) {
                                    foreach ($newElementData['elements'] as $slideIndex => $slideElements) {
                                        $walkElementsResult = $walkElements($slideElements);
                                        if ($walkElementsResult === -2) {
                                            return -2;
                                        }
                                        $newElementData['elements'][$slideIndex] = $walkElementsResult;
                                    }
                                }
                            }
                        }
                        $result[] = $newElementData;
                    }
                }
                return $result;
            };
        } else { // elementsList mode
            $walkElements = function (array $elements) use (&$walkElements, $callback) {
                foreach ($elements as $i => $elementData) {
                    $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                    if ($structuralElementData !== null) {
                        $elements[$i] = $structuralElementData;
                    }
                }
                $newElementsList = $callback($elements);
                if (is_array($newElementsList)) {
                    // updated
                } else {
                    $newElementsList = $elements;
                }
                foreach ($newElementsList as $i => $newElementData) {
                    if (self::isStructuralElementData($newElementData)) {
                        $elementType = $newElementData['type'];
                        if ($elementType === 'columns') {
                            if (isset($newElementData['elements'])) {
                                foreach ($newElementData['elements'] as $columnIndex => $columnElements) {
                                    $newElementData['elements'][$columnIndex] = $walkElements($columnElements);
                                }
                            }
                        } elseif ($elementType === 'floatingBox') {
                            if (isset($newElementData['elements'])) {
                                foreach (['inside', 'outside'] as $location) {
                                    if (isset($newElementData['elements'][$location]))
                                        $newElementData['elements'][$location] = $walkElements($newElementData['elements'][$location]);
                                }
                            }
                        } elseif ($elementType === 'flexibleBox') {
                            if (isset($newElementData['elements'])) {
                                $newElementData['elements'] = $walkElements($newElementData['elements']);
                            }
                        } elseif ($elementType === 'slider') {
                            if (isset($newElementData['elements'])) {
                                foreach ($newElementData['elements'] as $slideIndex => $slideElements) {
                                    $newElementData['elements'][$slideIndex] = $walkElements($slideElements);
                                }
                            }
                        }
                        $newElementsList[$i] = $newElementData;
                    }
                }
                return $newElementsList;
            };
        }
        $walkElementsResult = $walkElements($containerData['elements']);
        if ($walkElementsResult !== -2) {
            $containerData['elements'] = $walkElementsResult;
        }
        return $containerData;
    }

    /**
     * Replaces the elements IDs with new ones
     *
     * @param array $containerData
     * @param callable $newElementIDCallback
     * @return array Returns the new container data and a list of replaced IDs. Format [containerData, [oldID1=>newID1, oldID2=>newID2, ...]]
     */
    static private function updateContainerElementsIDs(array $containerData, callable $newElementIDCallback): array
    {
        $updatedIDs = [];
        $newContainerData = self::walkContainerDataElements($containerData, function (array $elementData) use (&$updatedIDs, $newElementIDCallback) {
            $oldElementID = $elementData['id'];
            $newElementID = call_user_func($newElementIDCallback, $oldElementID);
            $elementData['id'] = $newElementID;
            $updatedIDs[$oldElementID] = $newElementID;
            return $elementData;
        });
        return [$newContainerData, $updatedIDs];
    }

    /**
     * 
     * @param array $elementData
     * @return array
     */
    static function getElementDataUploadsSizeItems(array $elementData): array
    {
        $result = [];
        if (isset($elementData['type'])) {
            $elementTypeDefinition = ElementsHelper::getElementTypeDefinition($elementData['type']);
            if ($elementTypeDefinition !== null && is_callable($elementTypeDefinition->getUploadsSizeItems)) {
                $result = array_merge($result, call_user_func($elementTypeDefinition->getUploadsSizeItems, isset($elementData['data']) ? $elementData['data'] : []));
            }
        }
        $result = array_merge($result, self::getElementDataStyleUploadsSizeItems($elementData));
        return $result;
    }

    /**
     * 
     * @param array $elementData
     * @return array
     */
    static function getElementDataStyleUploadsSizeItems(array $elementData): array
    {
        $result = [];
        if (isset($elementData['style'])) {
            $filenames = InternalThemes::getFilesInValues($elementData['style']);
            foreach ($filenames as $filename) {
                $dataKey = InternalData::getFilenameDataKey($filename);
                if ($dataKey !== null) {
                    $result[] = $dataKey;
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $containerID
     * @return integer
     */
    static function getContainerUploadsSize(string $containerID): int
    {
        return UploadsSize::getItemsSize(self::getContainerUploadsSizeItems($containerID));
    }

    /**
     * 
     * @param string $containerID
     * @param array $options
     * @return array
     */
    static function getContainerUploadsSizeItems(string $containerID, array $options = []): array
    {
        $elementsIDs = isset($options['elementsIDs']) ? $options['elementsIDs'] : null;
        $containerData = $containerID !== null ? InternalDataElements::getContainer($containerID) : null;
        if ($containerData === null) {
            return [];
        }

        $result = [];

        $addedElements = [];
        $addElementUploadsSizeItems = function (array $elementData) use (&$addElementUploadsSizeItems, &$addedElements, &$result) {
            $elementID = $elementData['id'];
            if (isset($addedElements[$elementID])) {
                return;
            }
            $addedElements[$elementID] = true;
            if (self::isStructuralElementData($elementData)) {
                $result = array_merge($result, self::getElementDataUploadsSizeItems($elementData));
                self::walkStructuralElementChildren($elementData, function (array $childElementData) use (&$addElementUploadsSizeItems) {
                    $addElementUploadsSizeItems($childElementData);
                });
            } else {
                $elementData = InternalDataElements::getElement($elementID);
                if ($elementData !== null) {
                    $result = array_merge($result, self::getElementDataUploadsSizeItems($elementData));
                }
            }
        };

        self::walkContainerDataElements($containerData, function (array $elementData) use ($elementsIDs, $addElementUploadsSizeItems) {
            if ($elementsIDs !== null && array_search($elementData['id'], $elementsIDs) === false) {
                return;
            }
            $addElementUploadsSizeItems($elementData);
        });

        return array_values(array_unique($result));
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return integer
     */
    static function getElementUploadsSize(string $elementID, string $containerID = null): int
    {
        return UploadsSize::getItemsSize(self::getElementUploadsSizeItems($elementID, $containerID));
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return array
     */
    static function getElementUploadsSizeItems(string $elementID, string $containerID = null): array
    {
        $elementData = InternalDataElements::getElement($elementID);
        if ($elementData !== null) {
            return self::getElementDataUploadsSizeItems($elementData);
        }
        if ($containerID !== null) {
            return self::getContainerUploadsSizeItems($containerID, ['elementsIDs' => [$elementID]]);
        }
        return [];
    }

    /**
     * 
     * @param string $containerID
     * @param callable $add Function to add an item to the exported file
     * @return void
     */
    static function exportContainer(string $containerID, callable $add): void
    {
        $containerData = InternalDataElements::getContainer($containerID);
        if ($containerData === null) {
            return;
        }
        foreach ($containerData['elements'] as $elementData) {
            self::exportElement($elementData['id'], $containerID, $add, ['containerData' => $containerData]);
        }
        $add('bearcms/elements/container/' . md5($containerID) . '/value.json', json_encode($containerData, JSON_THROW_ON_ERROR));
    }

    /**
     * 
     * @param string $containerID
     * @param ImportContext $context
     * @param array $options
     * @return string|null
     */
    static function importContainer(string $containerID, ImportContext $context, array $options = []): ?string
    {
        $getContainerData = function (string $containerID) use ($context): ?array {
            $containerData = $context->getValue('bearcms/elements/container/' . md5($containerID) . '/value.json');
            if ($containerData !== null) {
                $containerData = json_decode($containerData, true);
                if (is_array($containerData) && isset($containerData['elements'])) {
                    return $containerData;
                }
            }
            return null;
        };
        $containerData = $getContainerData($containerID);
        if ($containerData === null) {
            return null; // may not be found when exporting
        }

        self::setLastChangeTime($containerData);
        foreach ($containerData['elements'] as $elementData) {
            self::importElement($elementData['id'], $containerID, $context, [
                'generateNewElementID' => true,
                'targetContainerData' => &$containerData,
                'insertTarget' => '-internal-replace'
            ]);
        }
        $targetContainerID = isset($options['targetContainerID']) ? $options['targetContainerID'] : $containerID;
        if ($context->isExecuteMode()) {
            self::deleteContainer($targetContainerID);
            InternalDataElements::setContainer($targetContainerID, $containerData);
            InternalDataElements::dispatchContainerChangeEvent($targetContainerID);
        }
        return $targetContainerID;
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @param callable $add Function to add an item to the exported file
     * @return void
     */
    static function exportElement(string $elementID, string $containerID = null, callable $add, array $options = []): void
    {
        $app = App::get();

        $export = function (array $elementData, bool $addElementData) use ($app, $add): array {
            $elementID = $elementData['id'];
            $elementTypeDefinition = ElementsHelper::getElementTypeDefinition($elementData['type']);
            if ($elementTypeDefinition !== null) {
                if (!$elementTypeDefinition->canImportExport) {
                    throw new \Exception('Cannot export element of type ' . $elementData['type'] . '! Trying to export ' . $elementID . '.');
                }
                if (is_callable($elementTypeDefinition->onExport)) {
                    $elementData['data'] = call_user_func($elementTypeDefinition->onExport, isset($elementData['data']) ? $elementData['data'] : [], function (string $key, string $content) use ($elementID, $add) {
                        $add('bearcms/elements/element/' . md5($elementID) . '/data/' . $key, $content);
                    });
                }
            }
            $updateStyleValues = function (array $styleValues, string $filenamePrefix) use ($app, $add) {
                $addedDataKeys = [];
                $filenames = InternalThemes::getFilesInValues($styleValues, true);
                if (!empty($filenames)) {
                    $filesToUpdate = [];
                    foreach ($filenames as $filename) {
                        if (strpos($filename, 'addon:') === 0) {
                            // leave as is
                        } else {
                            $filenameOptions = InternalData::getFilenameOptions($filename);
                            $dataKey = InternalData::getFilenameDataKey($filename);
                            if ($dataKey !== null && $app->data->exists($dataKey)) {
                                if (isset($addedDataKeys[$dataKey])) {
                                    $newFilename = $addedDataKeys[$dataKey];
                                } else {
                                    $newFilename = 'file' . (sizeof($addedDataKeys) + 1) . '.' . InternalData::getFilenameExtension($filename);
                                    $add($filenamePrefix . $newFilename, file_get_contents($app->data->getFilename($dataKey)));
                                    $addedDataKeys[$dataKey] = $newFilename;
                                }
                                $newFilenameWithOptions = InternalData::setFilenameOptions($newFilename, $filenameOptions);
                                $filesToUpdate[$filename] = $newFilenameWithOptions;
                            }
                        }
                    }
                    $styleValues = InternalThemes::updateFilesInValues($styleValues, $filesToUpdate);
                }
                return $styleValues;
            };
            if (isset($elementData['style'])) {
                $elementData['style'] = $updateStyleValues($elementData['style'], 'bearcms/elements/element/' . md5($elementID) . '/style/');
            }
            if (isset($elementData['styleID'])) {
                if (array_search($elementData['styleID'], ['default', 'custom']) === false) {
                    $sharedStyleData = ElementsSharedStyles::get($elementData['styleID']);
                    if ($sharedStyleData !== null) {
                        if (isset($sharedStyleData['style'])) {
                            $sharedStyleData['id'] = 's' . md5(serialize($sharedStyleData['style'])); // serial old value because the filenames are more unique
                            $sharedStyleData['style'] = $updateStyleValues($sharedStyleData['style'], 'bearcms/elements/style/' . md5($sharedStyleData['id']) . '/style/');
                        }
                        $elementData['styleID'] = $sharedStyleData['id'];
                        $add('bearcms/elements/style/' . md5($sharedStyleData['id']) . '/value.json', json_encode($sharedStyleData, JSON_THROW_ON_ERROR));
                    }
                }
            }
            if ($addElementData) {
                $add('bearcms/elements/element/' . md5($elementID) . '/value.json', json_encode($elementData, JSON_THROW_ON_ERROR));
            }
            return $elementData;
        };

        $elementData = InternalDataElements::getElement($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $export($elementData, true);
        } else {
            $containerData = isset($options['containerData']) ? $options['containerData'] : InternalDataElements::getContainer($containerID);
            if (is_array($containerData)) {
                $elementData = self::getContainerDataElement($containerData, $elementID, 'structural');
                if (is_array($elementData) && isset($elementData['type'])) {
                    $newElementData = self::walkStructuralElementChildren($elementData, function (array $elemData) use ($export) {
                        if (self::isStructuralElementData($elemData)) {
                            return $export($elemData, false);
                        } else {
                            $childElementData = InternalDataElements::getElement($elemData['id']);
                            if (is_array($childElementData) && isset($childElementData['type'])) {
                                $export($childElementData, true);
                            }
                        }
                    });
                    $export($newElementData, true);
                }
            }
        }
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @param ImportContext $changes
     * @param array $options
     * @return string|null Returns the imported element ID
     */
    static function importElement(string $elementID, string $containerID = null, ImportContext $context, array $options = []): ?string
    {
        $app = App::get();

        $isExecuteMode = $context->isExecuteMode();

        $import = function (array $elementData, string $oldElementID) use ($app, $context, $isExecuteMode) {
            $elementTypeDefinition = ElementsHelper::getElementTypeDefinition($elementData['type']);
            if ($elementTypeDefinition !== null && is_callable($elementTypeDefinition->onImport)) {
                $elementContext = $context->makeGetValueContext(function (string $key) use ($oldElementID, $context) {
                    return $context->getValue('bearcms/elements/element/' . md5($oldElementID) . '/data/' . $key);
                });
                $elementData['data'] = call_user_func($elementTypeDefinition->onImport, isset($elementData['data']) ? $elementData['data'] : [], $elementContext);
            }
            $updateStyleValues = function (array $styleValues, string $filenamePrefix, string $dataKeyPrefix, string $logType, $errorContextData) use ($app, $context, $isExecuteMode) {
                $filenames = InternalThemes::getFilesInValues($styleValues, true);
                if (!empty($filenames)) {
                    $addedFiles = [];
                    $filesToUpdate = [];
                    foreach ($filenames as $filename) {
                        if (strpos($filename, 'addon:') === 0) {
                            // leave as is
                        } else {
                            $filenameOptions = InternalData::getFilenameOptions($filename);
                            $filenameWithoutOptions = InternalData::removeFilenameOptions($filename);
                            $filenameInArchive = $filenamePrefix . $filenameWithoutOptions;
                            $content = $context->getValue($filenameInArchive);
                            if ($content !== null) {
                                if (isset($addedFiles[$filenameWithoutOptions])) {
                                    $newFilename = $addedFiles[$filenameWithoutOptions];
                                } else {
                                    $newFilename = InternalData::generateNewFilename($app->data->getFilename($dataKeyPrefix . $filenameWithoutOptions)); // , $context->id !== null ? [$containerID, $newElementID, $oldElementID, $filename, $context->id] : null
                                    $newFilenameDataKey = InternalData::getFilenameDataKey($newFilename);
                                    $newFilenameFileSize = strlen($content);
                                    if ($isExecuteMode) {
                                        file_put_contents($newFilename, $content);
                                        UploadsSize::add($newFilenameDataKey, $newFilenameFileSize);
                                    }
                                    $addedFiles[$filenameWithoutOptions] = $newFilename;
                                    $context->logChange($logType, ['dataKey' => $newFilenameDataKey]);
                                    $context->logChange('uploadsSizeAdd', ['key' => $newFilenameDataKey, 'size' => $newFilenameFileSize]);
                                }
                                $newFilenameWithOptions = InternalData::setFilenameOptions($newFilename, $filenameOptions);
                                $filesToUpdate[$filename] = InternalData::getShortFilename($newFilenameWithOptions);
                            } else {
                                $context->logWarning('Style file not found in archive (' . $filenameInArchive . ')', $errorContextData);
                                $filesToUpdate[$filename] = '';
                            }
                        }
                    }
                    $styleValues = InternalThemes::updateFilesInValues($styleValues, $filesToUpdate);
                }
                return $styleValues;
            };
            if (isset($elementData['style'])) {
                $elementData['style'] = $updateStyleValues($elementData['style'], 'bearcms/elements/element/' . md5($oldElementID) . '/style/', 'bearcms/files/elementstyleimage/', 'elementStyleFilesAdd', $elementData);
            }
            if (isset($elementData['styleID'])) {
                $sharedStyleData = $context->getValue('bearcms/elements/style/' . md5($elementData['styleID']) . '/value.json');
                if ($sharedStyleData !== null) {
                    $sharedStyleData = json_decode($sharedStyleData, true);
                    if (is_array($sharedStyleData)) {
                        if (!ElementsSharedStyles::exists($sharedStyleData['id'])) {
                            $sharedStyleData['style'] = $updateStyleValues($sharedStyleData['style'], 'bearcms/elements/style/' . md5($sharedStyleData['id']) . '/style/', 'bearcms/files/elementstyleimage/', 'sharedStyleFilesAdd', $sharedStyleData);
                            if ($isExecuteMode) {
                                ElementsSharedStyles::set($sharedStyleData['id'], $sharedStyleData);
                            }
                        }
                    }
                }
            }
            return $elementData;
        };

        $getElementData = function (string $elementID) use ($context): ?array {
            $elementData = $context->getValue('bearcms/elements/element/' . md5($elementID) . '/value.json');
            if ($elementData !== null) {
                $elementData = json_decode($elementData, true);
                if (is_array($elementData) && isset($elementData['type'])) {
                    return $elementData;
                }
            }
            return null;
        };

        $elementChangeEventsToDispatch = [];
        $setNonStructualElementData = function (array $elementData) use ($containerID, $isExecuteMode, &$elementChangeEventsToDispatch) {
            if ($isExecuteMode) {
                self::setLastChangeTime($elementData);
                $elementID = $elementData['id'];
                self::deleteElement($elementID, $containerID, ['updateContainer' => false, 'skipStructuralTypeCheck' => true]); // remove old element with the same id
                InternalDataElements::setElement($elementID, $elementData);
                $elementChangeEventsToDispatch[] = $elementID;
            }
        };

        $generateID = function () use ($isExecuteMode): string {
            return self::generateElementID('ig', null, $isExecuteMode); // , $context->id !== null ? [$containerID, $oldElementID, $context->id] : null
        };

        $elementData = $getElementData($elementID);
        if ($elementData === null) {
            return null; // may not be found when exporting
        }
        $oldElementID = $elementData['id'];
        if (isset($options['newElementID'])) {
            $elementData['id'] = $options['newElementID'];
        } elseif (isset($options['generateNewElementID']) && (int)$options['generateNewElementID'] === 1) {
            $elementData['id'] = $generateID();
        }
        $newElementID = $elementData['id'];
        $isStructural = self::isStructuralElementData($elementData);

        if ($isStructural) {
            if ($containerID === null) {
                throw new \Exception('Container ID is required when importing structural element!');
            }
            $elementData = self::walkStructuralElementChildren($elementData, function (array $elemData) use ($import, $generateID, $getElementData, $setNonStructualElementData) {
                $oldElementID = $elemData['id'];
                $newElementID = $generateID();
                $elemData['id'] = $newElementID;
                if (self::isStructuralElementData($elemData)) {
                    $elemData = $import($elemData, $oldElementID);
                } else {
                    $childElementData = $getElementData($oldElementID);
                    if ($childElementData !== null) {
                        $childElementData['id'] = $newElementID;
                        $childElementData = $import($childElementData, $oldElementID);
                        $setNonStructualElementData($childElementData);
                    }
                }
                return $elemData;
            });
            $elementData = $import($elementData, $oldElementID);
        } else {
            $elementData = $import($elementData, $oldElementID);
            $setNonStructualElementData($elementData);
        }
        if ($containerID !== null) {
            $hasTargetContainer = isset($options['targetContainerData']);
            if ($hasTargetContainer) {
                $containerData = &$options['targetContainerData'];
            } else {
                $containerData = InternalDataElements::getContainer($containerID, true);
            }
            $containerData = self::removeContainerDataElement($containerData, $newElementID); // remove old element with the same id
            $containerData['elements'][] = $isStructural ? $elementData : ['id' => $newElementID];
            if (isset($options['insertTarget']) && $options['insertTarget'] === '-internal-replace') {
                $containerData = self::moveContainerDataElement($containerData, $newElementID, ['beforeElement', $oldElementID]);
                $containerData = self::removeContainerDataElement($containerData, $oldElementID);
            } else {
                if (isset($options['insertTarget'])) {
                    $containerData = self::moveContainerDataElement($containerData, $newElementID, $options['insertTarget']);
                }
            }
            self::setLastChangeTime($containerData);
            if (!$hasTargetContainer) {
                if ($isExecuteMode) {
                    InternalDataElements::setContainer($containerID, $containerData);
                }
            }
        }

        if ($isExecuteMode) {
            foreach ($elementChangeEventsToDispatch as $eventElementID) {
                InternalDataElements::dispatchElementChangeEvent($eventElementID, $containerID);
            }
            if (!$hasTargetContainer) {
                if ($containerID !== null) {
                    InternalDataElements::dispatchContainerChangeEvent($containerID);
                }
            }
        }
        return $newElementID;
    }

    /**
     * 
     * @param string $filename
     * @return integer
     */
    static function getImportElementFromFileUploadsSize(string $filename): int
    {
        $size = 0;
        $result = self::executeImportElementFromFile($filename, true, 'dummy');
        if (isset($result['changes'], $result['changes']['uploadsSizeAdd'])) {
            foreach ($result['changes']['uploadsSizeAdd'] as $uploadSizeData) {
                $size += $uploadSizeData['size'];
            }
        }
        return $size;
    }

    /**
     * 
     * @param string $filename
     * @param string $targetContainerID
     * @param array|null $target
     * @return string|null
     */
    static function importElementFromFile(string $filename, string $targetContainerID, array $target = null): ?string
    {
        $result = self::executeImportElementFromFile($filename, false, $targetContainerID, $target);
        if (isset($result['results'], $result['results'][0], $result['results'][0]['result']) && is_string($result['results'][0]['result'])) {
            return $result['results'][0]['result'];
        }
        throw new \Exception('Invalid result ' . print_r($result, true));
    }

    /**
     * 
     * @param string $filename
     * @param boolean $preview
     * @param string $targetContainerID
     * @param array|null $insertTarget
     * @return array
     */
    static private function executeImportElementFromFile(string $filename, bool $preview, string $targetContainerID, array $insertTarget = null): array
    {
        return \BearCMS\Internal\ImportExport::import($filename, $preview, function ($manifest) use ($targetContainerID, $insertTarget) {
            if (sizeof($manifest['items']) === 1 && $manifest['items'][0]['type'] === 'element') {
                $manifest['items'][0]['importOptions'] = ['generateNewElementID' => true, 'insertTarget' => $insertTarget];
                if (!isset($manifest['items'][0]['args'])) {
                    $manifest['items'][0]['args'] = [];
                }
                if (isset($manifest['items'][0]['elementID'])) {
                    $manifest['items'][0]['args']['elementID'] = $manifest['items'][0]['elementID'];
                }
                $manifest['items'][0]['args']['containerID'] = $targetContainerID;
                return $manifest;
            }
            throw new \Exception('This is not a valid element export file!');
        });
    }

    /**
     * 
     * @param string $filename
     * @return integer
     */
    static function getImportElementsContainerFromFileUploadsSize(string $filename): int
    {
        $size = 0;
        $result = self::executeImportElementsContainerFromFile($filename, true, 'dummy');
        if (isset($result['changes'], $result['changes']['uploadsSizeAdd'])) {
            foreach ($result['changes']['uploadsSizeAdd'] as $uploadSizeData) {
                $size += $uploadSizeData['size'];
            }
        }
        return $size;
    }

    /**
     * 
     * @param string $filename
     * @param string $targetContainerID
     * @return string|null
     */
    static function importElementsContainerFromFile(string $filename, string $targetContainerID): ?string
    {
        $result = self::executeImportElementsContainerFromFile($filename, false, $targetContainerID);
        if (isset($result['results'], $result['results'][0], $result['results'][0]['result']) && is_string($result['results'][0]['result'])) {
            return $result['results'][0]['result'];
        }
        throw new \Exception('Invalid result ' . print_r($result, true));
    }

    /**
     * 
     * @param string $filename
     * @param boolean $preview
     * @param string $containerID
     * @return array
     */
    static private function executeImportElementsContainerFromFile(string $filename, bool $preview, string $targetContainerID): array
    {
        return \BearCMS\Internal\ImportExport::import($filename, $preview, function ($manifest) use ($targetContainerID) {
            if (sizeof($manifest['items']) === 1 && $manifest['items'][0]['type'] === 'elementsContainer') {
                $manifest['items'][0]['importOptions'] = ['targetContainerID' => $targetContainerID];
                return $manifest;
            }
            throw new \Exception('This is not a valid elements container export file!');
        });
    }

    /**
     * 
     * @param string $elementType
     * @param boolean $returnAsArray
     * @return array|null
     */
    static function getDefaultElementStyle(string $elementType, bool $returnAsArray = false): ?array
    {
        if ($elementType === 'columns') {
            $layout = ['value' => ['direction' => 'horizontal', 'widths' => ';']];
            return ['layout' => $returnAsArray ? $layout : json_encode($layout)];
        } elseif ($elementType === 'floatingBox') {
            $layout = ['value' => ['position' => 'left', 'width' => '50%']];
            return ['layout' => $returnAsArray ? $layout : json_encode($layout)];
        } elseif ($elementType === 'flexibleBox') {
            $layout = ['value' => ['direction' => 'vertical', 'alignment' => 'start']];
            return ['layout' => $returnAsArray ? $layout : json_encode($layout)];
        } elseif ($elementType === 'slider') {
            $layout = ['value' => ['direction' => 'horizontal', 'alignment' => 'start', 'speed' => '300ms', 'swipe' => '1']];
            $nextButton = '{"background-color":"rgba(0,0,0,0.5)","width":"30px","height":"30px","border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%","background-image":"url(addon:bearcms\/bearframework-addon:assets\/slider-next.svg)","background-position":"center center","background-repeat":"no-repeat","background-size":"14px"}';
            $nextButtonVisibility = '{"type":"floating","top":"calc(50% - 15px)","right":"10px"}';
            $previousButton = '{"background-color":"rgba(0,0,0,0.5)","width":"30px","height":"30px","border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%","background-image":"url(addon:bearcms\/bearframework-addon:assets\/slider-previous.svg)","background-position":"center center","background-repeat":"no-repeat","background-size":"14px"}';
            $previousButtonVisibility = '{"type":"floating","top":"calc(50% - 15px)","left":"10px"}';
            $indicator = '{"background-color":"rgba(0,0,0,0.1)","width":"12px","height":"12px","border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%","margin-left":"3px","margin-right":"3px"}';
            $indicatorSelected = '{"background-color":"rgba(0,0,0,0.5)"}';
            $indicators = '{"width":"100%","text-align":"center"}';
            $indicatorsVisibility = '{"type":"floating","bottom":"10px"}';
            return [
                'layout' => $returnAsArray ? $layout : json_encode($layout),
                'nextButton' => $returnAsArray ? json_decode($nextButton, true) : $nextButton,
                'nextButtonVisibility' => $returnAsArray ? json_decode($nextButtonVisibility, true) : $nextButtonVisibility,
                'previousButton' => $returnAsArray ? json_decode($previousButton, true) : $previousButton,
                'previousButtonVisibility' => $returnAsArray ? json_decode($previousButtonVisibility, true) : $previousButtonVisibility,
                'indicator' => $indicator,
                'indicatorSelected' => $indicatorSelected,
                'indicators' => $indicators,
                'indicatorsVisibility' => $indicatorsVisibility,
            ];
        }
        return [];
    }
}
