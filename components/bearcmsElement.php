<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\ElementsHelper;

$editable = $component->editable === 'true';
$typeCode = $component->getAttribute('bearcms-internal-attribute-type');
$containerType = $component->getAttribute('bearcms-internal-attribute-container');

if ($editable) {
    $componentContextData = ElementsHelper::getComponentContextData($component);
}

$rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
if ($rawData !== null) {
    $rawData = json_decode($rawData, true);
    $data = $rawData['data'];
    $options = ElementsHelper::$elementsTypesOptions[$component->src];
    if (isset($options['fields'])) {
        foreach ($options['fields'] as $field) {
            $fieldID = $field['id'];
            $fieldType = $field['type'];
            if ($fieldType === 'number') {
                $component->$fieldID = isset($data[$fieldID]) ? (string) $data[$fieldID] : '';
            } elseif ($fieldType === 'boolean') {//todo
                $component->$fieldID = isset($data[$fieldID]) ? ($data[$fieldID] ? 'true' : 'false') : '';
            } else {
                $component->$fieldID = isset($data[$fieldID]) ? (string) $data[$fieldID] : '';
            }
        }
    }
    if (isset($options['updateComponentFromData'])) {
        $component = call_user_func($options['updateComponentFromData'], clone($component), $data);
    }

    unset($rawData);
    unset($data);
    unset($options);
} else {
    if (strlen($component->id) > 0 && $component->editable === 'true') {
        $getRawDataFromComponent = function($component) {
            $options = ElementsHelper::$elementsTypesOptions[$component->src];
            $data = [];
            if (isset($options['fields'])) {
                foreach ($options['fields'] as $field) {
                    $fieldID = $field['id'];
                    $fieldType = $field['type'];
                    if ($fieldType === 'number') {
                        $data[$fieldID] = (int) $component->$fieldID;
                    } elseif ($fieldType === 'boolean') {//todo
                        $data[$fieldID] = $component->$fieldID === 'true';
                    } else {
                        $data[$fieldID] = (string) $component->$fieldID;
                    }
                }
            }
            if (isset($options['updateDataFromComponent'])) {
                $data = call_user_func($options['updateDataFromComponent'], clone($component), $data);
            }
            return json_encode(['id' => $component->id, 'type' => ElementsHelper::$elementsTypesCodes[$component->src], 'data' => $data]);
        };
        if ($editable) {
            $componentContextData['rawData'] = $getRawDataFromComponent($component);
        }
    }
}

$filename = $component->getAttribute('bearcms-internal-attribute-filename');
$component->setAttribute('src', 'file:' . $filename);

$component->removeAttribute('bearcms-internal-attribute-type');
$component->removeAttribute('bearcms-internal-attribute-filename');
$component->removeAttribute('bearcms-internal-attribute-container');
$component->removeAttribute('bearcms-internal-attribute-raw-data');
$componentHTML = (string) $component;
if ($containerType === 'none') {
    echo $componentHTML;
} else {
    $attributes = '';
    if ($editable) {
        ElementsHelper::$editorData[] = ['element', $component->id, $componentContextData, $typeCode];
        $htmlElementID = 'brelc' . md5($component->id);
        $attributes .= ' id="' . $htmlElementID . '"';
    }
    echo '<div' . $attributes . '>' . $componentHTML . '</div>';
}