<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearCMS\Internal\ElementsHelper;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class Elements
{


    static function copyContainer(string $sourceContainerID, string $targetContainerID): void
    {
        $app = App::get();
        $generateItemID = function () {
            for ($i = 0; $i < 100; $i++) {
                $id = base_convert(md5(uniqid()), 16, 36) . 'cc';
                $elementsRawData = ElementsHelper::getElementsRawData([$id]);
                if ($elementsRawData[$id] === null) {
                    return $id;
                }
            }
            throw new \Exception('Too much retries');
        };
        $containerData = ElementsHelper::getContainerData($sourceContainerID);
        $newContainerData = $containerData;
        $newContainerData['id'] = $targetContainerID;
        $itemsToCopy = [];
        $updateElements = function ($elements) use (&$updateElements, &$itemsToCopy, $generateItemID) {
            foreach ($elements as $index => $element) {
                if (isset($element['id'])) {
                    $oldItemID = $element['id'];
                    $newItemID = $generateItemID();
                    $elements[$index]['id'] = $newItemID;
                    if (isset($element['data'], $element['data']['type'])) {
                        if ($element['data']['type'] === 'floatingBox' || $element['data']['type'] === 'columns') {
                            if (isset($element['data']['elements'])) {
                                foreach ($element['data']['elements'] as $location => $locationElements) {
                                    $elements[$index]['data']['elements'][$location] = $updateElements($locationElements);
                                }
                            }
                        } else {
                            throw new \Exception('Unsupported type for an element');
                        }
                    } else {
                        $itemsToCopy[$oldItemID] = $newItemID;
                    }
                } else {
                    throw new \Exception('Missing id for an element');
                }
            }
            return $elements;
        };
        $newContainerData['elements'] = $updateElements($newContainerData['elements']);

        foreach ($itemsToCopy as $sourceElementID => $targetElementID) {
            $elementData = ElementsHelper::getElementData($sourceElementID);
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
            $app->data->setValue('bearcms/elements/element/' . md5($elementData['id']) . '.json', json_encode($elementData));
        }
        $app->data->setValue('bearcms/elements/container/' . md5($newContainerData['id']) . '.json', json_encode($newContainerData));
    }

    static function getContainerUploadsSize(string $containerID): int
    {
        $size = 0;
        $elementsIDs = ElementsHelper::getContainerElementsIDs($containerID);
        foreach ($elementsIDs as $elementID) {
            $size += self::getElementUploadsSize($elementID);
        }
        return $size;
    }

    static function getElementUploadsSize(string $elementID): int
    {
        $elementData = ElementsHelper::getElementData($elementID);
        if ($elementData !== null) {
            if (isset($elementData['type'])) {
                $componentName = array_search($elementData['type'], ElementsHelper::$elementsTypesCodes);
                if ($componentName !== false) {
                    $options = ElementsHelper::$elementsTypesOptions[$componentName];
                    if (isset($options['getUploadsSize']) && is_callable($options['getUploadsSize'])) {
                        return (int) call_user_func($options['getUploadsSize'], isset($elementData['data']) ? $elementData['data'] : []);
                    }
                }
            }
        }
        return 0;
    }
}
