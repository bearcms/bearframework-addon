<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\ElementsHelper;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Elements
{

    /**
     * 
     * @param string $elementID
     * @return string
     */
    static private function getElementDataKey(string $elementID): string
    {
        return 'bearcms/elements/element/' . md5($elementID) . '.json';
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    static function encodeElementData(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Returns an array (['id' => ..., 'type' => ..., 'data' => ...]) or null if data is invalid
     * @param string $data
     * @return array|null
     */
    static function decodeElementRawData(string $data): ?array
    {
        $data = json_decode($data, true);
        if (
            is_array($data) &&
            isset($data['id']) && is_string($data['id']) && strlen($data['id']) > 0 &&
            isset($data['type']) && is_string($data['type']) && strlen($data['type']) > 0 &&
            isset($data['data']) && is_array($data['data'])
        ) {
            return $data;
        }
        return null;
    }

    /**
     * 
     * @param array $elementsIDs
     * @return array
     */
    static function getElementsRawData(array $elementsIDs): array
    {
        $result = [];
        $elementsIDs = array_unique($elementsIDs);
        foreach ($elementsIDs as $elementID) {
            $result[$elementID] = self::getElementRawData($elementID);
        }
        return $result;
    }

    /**
     * 
     * @param string $elementID
     * @return string|null
     */
    static function getElementRawData(string $elementID): ?string
    {
        return InternalData::getValue(self::getElementDataKey($elementID));
    }

    /**
     * 
     * @param string $elementID
     * @return array|null
     */
    static function getElement(string $elementID): ?array // todo add $containerID
    {
        $data = self::getElementRawData($elementID);
        return $data !== null ? self::decodeElementRawData($data) : null;
    }

    /**
     * 
     * @param string $elementID
     * @param array $data
     * @param string|null $containerID
     * @return void
     */
    static function setElement(string $elementID, array $data, string $containerID = null): void
    {
        $app = App::get();
        $app->data->setValue(self::getElementDataKey($elementID), self::encodeElementData($data));
    }

    /**
     * Delete only the element's data. Use ElementsHelper::deleteElement() to delete artifacts.
     *
     * @param string $elementID
     * @param string|null $containerID
     * @return void
     */
    static function deleteElement(string $elementID, string $containerID = null): void
    {
        $app = App::get();
        $app->data->delete(self::getElementDataKey($elementID));
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return void
     */
    static function dispatchElementChangeEvent(string $elementID, string $containerID = null): void
    {
        $app = App::get();
        $bearCMS = $app->bearCMS;
        if ($bearCMS->hasEventListeners('internalElementChange')) {
            $eventDetails = new \BearCMS\Internal\ElementChangeEventDetails($elementID, $containerID);
            $bearCMS->dispatchEvent('internalElementChange', $eventDetails);
        }
    }

    /**
     * Optimizes the element's data
     *
     * @param string $elementID
     * @param string|null $containerID
     * @return void
     */
    static function optimizeElement(string $elementID, string $containerID = null): void
    {
        $data = self::getElement($elementID);
        if ($data !== null) {
            $optimizedData = self::getOptimizedElementData($data);
            if (is_array($optimizedData)) {
                self::setElement($elementID, $optimizedData, $containerID);
            }
        }
    }

    /**
     * Returns the element's optimized data.
     *
     * @param array $elementData
     * @return array|null Returns array if optimized, NULL otherwise.
     */
    static function getOptimizedElementData(array $elementData): ?array
    {
        if (isset($elementData['type'], $elementData['data'])) {
            $componentName = array_search($elementData['type'], ElementsHelper::$elementsTypesCodes);
            if ($componentName !== false) {
                $options = ElementsHelper::$elementsTypesOptions[$componentName];
                if (isset($options['optimizeData']) && is_callable($options['optimizeData'])) {
                    $newData = call_user_func($options['optimizeData'], $elementData['data']);
                    if (is_array($newData)) {
                        $elementData['data'] = $newData;
                        return $elementData;
                    }
                }
            }
        }
        return null;
    }

    /**
     * 
     * @param string $containerID
     * @return string
     */
    static private function getContainerDataKey(string $containerID): string
    {
        return 'bearcms/elements/container/' . md5($containerID) . '.json';
    }

    /**
     * 
     * @param string $containerID
     * @return array
     * @throws \Exception
     */
    static function getContainer(string $containerID): array
    {
        $container = InternalData::getValue(self::getContainerDataKey($containerID));
        $data = $container !== null ? json_decode($container, true) : [];
        if (!isset($data['elements'])) {
            $data['elements'] = [];
        }
        if (!is_array($data['elements'])) {
            throw new \Exception('');
        }
        return $data;
    }

    /**
     * 
     * @param string $containerID
     * @param array $data
     */
    static function setContainer(string $containerID, array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getContainerDataKey($containerID), json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * 
     * @param string $containerID
     * @return void
     */
    static function deleteContainer(string $containerID): void
    {
        $app = App::get();
        $containerDataKey = self::getContainerDataKey($containerID);
        $app->data->delete($containerDataKey);
    }

    /**
     * 
     * @param string $containerID
     * @return void
     */
    static function dispatchContainerChangeEvent(string $containerID): void
    {
        $app = App::get();
        $bearCMS = $app->bearCMS;
        if ($bearCMS->hasEventListeners('internalElementsContainerChange')) {
            $eventDetails = new \BearCMS\Internal\ElementsContainerChangeEventDetails($containerID);
            $bearCMS->dispatchEvent('internalElementsContainerChange', $eventDetails);
        }
    }

    /**
     * 
     * @param string $dataKey
     * @param boolean $preview
     * @return array
     */
    static function fixContainerStructuralElements(string $dataKey, bool $preview = true): array
    {
        if (strpos($dataKey, 'bearcms/elements/container/') !== 0) {
            return ['error' => 'Wrong data key!'];
        }

        $app = App::get();

        $walkElements = function ($elements) use (&$walkElements): array {
            $hasChange = false;
            $updatedElements = [];
            foreach ($elements as $elementData) {
                $structuralElementData = ElementsHelper::getUpdatedStructuralElementData($elementData);
                if ($structuralElementData !== null) {
                    $_hasChange = false;
                    if (isset($elementData['elements']) && isset($elementData['data']) && !isset($elementData['type'])) { // Has 'elements' and 'data' keys, but no 'type' key
                        $_hasChange = true;
                    }
                    if ($structuralElementData['type'] === 'columns') {
                        if (isset($structuralElementData['elements'])) {
                            for ($i = 0; $i < 100; $i++) {
                                if (isset($structuralElementData['elements'][$i])) {
                                    $result = $walkElements($structuralElementData['elements'][$i]);
                                    if ($result[0]) {
                                        $_hasChange = true;
                                        $structuralElementData['elements'][$i] = $result[1];
                                    }
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'floatingBox') {
                        if (isset($structuralElementData['elements'])) {
                            if (isset($structuralElementData['elements']['inside'])) {
                                $result = $walkElements($structuralElementData['elements']['inside']);
                                if ($result[0]) {
                                    $_hasChange = true;
                                    $structuralElementData['elements']['inside'] = $result[1];
                                }
                            }
                            if (isset($structuralElementData['elements']['outside'])) {
                                $result = $walkElements($structuralElementData['elements']['outside']);
                                if ($result[0]) {
                                    $_hasChange = true;
                                    $structuralElementData['elements']['outside'] = $result[1];
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'flexibleBox') {
                        if (isset($structuralElementData['elements'])) {
                            $result = $walkElements($structuralElementData['elements']);
                            if ($result[0]) {
                                $_hasChange = true;
                                $structuralElementData['elements'] = $result[1];
                            }
                        }
                    }
                    if ($_hasChange) {
                        $hasChange = true;
                        $updatedElements[] = $structuralElementData;
                    } else {
                        $updatedElements[] = $elementData;
                    }
                } else {
                    $updatedElements[] = $elementData;
                }
            }
            return [$hasChange, $updatedElements];
        };

        $result = [];
        $containerDataValue = $app->data->getValue($dataKey);
        $containerData = $containerDataValue !== null ? json_decode($containerDataValue, true) : [];
        if (isset($containerData['elements'])) {
            $walkElementsResult = $walkElements($containerData['elements']);
            if ($walkElementsResult[0]) { // has change
                $updatedContainerData = $containerData;
                $updatedContainerData['elements'] = $walkElementsResult[1];
                if (!$preview) {
                    $app->data->duplicate($dataKey, '.recyclebin/bearcms/update-' . str_replace('.', '-', microtime(true)) . '-' . str_replace('/', '-', $dataKey));
                    $app->data->setValue($dataKey, json_encode($updatedContainerData, JSON_THROW_ON_ERROR));
                }
                $result['new'] = $updatedContainerData;
                $result['old'] = $containerData;
            }
        } else {
            return ['error' => 'Missing elements key!'];
        }
        return $result;
    }
}
