<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\ElementStylesHelper;

$elementID = (string)$component->id;
$elementID = isset($elementID[0]) ? $elementID : null;
$editable = $component->editable === 'true';
$typeCode = $component->getAttribute('bearcms-internal-attribute-type');
$containerType = $component->getAttribute('bearcms-internal-attribute-container');
$inElementsContainer = $component->getAttribute('bearcms-internal-attribute-in-elements-container') === 'true';
$canStyle = $component->canStyle === 'true';

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
if ($outputType !== 'full-html') {
    $editable = false;
}

if ($editable) {
    $componentContextData = ElementsHelper::getComponentContextData($component);
}

$componentSrc = (string)$component->src;
$componentName = strlen($componentSrc) > 0 ? $componentSrc : ($component->tagName !== 'component' ? $component->tagName : null);
$isMissing = $componentName === 'bearcms-missing-element';

$elementType = null;
$elementStyleID = null;
$elementStyleValue = null;
$elementTags = [];
if (!$isMissing) {
    $rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
    if ($rawData !== null && strlen($rawData) > 0) {
        $elementData = \BearCMS\Internal\Data\Elements::decodeElementRawData($rawData);
        $data = $elementData['data'];
        $elementTypeDefinition = ElementsHelper::$elementsTypeDefinitions[$componentName];
        foreach ($elementTypeDefinition->properties as $property) {
            $propertyID = $property['id'];
            $propertyType = $property['type'];
            if ($propertyType === 'bool') {
                $component->$propertyID = isset($data[$propertyID]) ? ($data[$propertyID] ? 'true' : 'false') : '';
            } else { // int, float and string
                $component->$propertyID = isset($data[$propertyID]) ? (string) $data[$propertyID] : '';
            }
        }
        if (is_callable($elementTypeDefinition->updateComponentFromData)) {
            $component = call_user_func($elementTypeDefinition->updateComponentFromData, clone ($component), $data);
        }
        if (isset($elementData['type'])) {
            $elementType = $elementData['type'];
        }
        if (isset($elementData['styleID'])) {
            $elementStyleID = $elementData['styleID'];
        }
        if (isset($elementData['style'])) {
            $elementStyleValue = $elementData['style'];
        }
        if (isset($elementData['tags'])) {
            $elementTags = $elementData['tags'];
        }

        unset($rawData);
        unset($elementData);
        unset($data);
        unset($options);
    } else {
        if ($elementID !== null && $component->editable === 'true') {
            $getRawDataFromComponent = function ($component) use ($elementID) {
                $componentSrc = (string)$component->src;
                $componentName = strlen($componentSrc) > 0 ? $componentSrc : ($component->tagName !== 'component' ? $component->tagName : null);
                $elementTypeDefinition = ElementsHelper::$elementsTypeDefinitions[$componentName];
                $data = [];
                foreach ($elementTypeDefinition->properties as $property) {
                    $propertyID = $property['id'];
                    $propertyType = $property['type'];
                    if ($propertyType === 'int') {
                        $data[$propertyID] = (int) $component->$propertyID;
                    } elseif ($propertyType === 'float') {
                        $data[$propertyID] = (float) $component->$propertyID;
                    } elseif ($propertyType === 'bool') {
                        $data[$propertyID] = $component->$propertyID === 'true';
                    } else { // text
                        $data[$propertyID] = (string) $component->$propertyID;
                    }
                }
                if (is_callable($elementTypeDefinition->updateDataFromComponent)) {
                    $data = call_user_func($elementTypeDefinition->updateDataFromComponent, clone ($component), $data);
                }
                return json_encode(['id' => $elementID, 'type' => ElementsHelper::$elementsTypeComponents[$componentName], 'data' => $data], JSON_THROW_ON_ERROR);
            };
            if ($editable) {
                $componentContextData['rawData'] = $getRawDataFromComponent($component);
            }
            $elementType = ElementsHelper::$elementsTypeComponents[$componentName];
        }
    }

    $filename = $component->getAttribute('bearcms-internal-attribute-filename');
    $component->setAttribute('src', 'file:' . $filename);

    $component->removeAttribute('bearcms-internal-attribute-type');
    $component->removeAttribute('bearcms-internal-attribute-filename');
    $component->removeAttribute('bearcms-internal-attribute-container');
    $component->removeAttribute('bearcms-internal-attribute-raw-data');
    $componentHTML = (string) $component;
} else {
    $componentHTML = '';
    $app = App::get();
    if ($app->bearCMS->currentUser->exists()) {
        $componentHTML .= '<div style="background-color:red;color:#fff;padding:10px 15px 9px 15px;border-radius:4px;line-height:25px;font-size:14px;font-family:Arial,sans-serif;">';
        $componentHTML .= __('bearcms.element.UnsupportedElement.title') . '<div style="font-size:11px;">' . __('bearcms.element.UnsupportedElement.description') . '</div>';
        $componentHTML .= '</div>';
    }
}

if ($containerType === 'none') {
    echo $componentHTML;
} else {
    $classAttributeValue = '';
    $attributes = '';
    if ($editable) {
        $htmlElementID = ElementsHelper::getHTMLElementID($elementID);
        $attributes .= ' id="' . $htmlElementID . '"';
        ElementsHelper::$editorData[] = ['element', $elementID, $componentContextData, $typeCode];
    } else {
        ElementsHelper::$renderedData[] = ['element', $elementID];
    }
    $styleSelector = null;
    if ($outputType === 'full-html') {
        $classAttributeValue .= ' bearcms-element';
        if ($canStyle && $elementID !== null) {
            list($styleID, $styleValue) = ElementStylesHelper::getElementRealStyleData($elementStyleID, $elementStyleValue);
            $styleSelector = ElementStylesHelper::getElementStyleSelector($elementID, $styleID);
            if ($styleSelector !== null) {
                $classAttributeValue .= ' ' . ElementStylesHelper::getElementStyleClassName($elementID, $styleID);
            }
        }
    }
    if ($classAttributeValue !== '') {
        $attributes .= ' class="' . trim($classAttributeValue) . '"';
    }
    if (!empty($elementTags)) {
        $attributes .= ElementsHelper::getTagsHTMLAttributes($elementTags);
    }
    $content = '<html><head>';
    if ($styleSelector !== null) {
        $content .= ElementsHelper::getStyleHTML($elementType, $styleValue, $styleSelector, true, !$editable);
    }
    $content .= '</head><body>';
    if ($editable && !$inElementsContainer) {
        $content .= '<div>';
    }
    $content .= '<div' . $attributes . '>';
    $content .= $componentHTML;
    $content .= '</div>';
    if ($editable && !$inElementsContainer) {
        $content .= '</div>';
    }
    $content .= '</body></html>';
    echo $content;
}
