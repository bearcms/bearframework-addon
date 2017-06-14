<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class ElementsHelper
{

    static $editorData = [];
    static $elementsTypesCodes = [];
    static $elementsTypesFilenames = [];
    static $elementsTypesOptions = [];
    static $lastLoadMoreServerData = null;

    /**
     * 
     * @param type $component
     */
    static function updateComponentEditableAttribute($component)
    {
        $app = App::get();
        $editable = false;
        if ($component->editable === 'true' && strlen($component->id) > 0) {
            if ($app->bearCMS->currentUser->exists() && $app->bearCMS->currentUser->hasPermission('modifyContent')) {
                $editable = true;
            }
        }
        $component->editable = $editable ? 'true' : 'false';
    }

    /**
     * 
     * @param type $component
     */
    static function updateComponentContextAttributes($component)
    {
        $getUpdatedHTMLUnit = function($value) {
            if (strlen($value) > 0) {
                if (is_numeric($value)) {
                    $value .= 'px';
                }
                if (preg_match('/^(([0-9]+)|(([0-9]*)\.([0-9]+)))(px|rem|em|%|in|cm)$/', $value) !== 1) {
                    $value = '';
                }
            }
            return (string) $value;
        };

        // Update width
        $component->width = $getUpdatedHTMLUnit($component->width);
        if ((string) $component->width === '') {
            $component->width = '100%';
        }

        // Update spacing
        $component->spacing = $getUpdatedHTMLUnit($component->spacing);
        if ((string) $component->spacing === '') {
            $component->spacing = '1rem';
        }

        // Update color
        if (strlen($component->color) > 0) {
            if (preg_match('/^#[0-9a-fA-F]{6}$/', $component->color) !== 1) {
                $component->color = '';
            }
        }
        if ((string) $component->color === '') {
            $component->color = Options::$uiColor;
        }

        // Update canEdit
//        if (strlen($component->canEdit) > 0) {
//            if ($component->canEdit !== 'true') {
//                $component->canEdit = '';
//            }
//        }
//        if ((string) $component->canEdit === '') {
//            $component->canEdit = 'true';
//        }
        // Update canMove
//        if (strlen($component->canMove) > 0) {
//            if ($component->canMove !== 'true') {
//                $component->canMove = '';
//            }
//        }
//        if ((string) $component->canMove === '') {
//            $component->canMove = 'true';
//        }
        // Update canDelete
//        if (strlen($component->canDelete) > 0) {
//            if ($component->canDelete !== 'true') {
//                $component->canDelete = '';
//            }
//        }
//        if ((string) $component->canDelete === '') {
//            $component->canDelete = 'true';
//        }
    }

    /**
     * 
     * @param type $component
     * @return type
     */
    static function getComponentContextData($component)
    {
        $result = [];
        $result['width'] = $component->width;
        $result['spacing'] = $component->spacing;
        $result['color'] = $component->color;
//        $result['canEdit'] = $component->canEdit === 'true';
//        $result['canMove'] = $component->canMove === 'true';
//        $result['canDelete'] = $component->canDelete === 'true';

        $otherAttributes = [];
        $attributesToSkip = ['src', 'id', 'editable', 'width', 'spacing', 'color', 'group'];
        foreach ($component->attributes as $key => $value) {
            $add = true;
            if (array_search($key, $attributesToSkip) !== false || strpos($key, 'bearcms-internal-attribute-') === 0) {
                $add = false;
            }
            if ($add && isset(ElementsHelper::$elementsTypesOptions[$component->src])) {
                $options = ElementsHelper::$elementsTypesOptions[$component->src];
                if (isset($options['fields'])) {
                    foreach ($options['fields'] as $field) {
                        if (strtolower($key) === strtolower($field['id'])) {
                            $add = false;
                            break;
                        }
                    }
                }
            }
            if ($add) {
                $otherAttributes[$key] = $value;
            }
        }
        if (!empty($otherAttributes)) {
            $result['componentAttributes'] = $otherAttributes;
        }
        return $result;
    }

    /**
     * 
     * @param type $component
     * @throws \Exception
     */
    static function updateContainerComponent($component)
    {
        if (strlen($component->id) === 0) {
            throw new \Exception('');
        }
        self::updateComponentEditableAttribute($component);
        self::updateComponentContextAttributes($component);
        if (strlen($component->group) === 0) {
            $component->group = 'default';
        }
    }

    /**
     * 
     * @param type $component
     * @throws \Exception
     */
    static function updateElementComponent($component)
    {
        $rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
        $elementData = null;
        if (strlen($rawData) > 0) {
            $elementData = self::decodeElementRawData($rawData);
            $component->id = $elementData['id'];
        } elseif (strlen($component->id) > 0) {
            $elementsRawData = self::getElementsRawData([$component->id]);
            if (isset($elementsRawData[$component->id])) {
                $component->setAttribute('bearcms-internal-attribute-raw-data', $elementsRawData[$component->id]);
                //$elementData = self::decodeElementRawData($elementsRawData[$component->id]);
            }
        }
        //if ($elementData !== null) {
        //self::updateComponentFromRawData($component, $elementData);
        //}

        self::updateComponentEditableAttribute($component);
        self::updateComponentContextAttributes($component);
    }

    /**
     * 
     * @param type $data
     * @return type
     * @throws \Exception
     */
    static function decodeElementRawData($data)
    {
        $data2 = $data;
        $data = json_decode($data, true);
        if (!is_array($data)) {
            throw new \Exception('Invalid element data');
        }
        if (!isset($data['id']) || !is_string($data['id'])) {
            throw new \Exception('Missing element id');
        }
        if (!isset($data['type']) || !is_string($data['type'])) {
            throw new \Exception('Missing element type');
        }
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new \Exception('Missing element data');
        }
        return $data;
    }

    /**
     * 
     * @param type $rawData
     * @param type $editable
     * @param type $contextData
     * @return type
     * @throws \Exception
     */
    static function renderElement($rawData, $editable, $contextData)
    {
        $elementData = self::decodeElementRawData($rawData);
        if (!isset($elementData['id']) || strlen($elementData['id']) === 0) {
            throw new \Exception('Missing element id');
        }
        if (!isset($elementData['type']) || strlen($elementData['type']) === 0) {
            throw new \Exception('Missing element type');
        }
        $componentName = array_search($elementData['type'], self::$elementsTypesCodes);
        if ($componentName === false) {
            throw new \Exception('Invalid element type');
        }
        return '<component'
                . ' src="' . $componentName . '"'
                . ' editable="' . ($editable ? 'true' : 'false') . '"'
                . ' bearcms-internal-attribute-raw-data="' . htmlentities($rawData) . '"'
                . ' width="' . $contextData['width'] . '"'
                . ' spacing="' . $contextData['spacing'] . '"'
                . ' color="' . $contextData['color'] . '"'
                . '/>'; // canEdit="' . ($contextData['canEdit'] ? 'true' : 'false') . '" canMove="' . ($contextData['canMove'] ? 'true' : 'false') . '" canDelete="' . ($contextData['canDelete'] ? 'true' : 'false') . '"
    }

    /**
     * 
     * @param type $elementContainerData
     * @param type $editable
     * @param type $contextData
     * @param type $inContainer
     * @return type
     */
    static function renderColumn($elementContainerData, $editable, $contextData, $inContainer)
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        $columnsSizes = explode(':', $elementContainerData['data']['mode']);
        $columnsCount = sizeof($columnsSizes);
        $totalSize = array_sum($columnsSizes);
        $spacing = $contextData['spacing'];

        $content = '';
        for ($i = 0; $i < $columnsCount; $i++) {
            $columnContent = '';
            if (isset($elementContainerData['data']['elements'], $elementContainerData['data']['elements'][$i])) {
                $elementsInColumn = $elementContainerData['data']['elements'][$i];
                if (!empty($elementsInColumn)) {
                    $elementsInColumnContextData = $contextData;
                    $elementsInColumnContextData['width'] = '100%';
                    $elementsIDs = [];
                    foreach ($elementsInColumn as $elementInColumnContainerData) {
                        $elementsIDs[] = $elementInColumnContainerData['id'];
                    }
                    $elementsInColumnRawData = self::getElementsRawData($elementsIDs);
                    foreach ($elementsInColumn as $elementInColumnContainerData) {
                        $columnContent .= self::renderElement($elementsInColumnRawData[$elementInColumnContainerData['id']], $editable, $elementsInColumnContextData);
                    }
                }
            }

            $columnWidth = rtrim(rtrim(number_format($columnsSizes[$i] / $totalSize * 100, 3, '.', ''), 0), '.') . '%';
            $columnStyle = 'width:calc(' . $columnWidth . ' - (' . $spacing . '*' . ($columnsCount - 1) . '/' . $columnsCount . '));';
            if ($columnsCount > $i + 1) {
                $columnStyle .= 'margin-right:' . $spacing . ';';
            }
            $content .= '<div style="' . $columnStyle . '">' . $columnContent . '</div>';
        }

        if ($inContainer) {
            $attributes = '';
            $className = 'bre' . md5(uniqid());
            $attributes .= ' class="' . $className . '"';

            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['columns', $elementContainerData['id'], $contextData];
            }

            $attributes .= ' data-srvri="t2 s' . $spacing . '"'; // data-responsive-attributes="w<=500=>data-srvri-vertical=1"

            $styles = '';
            $styles .= '.' . $className . '[data-srvri-vertical="1"]>div{display:block !important;width:100% !important;margin-right:0 !important;}';
            $styles .= '.' . $className . '[data-srvri-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . $spacing . ' !important;}';
            $styles .= '.' . $className . '[data-rvr-editable][data-srvri-vertical="1"]>div:not(:last-child){margin-bottom:' . $spacing . ' !important;}';

            $content = '<html>'
                    . '<head>'
                    . '<script id="bearcms-bearframework-addon-script-1" src="' . htmlentities($context->assets->getUrl('assets/responsiveAttributes.min.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '"></script>'
                    . '<style>' . $styles . '</style>'
                    . '</head>'
                    . '<body>'
                    . '<div' . $attributes . '>' . $content . '</div>'
                    //. '<script>responsiveAttributes.run();</script>'
                    . '</body>'
                    . '</html>';
            return '<component src="data:base64,' . base64_encode($content) . '" />';
        } else {
            return $content;
        }
    }

    /**
     * 
     * @param type $elementContainerData
     * @param type $editable
     * @param type $contextData
     * @param type $inContainer
     * @return type
     */
    static function renderFloatingBox($elementContainerData, $editable, $contextData, $inContainer)
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        $position = $elementContainerData['data']['position'];
        $width = $elementContainerData['data']['width'];
        if (strlen($width) === 0 || $width === 'auto') {
            $width = '100%';
        }
        $spacing = $contextData['spacing'];

        $content = '';

        $getElementsContent = function($location) use ($elementContainerData, $contextData, $editable) {
            $content = '';
            $elements = $elementContainerData['data']['elements'][$location];
            if (!empty($elements)) {
                $elementsContextData = $contextData;
                $elementsContextData['width'] = '100%';
                $elementsIDs = [];
                foreach ($elements as $elementData) {
                    $elementsIDs[] = $elementData['id'];
                }
                $elementsRawData = self::getElementsRawData($elementsIDs);
                foreach ($elements as $elementData) {
                    $content .= self::renderElement($elementsRawData[$elementData['id']], $editable, $elementsContextData);
                }
            }
            return $content;
        };

        $content .= '<div style="margin-' . ($position === 'left' ? 'right' : 'left') . ':' . $spacing . ';float:' . $position . ';width:' . $width . ';">' . $getElementsContent('inside') . '</div>';
        $content .= '<div style="display:block;">' . $getElementsContent('outside') . '</div>';

        if ($inContainer) {
            $attributes = '';
            $className = 'bre' . md5(uniqid());
            $attributes .= ' class="' . $className . '"';

            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
            }

            $attributes .= ' data-srvri="t3 s' . $spacing . '"'; // data-responsive-attributes="w<=500=>data-srvri-vertical=1"

            $styles = '';
            $styles .= '.' . $className . '>div:empty{display:none;}';
            $styles .= '.' . $className . '>div:first-child{max-width:100%;}';
            $styles .= '.' . $className . '[data-rvr-editable]>div:empty{display:block;}';
            $styles .= '.' . $className . '[data-rvr-editable]>div:first-child{min-width:24px;}';
            //$styles .= '.' . $className . '[data-srvri-vertical="1"]>div{display:block !important;max-width:100% !important;width:100% !important;margin-right:0 !important;float:none !important;}';
            //$styles .= '.' . $className . '[data-srvri-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . $spacing . ' !important;}';
            //$styles .= '.' . $className . '[data-rvr-editable][data-srvri-vertical="1"]>div:not(:last-child){margin-bottom:' . $spacing . ' !important;}';
            $styles .= '.' . $className . ':after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';

            $content = '<html>'
                    . '<head>'
                    . '<script id="bearcms-bearframework-addon-script-1" src="' . htmlentities($context->assets->getUrl('assets/responsiveAttributes.min.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '"></script>'
                    . '<style>' . $styles . '</style>'
                    . '</head>'
                    . '<body>'
                    . '<div' . $attributes . '>' . $content . '</div>'
                    . '</body>'
                    . '</html>';
            return '<component src="data:base64,' . base64_encode($content) . '" />';
        } else {
            return $content;
        }
    }

    /**
     * 
     * @param type $elementsIDs
     * @return type
     */
    static function getElementsRawData($elementsIDs)
    {
        $app = App::get();
        $result = [];
        //$commands = [];
        $elementsIDs = array_values($elementsIDs);
        foreach ($elementsIDs as $elementID) {
//            $commands[] = [
//                'command' => 'get',
//                'key' => 'bearcms/elements/element/' . md5($elementID) . '.json',
//                'result' => ['body']
//            ];
            $result[$elementID] = \BearCMS\Internal\Data::getValue('bearcms/elements/element/' . md5($elementID) . '.json');
        }
        //$data = $app->data->execute($commands);
//        foreach ($elementsIDs as $index => $elementID) {
//            if (isset($data[$index]['body'])) {
//                $result[$elementID] = $data[$index]['body'];
//            }
//        }
        return $result;
    }

    static function getContainerData($id)
    {
        $app = App::get();
        $container = \BearCMS\Internal\Data::getValue('bearcms/elements/container/' . md5($id) . '.json');
        $data = $container !== null ? json_decode($container, true) : [];
        if (!isset($data['elements'])) {
            $data['elements'] = [];
        }
        if (!is_array($data['elements'])) {
            throw new Exception('');
        }
        return $data;
    }

    static function getContainerElementsIDs($id)
    {
        $containerData = self::getContainerData($id);
        $result = [];
        foreach ($containerData['elements'] as $elementData) {
            if (isset($elementData['data'], $elementData['data']['type'])) {
                if (($elementData['data']['type'] === 'column' || $elementData['data']['type'] === 'columns') && isset($elementData['data']['elements'])) {
                    foreach ($elementData['data']['elements'] as $columnElements) {
                        foreach ($columnElements as $columnElement) {
                            if (isset($columnElement['id'])) {
                                $result[] = $columnElement['id'];
                            }
                        }
                    }
                    continue;
                } elseif ($elementData['data']['type'] === 'floatingBox' && isset($elementData['data']['elements'])) {
                    if (isset($elementData['data']['elements']['inside'])) {
                        foreach ($elementData['data']['elements']['inside'] as $insideElements) {
                            foreach ($insideElements as $insideElement) {
                                if (isset($insideElement['id'])) {
                                    $result[] = $insideElement['id'];
                                }
                            }
                        }
                    }
                    if (isset($elementData['data']['elements']['outside'])) {
                        foreach ($elementData['data']['elements']['outside'] as $outsideElements) {
                            foreach ($outsideElements as $outsideElement) {
                                if (isset($outsideElement['id'])) {
                                    $result[] = $outsideElement['id'];
                                }
                            }
                        }
                    }
                    continue;
                }
            }
            if (isset($elementData['id'])) {
                $result[] = $elementData['id'];
            }
        }
        return $result;
    }

}
