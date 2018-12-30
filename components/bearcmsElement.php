<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

$editable = $component->editable === 'true';
$typeCode = $component->getAttribute('bearcms-internal-attribute-type');
$containerType = $component->getAttribute('bearcms-internal-attribute-container');
$inElementsContainer = $component->getAttribute('bearcms-internal-attribute-in-elements-container') === 'true';

if ($editable) {
    $componentContextData = Internal\ElementsHelper::getComponentContextData($component);
}

$isMissing = $component->src === 'bearcms-missing-element';

if (!$isMissing) {
    $rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
    if ($rawData !== null && strlen($rawData) > 0) {
        $rawData = json_decode($rawData, true);
        $data = $rawData['data'];
        $options = Internal\ElementsHelper::$elementsTypesOptions[$component->src];
        if (isset($options['fields'])) {
            foreach ($options['fields'] as $field) {
                $fieldID = $field['id'];
                $fieldType = $field['type'];
                if ($fieldType === 'number') {
                    $component->$fieldID = isset($data[$fieldID]) ? (string) $data[$fieldID] : '';
                } elseif ($fieldType === 'checkbox') {
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
                $options = Internal\ElementsHelper::$elementsTypesOptions[$component->src];
                $data = [];
                if (isset($options['fields'])) {
                    foreach ($options['fields'] as $field) {
                        $fieldID = $field['id'];
                        $fieldType = $field['type'];
                        if ($fieldType === 'number') {
                            $data[$fieldID] = (int) $component->$fieldID;
                        } elseif ($fieldType === 'checkbox') {
                            $data[$fieldID] = $component->$fieldID === 'true';
                        } else {
                            $data[$fieldID] = (string) $component->$fieldID;
                        }
                    }
                }
                if (isset($options['updateDataFromComponent'])) {
                    $data = call_user_func($options['updateDataFromComponent'], clone($component), $data);
                }
                return json_encode(['id' => $component->id, 'type' => Internal\ElementsHelper::$elementsTypesCodes[$component->src], 'data' => $data]);
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
} else {
    $componentHTML = '';
    $app = App::get();
    if ($app->bearCMS->currentUser->exists()) {
        $componentHTML .= '<div style="background-color:red;color:#fff;padding:10px 15px 9px 15px;border-radius:4px;line-height:25px;font-size:14px;font-family:Arial,sans-serif;">';
        $componentHTML .= 'Unsupported element!<div style="font-size:11px;">This message is visible to administrators only.</div>';
        $componentHTML .= '</div>';
    }
}

if ($containerType === 'none') {
    echo $componentHTML;
} else {
    $attributes = '';
    if ($editable) {
        Internal\ElementsHelper::$editorData[] = ['element', $component->id, $componentContextData, $typeCode];
        $htmlElementID = 'brelc' . md5($component->id);
        $attributes .= ' id="' . $htmlElementID . '"';
    }
    $attributes .= ' class="bearcms-elements-element-container"';
    if ($editable && !$inElementsContainer) {
        echo '<div>';
    }
    echo '<div' . $attributes . '>';
    echo $componentHTML;
    echo '</div>';
    if ($editable && !$inElementsContainer) {
        echo '</div>';
    }
}