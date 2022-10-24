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
use BearCMS\Internal\Data\Elements as InternalDataElements;
use BearCMS\Internal\Data\UploadsSize;

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
     * @return array
     */
    static function getComponentContextData(\IvoPetkov\HTMLServerComponent $component): array
    {
        $result = [];
        $result['width'] = $component->width;
        $result['spacing'] = $component->spacing;
        $result['color'] = $component->color;
        $canEdit = $component->canEdit;
        if ($canEdit !== null) {
            $result['canEdit'] = $canEdit;
        }
        $canDuplicate = $component->canDuplicate;
        if ($canDuplicate !== null) {
            $result['canDuplicate'] = $canDuplicate;
        }
        $canStyle = $component->canStyle;
        if ($canStyle !== null) {
            $result['canStyle'] = $canStyle;
        }
        $canMove = $component->canMove;
        if ($canMove !== null) {
            $result['canMove'] = $canMove;
        }
        $canDelete = $component->canDelete;
        if ($canDelete !== null) {
            $result['canDelete'] = $canDelete;
        }

        $otherAttributes = [];
        $attributesToSkip = ['src', 'id', 'editable', 'width', 'spacing', 'color', 'group', 'canEdit', 'canDuplicate', 'canStyle', 'canMove', 'canDelete'];
        $attributes = $component->getAttributes();
        foreach ($attributes as $key => $value) {
            $add = true;
            if (array_search($key, $attributesToSkip) !== false || strpos($key, 'bearcms-internal-attribute-') === 0) {
                $add = false;
            }
            if ($add && isset(self::$elementsTypesOptions[$component->src])) {
                $options = self::$elementsTypesOptions[$component->src];
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
     * @param string $rawData
     * @param bool $editable
     * @param array $contextData
     * @param string $outputType
     * @return string
     * @throws \Exception
     */
    static function renderElement(string $rawData, bool $editable, array $contextData, string $outputType = 'full-html'): string
    {
        $elementData = InternalDataElements::decodeElementRawData($rawData);
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
            . ($editable && isset($contextData['canEdit']) ? ' canEdit="' . $contextData['canEdit'] . '"' : '')
            . ($editable && isset($contextData['canDuplicate']) ? ' canDuplicate="' . $contextData['canDuplicate'] . '"' : '')
            . (isset($contextData['canStyle']) ? ' canStyle="' . $contextData['canStyle'] . '"' : '')
            . ($editable && isset($contextData['canMove']) ? ' canMove="' . $contextData['canMove'] . '"' : '')
            . ($editable && isset($contextData['canDelete']) ? ' canEdit="' . $contextData['canDelete'] . '"' : '')
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
    static function renderColumns(array $elementContainerData, bool $editable, array $contextData, bool $inContainer, string $outputType = 'full-html'): string
    {
        $elementContainerData = self::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $spacing = $contextData['spacing'];
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $widths = isset($elementStyle['widths']) ? $elementStyle['widths'] : ','; //50%,50%
        $autoVerticalWidth = isset($elementStyle['autoVerticalWidth']) ? $elementStyle['autoVerticalWidth'] : '500px';
        $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;

        $spacingStyleRule = '';
        if (isset($elementStyle['elementsSpacing']) && strlen($elementStyle['elementsSpacing']) > 0) {
            if ($editable) {
                $spacingStyleRule = '--bearcms-elements-spacing:' . $elementStyle['elementsSpacing'] . ';';
            }
            $spacing = $elementStyle['elementsSpacing'];
        }

        $columnsWidths = explode(',', $widths);
        $columnsCount = sizeof($columnsWidths);

        $innerContent = '';

        for ($i = 0; $i < $columnsCount; $i++) {
            $columnContent = '';
            if (isset($elementContainerData['elements'], $elementContainerData['elements'][$i])) {
                $elementsInColumnContainerData = $elementContainerData['elements'][$i];
                if (!empty($elementsInColumnContainerData)) {
                    $elementsContextData = $contextData;
                    $elementsContextData['width'] = '100%';
                    $elementsContextData['spacing'] = $editable ? 'var(--bearcms-elements-spacing)' : $spacing;
                    $columnContent .= self::renderContainerElements($elementsInColumnContainerData, $editable, $elementsContextData, $outputType);
                }
            }
            if ($outputType === 'full-html') {
                $innerContent .= '<div class="bearcms-elements-columns-column" style="' . $spacingStyleRule . '">' . $columnContent . '</div>'; // $spacingStyleRule must be here to prevent overwriting it in the elementsEditor.js
            } else {
                $innerContent .= '<div>' . $columnContent . '</div>';
            }
        }

        $styles = '';
        if ($inContainer) {
            $attributes = '';
            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                self::$editorData[] = ['columns', $elementContainerData['id'], $contextData];
                $attributes .= ' data-columns-elements-editor-widths="' . $widths . '"';
            }
            if ($outputType === 'full-html') {

                $columnsStyles = [];

                $notEmptyColumnsWidthsCalc = [];
                $emptyColumnsWidths = 0;
                for ($i = 0; $i < $columnsCount; $i++) {
                    if (strlen($columnsWidths[$i]) === 0) {
                        $emptyColumnsWidths++;
                    } else {
                        $notEmptyColumnsWidthsCalc[] = $columnsWidths[$i];
                    }
                }
                $notEmptyColumnsWidthsCalc = implode(' + ', $notEmptyColumnsWidthsCalc);

                for ($i = 0; $i < $columnsCount; $i++) {
                    $columnWidth = $columnsWidths[$i];
                    $isFixedWidth = strpos($columnWidth, 'px') !== false;
                    if (strlen($columnWidth) === 0) {
                        $columnWidth = (strlen($notEmptyColumnsWidthsCalc) === 0 ? '100%' : '(100% - (' . $notEmptyColumnsWidthsCalc . '))') . '/' . $emptyColumnsWidths;
                    }
                    $columnsStyles[$i] = 'min-width:15px;' . ($isFixedWidth ? 'flex:0 0 auto;width:' . $columnWidth : 'flex:1 0 auto;max-width:calc(' . $columnWidth . ' - (' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . '*' . ($columnsCount - 1) . '/' . $columnsCount . '))') . ';margin-right:' . ($columnsCount > $i + 1 ? ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) : '0') . ';';
                }

                $className = 'bre' . md5('columns$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

                $styles .= '.' . $className . '{display:flex !important;flex-direction:row;}';
                $styles .= '.' . $className . '>div>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                foreach ($columnsStyles as $index => $columnStyle) {
                    $styles .= '.' . $className . '>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
                }
                $styles .= '.' . $className . '[data-columns-auto-vertical="1"]{flex-direction:column;}';
                foreach ($columnsStyles as $index => $columnStyle) {
                    $styles .= '.' . $className . '[data-columns-auto-vertical="1"]>div:nth-child(' . ($index + 1) . '){flex:1 0 auto;width:100%;max-width:100%;margin-right:0;}';
                }
                $styles .= '.' . $className . '[data-columns-auto-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-rvr-editable][data-columns-auto-vertical="1"]>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';

                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-columns ' . $className . '"';
                if ($autoVerticalWidthInPx !== null) {
                    $attributes .= ' data-responsive-attributes="w<=' . $autoVerticalWidthInPx . '=>data-columns-auto-vertical=1"';
                }
            }
        }
        $content = '<html>';
        if ($outputType === 'full-html' && $inContainer) {
            $content .= '<head>'
                . '<style>' . $styles . '</style>'
                . (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="responsiveAttributes">' : '')
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
        $elementContainerData = self::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $spacing = $contextData['spacing'];
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $position = isset($elementStyle['position']) ? $elementStyle['position'] : 'left';
        if ($position !== 'left') {
            $position = 'right';
        }
        $width = isset($elementStyle['width']) ? $elementStyle['width'] : '50%';
        $autoVerticalWidth = isset($elementStyle['autoVerticalWidth']) ? $elementStyle['autoVerticalWidth'] : '500px';
        $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;

        $spacingStyleRule = '';
        if (isset($elementStyle['elementsSpacing']) && strlen($elementStyle['elementsSpacing']) > 0) {
            if ($editable) {
                $spacingStyleRule = '--bearcms-elements-spacing:' . $elementStyle['elementsSpacing'] . ';';
            }
            $spacing = $elementStyle['elementsSpacing'];
        }

        $getElementsContent = function ($location) use ($elementContainerData, $contextData, $editable, $outputType, $spacing) {
            $content = '';
            $elementsContainerData = isset($elementContainerData['elements'][$location]) ? $elementContainerData['elements'][$location] : [];
            if (!empty($elementsContainerData)) {
                $elementsContextData = $contextData;
                $elementsContextData['width'] = '100%';
                $elementsContextData['spacing'] = $editable ? 'var(--bearcms-elements-spacing)' : $spacing;
                $content .= self::renderContainerElements($elementsContainerData, $editable, $elementsContextData, $outputType);
            }
            return $content;
        };

        $innerContent = '';
        if ($outputType === 'full-html') {
            $innerContent .= '<div class="bearcms-elements-floating-box-inside">' . $getElementsContent('inside') . '</div>';
            $innerContent .= '<div class="bearcms-elements-floating-box-outside">' . $getElementsContent('outside') . '</div>';
        } else {
            $innerContent .= '<div>' . $getElementsContent('inside') . '</div>';
            $innerContent .= '<div>' . $getElementsContent('outside') . '</div>';
        }

        $responsiveFunctionName = 'cmsefbr' . md5((isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

        $styles = '';
        if ($inContainer) {
            $attributes = '';
            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                self::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $className = 'bre' . md5('floatingbox$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

                $styles .= '.' . $className . '{--bearcms-floating-box-width:' . (substr($width, -1) === '%' && $width !== '100%' ? 'calc(' . $width . ' - ' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . '/2)' : $width) . ';}';
                $styles .= '.' . $className . '>div{' . (strlen($spacingStyleRule) > 0 ? $spacingStyleRule : '') . '}';
                $styles .= '.' . $className . '>div:first-child{max-width:100%;}';
                $styles .= '.' . $className . '[data-floating-box-position="left"]:not([data-floating-box-auto-vertical="1"]):not([data-floating-box-vertical="1"])>div:first-child{width:var(--bearcms-floating-box-width);float:left;margin-right:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';margin-left:0;}';
                $styles .= '.' . $className . '[data-floating-box-position="right"]:not([data-floating-box-auto-vertical="1"]):not([data-floating-box-vertical="1"])>div:first-child{width:var(--bearcms-floating-box-width);float:right;margin-left:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';margin-right:0;}';
                $styles .= '.' . $className . '>div:last-child{display:block;}';
                $styles .= '.' . $className . '[data-rvr-editable]>div:first-child{min-width:15px;}';
                $styles .= '.' . $className . '>div>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-floating-box-auto-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-floating-box-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-rvr-editable][data-floating-box-auto-vertical="1"]>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-rvr-editable][data-floating-box-vertical="1"]>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . ':after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';

                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-floating-box ' . $className . '"';
                $attributes .= ' data-floating-box-position="' . $position . '"';
                $attributes .= ' data-responsive-attributes="' . ($autoVerticalWidthInPx !== null ? 'w<=' . $autoVerticalWidthInPx . '=>data-floating-box-auto-vertical=1,' : '') . 'f(' . $responsiveFunctionName . ')=>data-floating-box-vertical=1"';
            }
        }
        $content = '<html>';
        if ($outputType === 'full-html') {
            $content .= '<head>';
            $content .= '<style>' . $styles . '</style>';
            $content .= '<script>' . $responsiveFunctionName . '=function(e,d){' .
                'e.removeAttribute("data-floating-box-vertical");' .
                'var w=e.firstChild.getBoundingClientRect().width;' .
                'return w>=d.width;' .
                '};</script>'; // element, details
            $content .= (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="responsiveAttributes">' : '');
            $content .= '</head>';
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
    static function renderFlexibleBox(array $elementContainerData, bool $editable, array $contextData, bool $inContainer, string $outputType = 'full-html'): string
    {
        $elementContainerData = self::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $spacing = $contextData['spacing'];
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $autoVerticalWidth = isset($elementStyle['autoVerticalWidth']) ? $elementStyle['autoVerticalWidth'] : '500px';
        $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;

        $spacingStyleRule = '';
        if (isset($elementStyle['elementsSpacing']) && strlen($elementStyle['elementsSpacing']) > 0) {
            if ($editable) {
                $spacingStyleRule = '--bearcms-elements-spacing:' . $elementStyle['elementsSpacing'] . ';';
            }
            $spacing = $elementStyle['elementsSpacing'];
        }

        $direction = isset($elementStyle['direction']) ? $elementStyle['direction'] : 'column';
        if ($direction !== 'column') {
            $direction = 'row';
        }

        $rowAlignment = isset($elementStyle['rowAlignment']) ? $elementStyle['rowAlignment'] : 'left';

        $innerContent = '<div>';
        $elementsContainerData = $elementContainerData['elements'];
        if (!empty($elementsContainerData)) {
            $elementsContextData = $contextData;
            $elementsContextData['width'] = '100%';
            $elementsContextData['spacing'] = $editable ? 'var(--bearcms-elements-spacing)' : $spacing;
            $innerContent .= self::renderContainerElements($elementsContainerData, $editable, $elementsContextData, $outputType);
        }
        $innerContent .= '</div>';

        $styles = '';
        if ($inContainer) {

            $attributes = '';

            $hasElementStyle = !empty($elementStyle) && isset($contextData['canStyle']) && $contextData['canStyle'] === 'true';

            if ($editable || $hasElementStyle) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                if ($editable) {
                    self::$editorData[] = ['flexibleBox', $elementContainerData['id'], $contextData];
                }
            }

            if ($outputType === 'full-html') {
                $className = 'bre' . md5('flexiblebox$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));
                $classAttributeValue = 'bearcms-elements-element-container bearcms-elements-flexible-box ' . $className;

                if ($hasElementStyle) {
                    $styleClassName = 'bearcms-elements-element-style-' . md5($elementContainerData['id']);
                    $classAttributeValue .= ' ' . $styleClassName;
                    if (isset($elementStyle['css'])) {
                        $innerContent .= self::getElementStyleHTML('flexibleBox', $elementStyle, '#' . $htmlElementID . '.' . $styleClassName);
                    }
                }

                $styles .= '.' . $className . '>div{' . (strlen($spacingStyleRule) > 0 ? $spacingStyleRule : '') . ';}';
                $styles .= '.' . $className . '[data-flexible-box-direction="row"]:not([data-flexible-box-auto-vertical="1"])[data-flexible-box-row-alignment="center"]>div{justify-content:center;}';
                $styles .= '.' . $className . '[data-flexible-box-direction="row"]:not([data-flexible-box-auto-vertical="1"])[data-flexible-box-row-alignment="right"]>div{justify-content:right;}';
                $styles .= '.' . $className . '>div>div:not(:last-child){margin-bottom:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';
                $styles .= '.' . $className . '[data-flexible-box-direction="row"]:not([data-flexible-box-auto-vertical="1"])>div{display:flex;flex-direction:row;flex-wrap:wrap;align-items:flex-start;}';
                $styles .= '.' . $className . '[data-flexible-box-direction="row"]:not([data-flexible-box-auto-vertical="1"])>div>div{min-width:15px;}';
                $styles .= '.' . $className . '[data-flexible-box-direction="row"]:not([data-flexible-box-auto-vertical="1"])>div>div:not(:last-child){margin-bottom:0;margin-right:' . ($editable ? 'var(--bearcms-elements-spacing)' : $spacing) . ';}';

                $attributes .= ' data-flexible-box-direction="' . $direction . '"';
                $attributes .= ' data-flexible-box-row-alignment="' . $rowAlignment . '"';

                $attributes .= ' class="' . $classAttributeValue . '"';
                if ($autoVerticalWidthInPx !== null) {
                    $attributes .= ' data-responsive-attributes="w<=' . $autoVerticalWidthInPx . '=>data-flexible-box-auto-vertical=1"';
                }
            }
        }
        $content = '<html>';
        if ($outputType === 'full-html' && $inContainer) {
            $content .= '<head>'
                . '<style>' . $styles . '</style>'
                . (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="responsiveAttributes">' : '')
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
     * @param array $elementsContainerData
     * @param boolean $editable
     * @param array $contextData
     * @param string $outputType
     * @return string
     */
    static function renderContainerElements(array $elementsContainerData, bool $editable, array $contextData, string $outputType = 'full-html'): string
    {
        $content = '';
        $elementsIDs = [];

        foreach ($elementsContainerData as $elementContainerData) {
            if (!self::isStructuralElementContainerData($elementContainerData)) {
                $elementsIDs[] = $elementContainerData['id'];
            }
        }
        $elementsRawData = InternalDataElements::getElementsRawData($elementsIDs);
        foreach ($elementsContainerData as $elementContainerData) {
            if (self::isColumnsElementContainerData($elementContainerData)) {
                $content .= self::renderColumns($elementContainerData, $editable, $contextData, true, $outputType);
            } elseif (self::isFloatingBoxElementContainerData($elementContainerData)) {
                $content .= self::renderFloatingBox($elementContainerData, $editable, $contextData, true, $outputType);
            } elseif (self::isFlexibleBoxElementContainerData($elementContainerData)) {
                $content .= self::renderFlexibleBox($elementContainerData, $editable, $contextData, true, $outputType);
            } else {
                $elementRawData = $elementsRawData[$elementContainerData['id']];
                if ($elementRawData !== null) {
                    $content .= self::renderElement($elementRawData, $editable, $contextData, $outputType);
                }
            }
        }
        return $content;
    }

    /**
     * 
     * @param array $elementContainerData
     * @return boolean
     */
    static private function isColumnsElementContainerData(array $elementContainerData): bool
    {
        if (isset($elementContainerData['type']) && $elementContainerData['type'] === 'columns') {
            return true;
        }
        return isset($elementContainerData['data'], $elementContainerData['data']['type']) && ($elementContainerData['data']['type'] === 'columns' || $elementContainerData['data']['type'] === 'column');
    }

    /**
     * 
     * @param array $elementContainerData
     * @return boolean
     */
    static private function isFloatingBoxElementContainerData(array $elementContainerData): bool
    {
        if (isset($elementContainerData['type']) && $elementContainerData['type'] === 'floatingBox') {
            return true;
        }
        return isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'floatingBox';
    }

    /**
     * 
     * @param array $elementContainerData
     * @return boolean
     */
    static private function isFlexibleBoxElementContainerData(array $elementContainerData): bool
    {
        if (isset($elementContainerData['type']) && $elementContainerData['type'] === 'flexibleBox') {
            return true;
        }
        return isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'flexibleBox';
    }

    /**
     * 
     * @param array $elementContainerData
     * @return boolean
     */
    static private function isStructuralElementContainerData(array $elementContainerData): bool
    {
        if (self::isColumnsElementContainerData($elementContainerData)) {
            return true;
        }
        if (self::isFloatingBoxElementContainerData($elementContainerData)) {
            return true;
        }
        if (self::isFlexibleBoxElementContainerData($elementContainerData)) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $containerID
     * @param string $elementID
     * @return array
     */
    static function getElementStyleOptions(string $containerID, string $elementID): ?array
    {
        $elementType = null;
        $elementData = InternalDataElements::getElement($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
            $htmlElementID = 'brelc' . md5($elementData['id']);
        }
        if ($elementType === null) {
            $containerData = InternalDataElements::getContainer($containerID);
            $elementData = self::getStructuralElement($containerData, $elementID);
            if (is_array($elementData)) {
                $elementType = $elementData['type'];
                $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
                $htmlElementID = 'brelb' . md5($elementData['id']);
            }
        }
        if ($elementType !== null) {
            if (isset(Internal\Themes::$elementsOptions[$elementType])) {
                Localization::setAdminLocale();
                if ($elementType === 'flexibleBox') {
                    $themeID = null;
                    $themeOptionsSelectors = null;
                } else {
                    $themeID = Internal\Themes::getActiveThemeID();
                    $themeOptionsSelectors = Internal\Themes::getElementsOptionsSelectors($themeID, $elementType);
                }
                $options = new \BearCMS\Themes\Theme\Options();
                $callback = Internal\Themes::$elementsOptions[$elementType];
                if (is_array($callback)) {
                    $callback = $callback[1];
                }
                call_user_func($callback, $options, 'ElementStyle', '#' . $htmlElementID . '.bearcms-elements-element-style-' . md5($elementID), Internal\Themes::OPTIONS_CONTEXT_ELEMENT, []);
                $values = [];
                foreach ($elementStyle as $name => $value) {
                    $values['ElementStyle' . $name] = $value;
                }
                Localization::restoreLocale();
                return [$options, $values, $themeID, $themeOptionsSelectors, $elementType];
            }
        }
        return null;
    }

    /**
     * 
     * @param string $containerID
     * @param string $elementID
     * @param array $values
     * @return void
     */
    static function setElementStyleValues(string $containerID, string $elementID, array $values)
    {
        $app = App::get();
        $elementType = null;
        $isStructural = false;
        $elementData = InternalDataElements::getElement($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $oldElementStyle = isset($elementData['style']) ? $elementData['style'] : [];
        }
        if ($elementType === null) {
            $containerData = InternalDataElements::getContainer($containerID);
            $elementData = self::getStructuralElement($containerData, $elementID);
            if (is_array($elementData)) {
                $elementType = $elementData['type'];
                $oldElementStyle = isset($elementData['style']) ? $elementData['style'] : [];
                $isStructural = true;
            }
        }
        if ($elementType !== null) {
            $filesInOldStyle = Internal\Themes::getFilesInValues($oldElementStyle);
            $filesToDelete = [];
            $newElementStyle = [];
            foreach ($values as $key => $value) {
                $value = trim($value);
                if (strpos($key, 'ElementStyle') === 0 && strlen($value) > 0) {
                    $optionKey = substr($key, strlen('ElementStyle'));
                    if (!isset($newElementStyle[$optionKey]) || $newElementStyle[$optionKey] !== $value) {
                        $newElementStyle[$optionKey] = $value;
                    }
                }
            }
            $filesInNewStyle = Internal\Themes::getFilesInValues($newElementStyle, true);
            $filesToUpdate = [];
            $duplicatedDataKeys = [];
            $filesToKeep = [];
            foreach ($filesInNewStyle as $filename) {
                $filenameOptions = Internal\Data::getFilenameOptions($filename);
                $filenameWithoutOptions = Internal\Data::removeFilenameOptions($filename);
                $dataKey = Internal\Data::getFilenameDataKey($filenameWithoutOptions);
                if ($dataKey !== null && strpos($dataKey, '.temp/bearcms/files/elementstyleimage/') === 0) {
                    $newDataKey = 'bearcms/files/elementstyleimage/' . pathinfo($dataKey, PATHINFO_BASENAME);
                    if (!isset($duplicatedDataKeys[$dataKey])) {
                        $app->data->duplicate($dataKey, $newDataKey);
                        UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        $duplicatedDataKeys[$dataKey] = true;
                    }
                    $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                    $filesToDelete[] = $filenameWithoutOptions;
                } else {
                    $filesToKeep[] = $filenameWithoutOptions;
                }
            }
            $filesToDelete = array_merge($filesToDelete, array_diff($filesInOldStyle, $filesToKeep));
            $newElementStyle = Internal\Themes::updateFilesInValues($newElementStyle, $filesToUpdate);
            if ($isStructural) {

                if ($elementType === 'columns') { // Move elements to the last column if theirs is removed
                    $oldColumnsCount = isset($oldElementStyle['widths']) ? sizeof(explode(',', $oldElementStyle['widths'])) : 2; // default is 50%,50%
                    $newColumnsCount = isset($newElementStyle['widths']) ? sizeof(explode(',', $newElementStyle['widths'])) : 2; // default is 50%,50%
                    if ($newColumnsCount < $oldColumnsCount) {
                        if (!isset($elementData['elements'])) {
                            $elementData['elements'] = [];
                        }
                        if (!isset($elementData['elements'][$newColumnsCount - 1])) {
                            $elementData['elements'][$newColumnsCount - 1] = [];
                        }
                        for ($i = $newColumnsCount; $i < $oldColumnsCount; $i++) {
                            if (isset($elementData['elements'][$i])) {
                                $elementData['elements'][$newColumnsCount - 1] = array_merge($elementData['elements'][$newColumnsCount - 1], $elementData['elements'][$i]);
                                unset($elementData['elements'][$i]);
                            }
                        }
                    }
                }

                $elementData['style'] = $newElementStyle;
                if (empty($elementData['style'])) {
                    unset($elementData['style']);
                }

                $containerData = self::setStructuralElement($containerData, $elementData);
                InternalDataElements::setContainer($containerID, $containerData);
                InternalDataElements::dispatchContainerChangeEvent($containerID);
            } else {
                $elementData['style'] = $newElementStyle;
                if (empty($elementData['style'])) {
                    unset($elementData['style']);
                }
                InternalDataElements::setElement($elementID, $elementData, $containerID);
                InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
            }
            self::deleteElementStyleFiles($filesToDelete);
        }
    }

    /**
     * 
     * @param string $elementType
     * @param array $elementStyleData
     * @param string $cssSelector
     * @return string
     */
    static function getElementStyleHTML(string $elementType, array $elementStyleData, string $cssSelector): string
    {
        if (isset(Internal\Themes::$elementsOptions[$elementType])) {
            $options = new \BearCMS\Themes\Theme\Options();
            $callback = Internal\Themes::$elementsOptions[$elementType];
            if (is_array($callback)) {
                $callback = $callback[1];
            }
            call_user_func($callback, $options, '', $cssSelector, Internal\Themes::OPTIONS_CONTEXT_ELEMENT, []);
            $options->setValues($elementStyleData);
            $htmlData = Internal\Themes::getOptionsHTMLData($options->getList());
            $html = Internal\Themes::processOptionsHTMLData($htmlData);
            return '<component src="data:base64,' . base64_encode($html) . '" />';
        }
        return '';
    }

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     * @return void
     */
    static function deleteElement(string $elementID, string $containerID = null): void
    {
        $elementData = InternalDataElements::getElement($elementID);
        if ($elementData !== null) {
            InternalDataElements::deleteElement($elementID, $containerID);
            if (isset($elementData['type'])) {
                $componentName = array_search($elementData['type'], self::$elementsTypesCodes);
                if ($componentName !== false) {
                    $options = self::$elementsTypesOptions[$componentName];
                    if (isset($options['onDelete']) && is_callable($options['onDelete'])) {
                        call_user_func($options['onDelete'], isset($elementData['data']) ? $elementData['data'] : []);
                    }
                }
            }
            if (isset($elementData['style'])) {
                self::deleteElementStyleFiles(Internal\Themes::getFilesInValues($elementData['style']));
            }
        }
    }

    /**
     * 
     * @param string $containerID
     * @return void
     */
    static function deleteContainer(string $containerID): void
    {
        $elementsIDs = self::getContainerElementsIDs($containerID);
        foreach ($elementsIDs as $elementID) {
            self::deleteElement($elementID, $containerID);
        }
        InternalDataElements::deleteContainer($containerID);
    }

    /**
     * 
     * @param array $filenames
     * @return void
     */
    static private function deleteElementStyleFiles(array $filenames): void
    {
        if (!empty($filenames)) {
            $app = App::get();
            $recycleBinPrefix = '.recyclebin/bearcms/element-style-changes-' . str_replace('.', '-', microtime(true)) . '/';
            foreach ($filenames as $filename) {
                $dataKey = Internal\Data::getFilenameDataKey($filename);
                if ($dataKey !== null && (strpos($dataKey, '.temp/bearcms/files/elementstyleimage/') === 0 || strpos($dataKey, 'bearcms/files/elementstyleimage/') === 0)) {
                    if ($app->data->exists($dataKey)) {
                        $app->data->rename($dataKey, $recycleBinPrefix . $dataKey);
                    }
                    UploadsSize::remove($dataKey);
                }
            }
        }
    }

    /**
     * Returns a list of element IDs in rendered order (from top left)
     * @param string $id
     * @return array
     */
    static function getContainerElementsIDs(string $id): array
    {
        $containerData = InternalDataElements::getContainer($id);
        $result = [];
        $walkElements = function ($elements) use (&$result, &$walkElements) {
            foreach ($elements as $elementData) {
                $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                if ($structuralElementData !== null) {
                    if ($structuralElementData['type'] === 'columns') {
                        if (isset($structuralElementData['elements'])) {
                            for ($i = 0; $i < 100; $i++) {
                                if (isset($structuralElementData['elements'][$i])) {
                                    $walkElements($structuralElementData['elements'][$i]);
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'floatingBox') {
                        if (isset($structuralElementData['elements'])) {
                            if (isset($structuralElementData['elements']['inside'])) {
                                $walkElements($structuralElementData['elements']['inside']);
                            }
                            if (isset($structuralElementData['elements']['outside'])) {
                                $walkElements($structuralElementData['elements']['outside']);
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'flexibleBox') {
                        if (isset($structuralElementData['elements'])) {
                            $walkElements($structuralElementData['elements']);
                        }
                    }
                } elseif (isset($elementData['id'])) {
                    $result[] = $elementData['id'];
                }
            }
        };
        $walkElements($containerData['elements']);
        return $result;
    }

    /**
     * 
     * @return string
     */
    static function getEditableElementsHTML(): string
    {
        $app = App::get();
        $html = '';
        if ((Config::hasFeature('ELEMENTS') || Config::hasFeature('ELEMENTS_*')) && !empty(self::$editorData)) {
            $requestArguments = [];
            $requestArguments['data'] = json_encode(self::$editorData, JSON_THROW_ON_ERROR);
            $cacheKey = json_encode([
                'elementsEditor',
                $app->request->base,
                $app->bearCMS->currentUser->getSessionKey(),
                $app->bearCMS->currentUser->getPermissions(),
                get_class_vars('\BearCMS\Internal\Config')
            ], JSON_THROW_ON_ERROR);
            $elementsEditorData = Server::call('elementseditor', $requestArguments, true, $cacheKey);
            if (is_array($elementsEditorData) && isset($elementsEditorData['result']) && is_array($elementsEditorData['result']) && isset($elementsEditorData['result']['content'])) {
                $html = $elementsEditorData['result']['content'];
                $html = Server::updateAssetsUrls($html, false);
            }
        }
        return $html;
    }

    /**
     * 
     * @param string $elementsContainerID
     * @return integer|null
     */
    static function getLastChangeTime(string $elementsContainerID): ?int
    {
        $dates = [];
        $containerData = InternalDataElements::getContainer($elementsContainerID);
        if (is_array($containerData)) {
            if (isset($containerData['lastChangeTime'])) {
                $dates[] = (int)$containerData['lastChangeTime'];
            }
            $elementsIDs = self::getContainerElementsIDs($elementsContainerID);
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
     * 
     * @param array $containerData
     * @param string $elementID
     * @return array|null
     */
    static function getStructuralElement(array $containerData, string $elementID): ?array
    {
        $findElement = function ($elements) use (&$findElement, $elementID) {
            foreach ($elements as $elementData) {
                $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                if ($structuralElementData !== null) {
                    if ($structuralElementData['id'] === $elementID) {
                        return $structuralElementData;
                    }
                    if ($structuralElementData['type'] === 'columns') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $columnElements) {
                                $result = $findElement($columnElements);
                                if ($result !== null) {
                                    return $result;
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'floatingBox') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $boxElements) {
                                $result = $findElement($boxElements);
                                if ($result !== null) {
                                    return $result;
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'flexibleBox') {
                        if (isset($structuralElementData['elements'])) {
                            $result = $findElement($structuralElementData['elements']);
                            if ($result !== null) {
                                return $result;
                            }
                        }
                    }
                }
            }
            return null;
        };
        return $findElement($containerData['elements']);
    }

    /**
     * 
     * @param array $containerData
     * @param array $newElementData
     * @return array
     */
    static function setStructuralElement(array $containerData, array $newElementData): array
    {
        $elementID = $newElementData['id'];
        $walkElements = function ($elements) use (&$walkElements, $elementID, $newElementData) {
            $hasChange = false;
            foreach ($elements as $index => $elementData) {
                $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                if ($structuralElementData !== null) {
                    if ($structuralElementData['id'] === $elementID) {
                        $elements[$index] = $newElementData;
                        $hasChange = true;
                        break;
                    }
                    if ($structuralElementData['type'] === 'columns') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $i => $columnElements) {
                                $result = $walkElements($columnElements);
                                if ($result[0]) {
                                    $structuralElementData['elements'][$i] = $result[1];
                                    $elements[$index] = $structuralElementData;
                                    $hasChange = true;
                                    break;
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'floatingBox') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $i => $boxElements) {
                                $result = $walkElements($boxElements);
                                if ($result[0]) {
                                    $structuralElementData['elements'][$i] = $result[1];
                                    $elements[$index] = $structuralElementData;
                                    $hasChange = true;
                                    break;
                                }
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'flexibleBox') {
                        if (isset($structuralElementData['elements'])) {
                            $result = $walkElements($structuralElementData['elements']);
                            if ($result[0]) {
                                $structuralElementData['elements'] = $result[1];
                                $elements[$index] = $structuralElementData;
                                $hasChange = true;
                                break;
                            }
                        }
                    }
                }
            }
            return [$hasChange, $elements];
        };
        $result = $walkElements($containerData['elements']);
        if ($result[0]) { // has change
            $containerData['elements'] = $result[1];
        }
        return $containerData;
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

        if ($result['type'] === null) {
            return null;
        }

        if ($result['type'] === 'column') {
            $result['type'] === 'columns';
        }

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
            } else {
                $result['style'] = []; // there is no old format
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $suffix
     * @return string
     */
    static function generateElementID(string $suffix): string
    {
        for ($i = 0; $i < 100; $i++) {
            $id = base_convert(md5(uniqid()), 16, 36) . $suffix;
            if (InternalDataElements::getElementRawData($id) === null) {
                return $id;
            }
        }
        throw new \Exception('Too much retries!');
    }

    /**
     * 
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return void
     */
    static function updateComponent(\IvoPetkov\HTMLServerComponent $component): void
    {
        $componentSrc = (string)$component->src;
        $name = strlen($componentSrc) > 0 ? $componentSrc : ($component->tagName !== 'component' ? $component->tagName : null);
        if ($name !== null) {

            $updateEditableAttribute = function (\IvoPetkov\HTMLServerComponent $component): void {
                $app = App::get();
                $editable = false;
                if ($component->editable === 'true' && $component->id !== null && strlen($component->id) > 0) {
                    if ($app->bearCMS->currentUser->exists() && $app->bearCMS->currentUser->hasPermission('modifyContent')) { // Todo cache
                        $editable = true;
                    }
                }
                $component->editable = $editable ? 'true' : 'false';
            };

            $updateContextAttributes = function (\IvoPetkov\HTMLServerComponent $component): void {
                $getUpdatedHTMLUnit = function (string $value): string {
                    if (strlen($value) > 0) {
                        if (substr($value, 0, 4) === 'var(') {
                            return $value;
                        }
                        if (is_numeric($value)) {
                            $value .= 'px';
                        }
                        if (preg_match('/^(([0-9]+)|(([0-9]*)\.([0-9]+)))(px|rem|em|%|in|cm)$/', $value) !== 1) {
                            $value = '';
                        }
                    }
                    return $value;
                };

                // Update width
                $component->width = $getUpdatedHTMLUnit((string)$component->width);
                if ($component->width === '') {
                    $component->width = '100%';
                }

                // Update spacing
                $component->spacing = $getUpdatedHTMLUnit((string)$component->spacing);
                if ($component->spacing === '') {
                    $component->spacing = '1rem';
                }

                // Update color
                $componentColor = (string)$component->color;
                if ($componentColor !== '') {
                    if (preg_match('/^#[0-9a-fA-F]{6}$/', $componentColor) !== 1) {
                        $componentColor = '';
                    }
                }
                if ($componentColor === '') {
                    $componentColor = (string)Config::getVariable('uiColor');
                }
                $component->color = $componentColor;
            };

            if ($name === 'bearcms-elements') {
                if ($component->id === null || strlen($component->id) === 0) {
                    throw new \Exception('The ID attribute is required for <bearcms-elements>');
                }
                $updateEditableAttribute($component);
                $updateContextAttributes($component);
                if (strlen((string)$component->group) === 0) {
                    $component->group = 'default';
                }
                if ($component->getAttribute('canStyle', '') === '') {
                    $currentThemeID = Internal\CurrentTheme::getID();
                    $theme = Internal\Themes::get($currentThemeID);
                    if ($theme !== null && $theme->canStyleElements) { // just in case it's registered later or other
                        $component->setAttribute('canStyle', 'true');
                    }
                }
            } elseif (isset(self::$elementsTypesFilenames[$name])) {
                $component->setAttribute('bearcms-internal-attribute-type', self::$elementsTypesCodes[$name]);
                $component->setAttribute('bearcms-internal-attribute-filename', self::$elementsTypesFilenames[$name]);
                $rawData = (string)$component->getAttribute('bearcms-internal-attribute-raw-data');
                $elementData = null;
                if (strlen($rawData) > 0) {
                    $elementData = InternalDataElements::decodeElementRawData($rawData);
                    if (is_array($elementData)) {
                        $component->id = $elementData['id'];
                    }
                } elseif ($component->id !== null && strlen($component->id) > 0) {
                    $elementRawData = InternalDataElements::getElementRawData($component->id);
                    $component->setAttribute('bearcms-internal-attribute-raw-data', $elementRawData);
                }
                $updateEditableAttribute($component);
                $updateContextAttributes($component);
                if ($component->canStyle === 'true' && isset(self::$elementsTypesOptions[$component->src])) { // Check if element supports styling
                    $canStyle = false;
                    $elementTypeOptions = self::$elementsTypesOptions[$component->src];
                    if (isset($elementTypeOptions['canStyle']) && $elementTypeOptions['canStyle']) {
                        $canStyle = true;
                    }
                    if (!$canStyle) {
                        $component->canStyle = 'false';
                    }
                }
            } elseif ($name === 'bearcms-missing-element') {
                $component->setAttribute('bearcms-internal-attribute-type', 'missing');
                $updateEditableAttribute($component);
                $updateContextAttributes($component);
            }
        }
    }

    /**
     * 
     * @param string $sourceElementID
     * @param string $targetElementID
     * @param string|null $sourceContainerID
     * @param string|null $targetContainerID
     * @return void
     */
    static function copyElement(string $sourceElementID, string $targetElementID, string $sourceContainerID = null, string $targetContainerID = null): void
    {
        $app = App::get();
        $elementData = InternalDataElements::getElement($sourceElementID);
        if ($elementData === null) {
            throw new \Exception('Source element (' . $sourceElementID . ') not found!');
        }
        $elementData['id'] = $targetElementID;
        $elementData['lastChangeTime'] = time();
        if (isset($elementData['type'])) {
            $componentName = array_search($elementData['type'], self::$elementsTypesCodes);
            if ($componentName !== false) {
                $options = self::$elementsTypesOptions[$componentName];
                if (isset($options['onDuplicate']) && is_callable($options['onDuplicate'])) {
                    $elementData['data'] = call_user_func($options['onDuplicate'], isset($elementData['data']) ? $elementData['data'] : []);
                }
            }
        }
        if (isset($elementData['style'])) {
            $filenames = Themes::getFilesInValues($elementData['style'], true);
            if (!empty($filenames)) {
                $duplicatedDataKeys = [];
                $filesToUpdate = [];
                foreach ($filenames as $filename) {
                    $filenameOptions = Internal\Data::getFilenameOptions($filename);
                    $dataKey = Internal\Data::getFilenameDataKey($filename);
                    if ($dataKey !== null && $app->data->exists($dataKey)) {
                        if (isset($duplicatedDataKeys[$dataKey])) {
                            $newDataKey = $duplicatedDataKeys[$dataKey];
                        } else {
                            $newDataKey = Internal\Data::generateNewFilename($dataKey);
                            $app->data->duplicate($dataKey, $newDataKey);
                            UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                            $duplicatedDataKeys[$dataKey] = $newDataKey;
                        }
                        $newFilenameWithOptions = Internal\Data::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                        $filesToUpdate[$filename] = $newFilenameWithOptions;
                    }
                }
                $elementData['style'] = Themes::updateFilesInValues($elementData['style'], $filesToUpdate);
            }
        }
        InternalDataElements::setElement($targetElementID, $elementData, $targetContainerID);
        InternalDataElements::dispatchElementChangeEvent($targetElementID, $targetContainerID);
    }

    /**
     * 
     * @param string $sourceContainerID
     * @param string $targetContainerID
     * @return void
     */
    static function copyContainer(string $sourceContainerID, string $targetContainerID): void
    {
        $containerData = InternalDataElements::getContainer($sourceContainerID);
        $newContainerData = $containerData;
        $newContainerData['id'] = $targetContainerID;
        $copiedElementIDs = [];
        $updateElementIDs = function ($elements) use (&$updateElementIDs, &$copiedElementIDs) {
            foreach ($elements as $index => $element) {
                if (isset($element['id'])) {
                    $oldItemID = $element['id'];
                    $newItemID = self::generateElementID('cc');
                    $elements[$index]['id'] = $newItemID;
                    $structuralElementData = self::getUpdatedStructuralElementData($element);
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
        InternalDataElements::setContainer($targetContainerID, $newContainerData);
        InternalDataElements::dispatchContainerChangeEvent($targetContainerID);
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
     * 
     * @param string $elementID
     * @return array
     */
    static function getElementUploadsSizeItems(string $elementID): array
    {
        $result = [];
        $elementData = InternalDataElements::getElement($elementID);
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
                $filenames = Themes::getFilesInValues($elementData['style']);
                foreach ($filenames as $filename) {
                    $dataKey = Internal\Data::getFilenameDataKey($filename);
                    if ($dataKey !== null) {
                        $result[] = $dataKey;
                    }
                }
            }
        }
        return $result;
    }
}
