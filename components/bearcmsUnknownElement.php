<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearCMS\Internal\ElementsDataHelper;
use BearCMS\Internal\ElementsHelper;

$attributes = $component->getAttributes();
$newComponent = clone ($component);
$elementID = $component->getAttribute('id');
$elementType = null;
if ($elementID !== null) {
    $elementData = InternalDataElements::getElement($elementID);
    if ($elementData !== null) {
        if (isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $containerID = '-bearcms-internal-external-element-container';
            $newComponent->setAttribute('bearcms-internal-attribute-external-element-id', $elementID);
        }
    } else {
        $containerID = $component->getAttribute('container-id');
        if ($containerID !== null) {
            $containerData = InternalDataElements::getContainer($containerID);
            if ($containerData !== null) {
                $elementDataInContainer = ElementsDataHelper::getContainerDataElement($containerData, $elementID);
                if (isset($elementDataInContainer['type'])) {
                    $elementType = $elementDataInContainer['type'];
                }
            }
        }
    }
}

if ($elementType !== null) {
    $updated = false;
    $newComponent->removeAttribute('container-id');
    if ($elementType === 'columns') {
        $newComponent->setAttribute('src', 'bearcms-elements');
        $newComponent->setAttribute('id', $containerID);
        $newComponent->setAttribute('bearcms-internal-attribute-columns-id', $elementID);
        $updated = true;
    } elseif ($elementType === 'floatingBox') {
        $newComponent->setAttribute('src', 'bearcms-elements');
        $newComponent->setAttribute('id', $containerID);
        $newComponent->setAttribute('bearcms-internal-attribute-floatingbox-id', $elementID);
        $updated = true;
    } elseif ($elementType === 'flexibleBox') {
        $newComponent->setAttribute('src', 'bearcms-elements');
        $newComponent->setAttribute('id', $containerID);
        $newComponent->setAttribute('bearcms-internal-attribute-flexiblebox-id', $elementID);
        $updated = true;
    } elseif ($elementType === 'slider') {
        $newComponent->setAttribute('src', 'bearcms-elements');
        $newComponent->setAttribute('id', $containerID);
        $newComponent->setAttribute('bearcms-internal-attribute-slider-id', $elementID);
        $updated = true;
    } else {
        $componentName = array_search($elementType, ElementsHelper::$elementsTypeComponents);
        if ($componentName !== false) {
            $newComponent->setAttribute('src', $componentName);
            $updated = true;
        }
    }
    if ($updated) {
        echo (string)$newComponent;
    }
}
