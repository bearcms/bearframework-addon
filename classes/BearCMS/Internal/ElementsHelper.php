<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsHelper
{

    static $editorData = [];
    static $elementsTypesCodes = [];
    static $elementsTypesFilenames = [];
    static $elementsTypesOptions = [];
    static $lastLoadMoreServerData = null;

    /**
     * 
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return void
     */
    static function updateComponentEditableAttribute(\IvoPetkov\HTMLServerComponent $component): void
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
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return void
     */
    static function updateComponentContextAttributes(\IvoPetkov\HTMLServerComponent $component): void
    {
        $getUpdatedHTMLUnit = function ($value) {
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
            $component->color = Config::$uiColor;
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
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return array
     */
    static function getComponentContextData(\IvoPetkov\HTMLServerComponent $component): array
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
        $attributes = $component->getAttributes();
        foreach ($attributes as $key => $value) {
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
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return void
     * @throws \Exception
     */
    static function updateContainerComponent(\IvoPetkov\HTMLServerComponent $component): void
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
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return void
     */
    static function updateElementComponent(\IvoPetkov\HTMLServerComponent $component): void
    {
        $rawData = $component->getAttribute('bearcms-internal-attribute-raw-data');
        $elementData = null;
        if (strlen($rawData) > 0) {
            $elementData = self::decodeElementRawData($rawData);
            if (is_array($elementData)) {
                $component->id = $elementData['id'];
            }
        } elseif (strlen($component->id) > 0) {
            $elementsRawData = self::getElementsRawData([$component->id]);
            $component->setAttribute('bearcms-internal-attribute-raw-data', $elementsRawData[$component->id]);
        }
        self::updateComponentEditableAttribute($component);
        self::updateComponentContextAttributes($component);
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
     * @param string $rawData
     * @param bool $editable
     * @param array $contextData
     * @param string $outputType
     * @return string
     * @throws \Exception
     */
    static function renderElement(string $rawData, bool $editable, array $contextData, string $outputType = 'full-html'): string
    {
        $elementData = self::decodeElementRawData($rawData);
        if (!is_array($elementData)) {
            return '';
        }
        $componentName = array_search($elementData['type'], self::$elementsTypesCodes);
        return '<component'
            . ' src="' . ($componentName === false ? 'bearcms-missing-element' : $componentName) . '"'
            . ' editable="' . ($editable ? 'true' : 'false') . '"'
            . ' bearcms-internal-attribute-raw-data="' . htmlentities($rawData) . '"'
            . ' bearcms-internal-attribute-in-elements-container="' . ((int) $contextData['inElementsContainer'] === 1 ? 'true' : 'false') . '"'
            . ' width="' . $contextData['width'] . '"'
            . ' spacing="' . $contextData['spacing'] . '"'
            . ' color="' . $contextData['color'] . '"'
            . ' output-type="' . $outputType . '"'
            . '/>';
    }

    /**
     * 
     * @param array $elementContainerData
     * @param bool $editable
     * @param array $contextData
     * @param bool $inContainer
     * @param string $outputType
     * @return string
     */
    static function renderColumn(array $elementContainerData, bool $editable, array $contextData, bool $inContainer, string $outputType = 'full-html'): string
    {
        $app = App::get();
        $context = $app->contexts->get(__FILE__);
        $columnsSizes = explode(':', $elementContainerData['data']['mode']);
        $responsive = isset($elementContainerData['data']['responsive']) ? (int) $elementContainerData['data']['responsive'] > 0 : false;
        $columnsCount = sizeof($columnsSizes);
        $totalSize = array_sum($columnsSizes);
        $spacing = $contextData['spacing'];

        $innerContent = '';
        $columnStyles = [];
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
                        $elementInColumnRawData = $elementsInColumnRawData[$elementInColumnContainerData['id']];
                        if ($elementInColumnRawData !== null) {
                            $columnContent .= self::renderElement($elementInColumnRawData, $editable, $elementsInColumnContextData, $outputType);
                        }
                    }
                }
            }

            $columnWidth = rtrim(rtrim(number_format($columnsSizes[$i] / $totalSize * 100, 3, '.', ''), 0), '.') . '%';
            $columnStyle = 'flex:' . $columnsSizes[$i] . ' 0 auto;max-width:calc(' . $columnWidth . ' - (' . $spacing . '*' . ($columnsCount - 1) . '/' . $columnsCount . '));margin-right:' . ($columnsCount > $i + 1 ? $spacing : '0') . ';';
            $columnStyles[$i] = $columnStyle;
            if ($outputType === 'full-html') {
                $innerContent .= '<div class="bearcms-elements-columns-column">' . $columnContent . '</div>';
            } elseif ($outputType === 'simple-html') {
                $innerContent .= '<div>' . $columnContent . '</div>';
            }
        }

        $className = 'bre' . md5('column$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

        $styles = '';
        $styles .= '.' . $className . '[data-srvri~="t2"]{display:flex;}';
        foreach ($columnStyles as $index => $columnStyle) {
            $styles .= '.' . $className . '>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
        }
        if ($responsive) {
            $styles .= '.' . $className . '[data-srvri-rows="1"]{flex-direction:column;}';
            foreach ($columnStyles as $index => $columnStyle) {
                $styles .= '.' . $className . '[data-srvri-rows="1"]>div:nth-child(' . ($index + 1) . '){width:100%;max-width:100%;margin-right:0;}';
            }
            $styles .= '.' . $className . '[data-srvri-rows="1"]>div:not(:empty):not(:last-child){margin-bottom:' . $spacing . ';}';
            $styles .= '.' . $className . '[data-rvr-editable][data-srvri-rows="1"]>div:not(:last-child){margin-bottom:' . $spacing . ';}';
        } else {
            $styles .= '.' . $className . '[data-srvri-rows="1"]{flex-direction:row;}';
            foreach ($columnStyles as $index => $columnStyle) {
                $styles .= '.' . $className . '[data-srvri-rows="1"]>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
            }
            $styles .= '.' . $className . '[data-srvri-rows="1"]>div:not(:empty):not(:last-child){margin-bottom:0;}';
            $styles .= '.' . $className . '[data-rvr-editable][data-srvri-rows="1"]>div:not(:last-child){margin-bottom:0;}';
        }

        $attributes = '';
        if ($inContainer) {
            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['columns', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-columns ' . $className . '"';
                $attributes .= ' data-srvri="t2 s' . $spacing . '"';
                if ($responsive || $editable) {
                    $attributes .= ' data-responsive-attributes="w<=500=>data-srvri-rows=1"';
                }
            }
        }
        $content = '<html>';
        if ($outputType === 'full-html') {
            $content .= '<head>'
                . ($inContainer && ($responsive || $editable) ? '<link rel="client-packages-embed" name="-bearcms-responsive-attributes">' : '')
                . '<style>' . $styles . '</style>'
                . '</head>';
        }
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . '</body>'
            . '</html>';
        return '<component src="data:base64,' . base64_encode($content) . '" />';
    }

    /**
     * 
     * @param array $elementContainerData
     * @param bool $editable
     * @param array $contextData
     * @param bool $inContainer
     * @param string $outputType
     * @return string
     */
    static function renderFloatingBox(array $elementContainerData, bool $editable, array $contextData, bool $inContainer, string $outputType = 'full-html'): string
    {
        $app = App::get();
        $context = $app->contexts->get(__FILE__);
        $position = $elementContainerData['data']['position'];
        $width = $elementContainerData['data']['width'];
        if (strlen($width) === 0 || $width === 'auto') {
            $width = '100%';
        }
        $responsive = isset($elementContainerData['data']['responsive']) ? (int) $elementContainerData['data']['responsive'] > 0 : false;
        $spacing = $contextData['spacing'];

        if (substr($width, -1) === '%' && $width !== '100%') {
            $width = 'calc(' . $width . ' - ' . $spacing . '/2)';
        }

        $getElementsContent = function ($location) use ($elementContainerData, $contextData, $editable, $outputType) {
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
                    $elementRawData = $elementsRawData[$elementData['id']];
                    if ($elementRawData !== null) {
                        $content .= self::renderElement($elementRawData, $editable, $elementsContextData, $outputType);
                    }
                }
            }
            return $content;
        };

        $innerContent = '';
        if ($outputType === 'full-html') {
            $innerContent .= '<div class="bearcms-elements-floating-box-inside">' . $getElementsContent('inside') . '</div>';
            $innerContent .= '<div class="bearcms-elements-floating-box-outside">' . $getElementsContent('outside') . '</div>';
        } elseif ($outputType === 'simple-html') {
            $innerContent .= '<div>' . $getElementsContent('inside') . '</div>';
            $innerContent .= '<div>' . $getElementsContent('outside') . '</div>';
        }

        $className = 'bre' . md5('floatingbox$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

        $styles = '';
        $styles .= '.' . $className . '>div:empty{display:none;}';
        $styles .= '.' . $className . '>div:first-child{max-width:100%;' . ($position === 'left' ? 'margin-right:' . $spacing . ';margin-left:0;' : 'margin-left:' . $spacing . ';margin-right:0;') . 'float:' . $position . ';width:' . $width . ';}';
        $styles .= '.' . $className . '>div:last-child{display:block;}';
        $styles .= '.' . $className . '[data-rvr-editable]>div:empty{display:block;}';
        $styles .= '.' . $className . '[data-rvr-editable]>div:first-child{min-width:24px;}';
        $styles .= '.' . $className . '[data-srvri-rows="1"]>div{display:block;max-width:100%;width:100%;margin-' . ($position === 'left' ? 'right' : 'left') . ':0;float:none;}';
        $styles .= '.' . $className . '[data-srvri-rows="1"]>div:not(:empty):not(:last-child){margin-bottom:' . $spacing . ';}';
        $styles .= '.' . $className . '[data-rvr-editable][data-srvri-rows="1"]>div:not(:last-child){margin-bottom:' . $spacing . ';}';
        $styles .= '.' . $className . ':after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';

        if ($inContainer) {
            $attributes = '';
            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-floating-box ' . $className . '"';
                $attributes .= ' data-srvri="t3 s' . $spacing . '"';
                $attributes .= ' data-responsive-attributes="f(cmsefbr' . $className . ')=>data-srvri-rows=1"';
            }
        }
        $content = '<html>';
        if ($outputType === 'full-html') {
            $content .= '<head>'
                . '<style>' . $styles . '</style>'
                . '<script>cmsefbr' . $className . '=function(e,d){' // element, details
                . ($responsive ? 'if(d.width<=500){return true}' : '')
                . 'e.firstChild.style.setProperty("width","' . $width . '");'
                . 'var w=e.firstChild.getBoundingClientRect().width;'
                . 'e.firstChild.style.removeProperty("width");'
                . 'if(w===d.width){return true}' // if first children width is equal to containwe width then make vertical
                . 'return false'
                . '};</script>'
                . ($inContainer ? '<link rel="client-packages-embed" name="-bearcms-responsive-attributes">' : '')
                . '</head>';
        }
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . '</body>'
            . '</html>';
        return '<component src="data:base64,' . base64_encode($content) . '" />';
    }

    /**
     * 
     * @param array $elementsIDs
     * @return array
     */
    static function getElementsRawData(array $elementsIDs): array
    {
        $result = [];
        $elementsIDs = array_values($elementsIDs);
        foreach ($elementsIDs as $elementID) {
            $result[$elementID] = Internal\Data::getValue('bearcms/elements/element/' . md5($elementID) . '.json');
        }
        return $result;
    }

    /**
     * 
     * @param string $id
     * @return array
     * @throws Exception
     */
    static function getContainerData(string $id): array
    {
        $container = Internal\Data::getValue('bearcms/elements/container/' . md5($id) . '.json');
        $data = $container !== null ? json_decode($container, true) : [];
        if (!isset($data['elements'])) {
            $data['elements'] = [];
        }
        if (!is_array($data['elements'])) {
            throw new Exception('');
        }
        return $data;
    }

    /**
     * 
     * @param string $id
     * @return array
     */
    static function getContainerElementsIDs(string $id): array
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
                        foreach ($elementData['data']['elements']['inside'] as $insideElement) {
                            if (isset($insideElement['id'])) {
                                $result[] = $insideElement['id'];
                            }
                        }
                    }
                    if (isset($elementData['data']['elements']['outside'])) {
                        foreach ($elementData['data']['elements']['outside'] as $outsideElement) {
                            if (isset($outsideElement['id'])) {
                                $result[] = $outsideElement['id'];
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

    /**
     * 
     * @return string
     */
    static function getEditableElementsHtml(): string
    {
        $app = App::get();
        $html = '';
        if ((Config::hasFeature('ELEMENTS') || Config::hasFeature('ELEMENTS_*')) && !empty(self::$editorData)) {
            $requestArguments = [];
            $requestArguments['data'] = json_encode(self::$editorData);
            $cacheKey = json_encode([
                'elementsEditor',
                $app->request->base,
                $app->bearCMS->currentUser->getSessionKey(),
                $app->bearCMS->currentUser->getPermissions(),
                get_class_vars('\BearCMS\Internal\Config'),
                Cookies::getList(Cookies::TYPE_SERVER)
            ]);
            $elementsEditorData = Server::call('elementseditor', $requestArguments, true, $cacheKey);
            if (is_array($elementsEditorData) && isset($elementsEditorData['result']) && is_array($elementsEditorData['result']) && isset($elementsEditorData['result']['content'])) {
                $html = $elementsEditorData['result']['content'];
                $html = Server::updateAssetsUrls($html, false);
            }
        }
        return $html;
    }
}
