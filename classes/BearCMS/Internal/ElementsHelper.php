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
     * @param string $type
     * @return array|null
     */
    static function getElementTypeOptions(string $type): ?array
    {
        $componentName = array_search($type, ElementsHelper::$elementsTypesCodes);
        if ($componentName !== false) {
            return ElementsHelper::$elementsTypesOptions[$componentName];
        }
        return null;
    }

    /**
     * 
     * @param \IvoPetkov\HTMLServerComponent $component
     * @return array
     */
    static function getComponentContextData(\IvoPetkov\HTMLServerComponent $component): array
    {
        $result = [];
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
        $attributesToSkip = ['src', 'id', 'editable', 'color', 'group', 'canEdit', 'canDuplicate', 'canStyle', 'canMove', 'canDelete'];
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
        $elementContainerData = ElementsDataHelper::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $attributes = '';

        $defaultElementStyle = ElementsDataHelper::getDefaultElementStyle('columns');
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];
        if (serialize($defaultElementStyle) === serialize($elementStyle)) {
            $elementStyle = []; // no need to render customizations code
        }
        $hasStyle = !empty($elementStyle);

        $layout = isset($elementStyle['layout']) ? $elementStyle['layout'] : null;
        $layoutValueDetails = $layout !== null ? Themes::getValueDetails($layout) : null;
        $direction = $layout !== null && is_array($layoutValueDetails['value']) && isset($layoutValueDetails['value']['direction']) ? trim($layoutValueDetails['value']['direction']) : '';
        if (!isset($direction[0])) {
            $direction = 'horizontal';
        }
        $widths = $layout !== null && is_array($layoutValueDetails['value']) && isset($layoutValueDetails['value']['widths']) ? trim($layoutValueDetails['value']['widths']) : '';
        if (!isset($widths[0])) {
            $widths = ';';
        }

        $columnsWidths = explode(';', $widths);
        $columnsCount = sizeof($columnsWidths);

        $innerContent = '';
        $columnsElements = isset($elementContainerData['elements']) ? $elementContainerData['elements'] : [];
        for ($i = 0; $i < $columnsCount; $i++) {
            $columnContent = '';
            if (isset($columnsElements[$i])) {
                $elementsInColumnContainerData = $columnsElements[$i];
                if (!empty($elementsInColumnContainerData)) {
                    $columnContent .= self::renderContainerElements($elementsInColumnContainerData, $editable, $contextData, $outputType);
                }
            }
            $innerContent .= '<div>' . $columnContent . '</div>';
        }

        $styles = '';
        $customizationsSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementContainerData['id']) . '"';
                self::$editorData[] = ['columns', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-columns-element';
                if ($hasStyle) {
                    $classAttributeValue .= ' ' . ElementsHelper::getCustomizationsClassName($elementContainerData['id']);
                    $customizationsSelector = ElementsHelper::getCustomizationsSelector($elementContainerData['id']);
                }
                $attributes .= ' data-bearcms-columns-direction="' . htmlentities($direction) . '"';
                $attributes .= ' data-bearcms-columns-widths="' . htmlentities($widths) . '"';
                $attributes .= ' class="' . $classAttributeValue . '"';

                $styles .= '.bearcms-columns-element{display:flex;flex-direction:row;gap:var(--bearcms-elements-spacing);}';
                $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="horizontal"]{flex-direction:row;}';
                $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="vertical"]{flex-direction:column;}';
                $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="vertical-reverse"]{flex-direction:column-reverse;}';
                $styles .= '.bearcms-columns-element[data-rvr-editable]>div{min-width:15px;}';

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
                    $columnsStyles[$i] = ($isFixedWidth ? 'flex:0 0 auto;width:' . $columnWidth . ';' : 'flex:1 0 auto;max-width:calc(' . $columnWidth . ' - (var(--bearcms-elements-spacing)*' . ($columnsCount - 1) . '/' . $columnsCount . '))') . ';';
                }

                $emptySelectorPart = '';
                foreach ($columnsStyles as $index => $columnStyle) {
                    $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="horizontal"][data-bearcms-columns-widths="' . htmlentities($widths) . '"]>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
                    $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="vertical"]:not([data-rvr-editable])>div:nth-child(' . ($index + 1) . '):empty{display:none;}';
                    $styles .= '.bearcms-columns-element[data-bearcms-columns-direction="vertical-reverse"]:not([data-rvr-editable])>div:nth-child(' . ($index + 1) . '):empty{display:none;}';
                    $emptySelectorPart .= ':has(> div:nth-child(' . ($index + 1) . '):empty)';
                }
                $styles .= '.bearcms-columns-element[data-bearcms-columns-widths="' . htmlentities($widths) . '"]:not([data-rvr-editable])' . $emptySelectorPart . '{display:none;}';
            }
        }
        $content = '<html>';
        $content .= '<head><style>' . $styles . '</style></head>';
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . '</body>'
            . '</html>';

        if ($customizationsSelector !== null) {
            $content = self::applyCustomizations($content, 'columns', $elementStyle, $customizationsSelector);
        }

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
        $elementContainerData = ElementsDataHelper::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $attributes = '';

        $defaultElementStyle = ElementsDataHelper::getDefaultElementStyle('floatingBox');
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];
        if (serialize($defaultElementStyle) === serialize($elementStyle)) {
            $elementStyle = []; // no need to render customizations code
        }
        $hasStyle = !empty($elementStyle);

        $layout = isset($elementStyle['layout']) ? $elementStyle['layout'] : null;
        $layoutValueDetails = $layout !== null ? Themes::getValueDetails($layout) : null;
        $position = $layout !== null && is_array($layoutValueDetails['value']) && isset($layoutValueDetails['value']['position']) ? trim($layoutValueDetails['value']['position']) : '';
        if (!isset($position[0])) {
            $position = 'left';
        }
        $width = $layout !== null && is_array($layoutValueDetails['value']) && isset($layoutValueDetails['value']['width']) ? trim($layoutValueDetails['value']['width']) : '';
        if (!isset($width[0])) {
            $width = '50%';
        }

        $getElementsContent = function (string $location) use ($elementContainerData, $contextData, $editable, $outputType) {
            $content = '';
            $elementsContainerData = isset($elementContainerData['elements'][$location]) ? $elementContainerData['elements'][$location] : [];
            if (!empty($elementsContainerData)) {
                $content .= self::renderContainerElements($elementsContainerData, $editable, $contextData, $outputType);
            }
            return $content;
        };

        $innerContent = '<div>' . $getElementsContent('inside') . '</div>';
        $innerContent .= '<div>' . $getElementsContent('outside') . '</div>';

        $styles = '';
        $customizationsSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementContainerData['id']) . '"';
                self::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-floating-box-element';
                if ($hasStyle) {
                    $classAttributeValue .= ' ' . ElementsHelper::getCustomizationsClassName($elementContainerData['id']);
                    $customizationsSelector = ElementsHelper::getCustomizationsSelector($elementContainerData['id']);
                }
                $attributes .= ' data-bearcms-floating-box-position="' . htmlentities($position) . '"';
                $attributes .= ' data-bearcms-floating-box-width="' . htmlentities($width) . '"';
                $attributes .= ' class="' . $classAttributeValue . '"';

                $styles .= '.bearcms-floating-box-element:not([data-rvr-editable]):has(> div:first-child:empty):has(> div:last-child:empty){display:none;}';
                $styles .= '.bearcms-floating-box-element>div:first-child{max-width:100%;}';
                $styles .= '.bearcms-floating-box-element:not([data-rvr-editable])>div:first-child:empty{display:none;}';
                $styles .= '.bearcms-floating-box-element:not([data-rvr-editable])>div:last-child:empty{display:none;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]>div:first-child{float:left;margin-right:var(--bearcms-elements-spacing);margin-left:0;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]>div:first-child{float:right;margin-left:var(--bearcms-elements-spacing);margin-right:0;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="above"]{display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="above"]>div{width:100%;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="below"]{display:flex;flex-direction:column-reverse;gap:var(--bearcms-elements-spacing);}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="below"]>div{width:100%;}';
                $styles .= '.bearcms-floating-box-element>div:last-child{display:block;}';
                $styles .= '.bearcms-floating-box-element[data-rvr-editable]>div:first-child{min-width:15px;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';
                $styles .= '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';

                foreach (['left', 'right'] as $selectorPosition) {
                    $selector = '.bearcms-floating-box-element[data-bearcms-floating-box-position="' . $selectorPosition . '"][data-bearcms-floating-box-width="' . htmlentities($width) . '"]>div:first-child';
                    if (preg_match("/^[0-9\.]*%$/", $width) === 1 && $width !== '100%') {
                        $styles .= $selector . '{width:calc(' . $width . ' - var(--bearcms-elements-spacing)/2);}';
                    } else {
                        $styles .= $selector . '{width:' . $width . ';}';
                    }
                }
            }
        }
        $content = '<html>';
        $content .= '<head><style>' . $styles . '</style></head>';
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . '</body>'
            . '</html>';

        if ($customizationsSelector !== null) {
            $content = self::applyCustomizations($content, 'floatingBox', $elementStyle, $customizationsSelector);
        }

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
        $elementContainerData = ElementsDataHelper::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $attributes = '';

        $canStyle = isset($contextData['canStyle']) && $contextData['canStyle'] === 'true';
        $defaultElementStyle = ElementsDataHelper::getDefaultElementStyle('flexibleBox');
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];
        if (serialize($defaultElementStyle) === serialize($elementStyle)) {
            $elementStyle = []; // no need to render customizations code
        }
        $hasStyle = $canStyle && !empty($elementStyle);

        $linkURL = isset($elementContainerData['data'], $elementContainerData['data']['url']) ? $elementContainerData['data']['url'] : null;
        $linkTitle = $linkURL !== null && isset($elementContainerData['data'], $elementContainerData['data']['title']) ? $elementContainerData['data']['title'] : null;

        $innerContent = '<div>';
        $elementsContainerData = $elementContainerData['elements'];
        if (!empty($elementsContainerData)) {
            $innerContent .= self::renderContainerElements($elementsContainerData, $editable, $contextData, $outputType);
        }
        $innerContent .= '</div>';
        $linkHTML = null;
        if ($linkURL !== null) {
            $attributes .= ' data-bearcms-flexible-box-link';
            list($linkURL, $linkOnClick, $linkHTML) = \BearCMS\Internal\Links::updateURL($linkURL);
            $innerContent .= '<a href="' . htmlentities($linkURL) . '"' . ($linkOnClick !== null ? ' onclick="' . htmlentities($linkOnClick) . '"' : '') . ($linkTitle !== null ? ' title="' . htmlentities($linkTitle) . '"' : '') . '></a>';
        }

        $styles = '';
        $customizationsSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementContainerData['id']) . '"';
                self::$editorData[] = ['flexibleBox', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-flexible-box-element';
                if ($hasStyle) {
                    $classAttributeValue .= ' ' . ElementsHelper::getCustomizationsClassName($elementContainerData['id']);
                    $customizationsSelector = ElementsHelper::getCustomizationsSelector($elementContainerData['id']);
                }
                $attributes .= ' class="' . $classAttributeValue . '"';

                $styles .= '.bearcms-flexible-box-element{position:relative;box-sizing:border-box;display:flex;flex-direction:column;}';
                $styles .= '.bearcms-flexible-box-element:not([data-rvr-editable]):has(> div:empty){display:none;}';
                $styles .= '.bearcms-flexible-box-element>a{width:100%;height:100%;position:absolute;top:0;left:0;display:block;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-link]:not([data-rvr-editable])>div{pointer-events:none;}';
                $styles .= '.bearcms-flexible-box-element>div{display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'; // Must be here when canStyle=false
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical-reverse"]>div{flex-direction:column-reverse;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal"]>div{flex-direction:row;flex-wrap:wrap;align-items:flex-start;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal-reverse"]>div{flex-direction:row-reverse;flex-wrap:wrap;align-items:flex-start;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal"]>div>div{min-width:15px;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal-reverse"]>div>div{min-width:15px;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical"]>div>*{width:100%}'; // the default size when elements have margin-left/right=auto;
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical-reverse"]>div>*{width:100%}'; // the default size when elements have margin-left/right=auto;
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="start"]>div{justify-content:flex-start;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="center"]>div{justify-content:center;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="end"]>div{justify-content:flex-end;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-between"]>div{justify-content:space-between;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-around"]>div{justify-content:space-around;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-evenly"]>div{justify-content:space-evenly;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="start"]>div{align-items:flex-start;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="center"]>div{align-items:center;}';
                $styles .= '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="end"]>div{align-items:flex-end;}';
            }
        }
        $content = '<html>';
        $content .= '<head><style>' . $styles . '</style></head>';
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . ($linkHTML !== null ? $linkHTML : '') // todo why cant be outsite the html??
            . '</body>'
            . '</html>';

        if ($customizationsSelector !== null) {
            $content = self::applyCustomizations($content, 'flexibleBox', $elementStyle, $customizationsSelector);
        }

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
            if (!ElementsDataHelper::isStructuralElementData($elementContainerData)) {
                $elementsIDs[] = $elementContainerData['id'];
            }
        }
        $elementsRawData = InternalDataElements::getElementsRawData($elementsIDs);
        foreach ($elementsContainerData as $elementContainerData) {
            if (ElementsDataHelper::isColumnsElementContainerData($elementContainerData)) {
                $content .= self::renderColumns($elementContainerData, $editable, $contextData, true, $outputType);
            } elseif (ElementsDataHelper::isFloatingBoxElementContainerData($elementContainerData)) {
                $content .= self::renderFloatingBox($elementContainerData, $editable, $contextData, true, $outputType);
            } elseif (ElementsDataHelper::isFlexibleBoxElementContainerData($elementContainerData)) {
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
     * @param string $elementID
     * @param string $containerID
     * @return array|null
     */
    static function getElementStyleOptions(string $elementID, string $containerID): ?array
    {
        $elementType = null;
        $elementData = InternalDataElements::getElement($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
            //$htmlElementID = self::getHTMLElementID($elementData['id']);
        }
        if ($elementType === null) {
            $containerData = InternalDataElements::getContainer($containerID);
            if (is_array($containerData)) {
                $elementData = ElementsDataHelper::getContainerDataElement($containerData, $elementID, 'structural');
                if (is_array($elementData)) {
                    $elementType = $elementData['type'];
                    $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
                    //$htmlElementID = self::getHTMLElementID($elementData['id']);
                }
            }
        }
        if ($elementType !== null) {
            if (isset(InternalThemes::$elementsOptions[$elementType])) {
                Localization::setAdminLocale();
                if ($elementType === 'flexibleBox') { // todo maybe add other structural here ???
                    $themeID = null;
                    $themeOptionsSelectors = null;
                } else {
                    $themeID = InternalThemes::getActiveThemeID();
                    $themeOptionsSelectors = InternalThemes::getElementsOptionsSelectors($themeID, $elementType);
                }
                $options = new \BearCMS\Themes\Theme\Options();
                $callback = InternalThemes::$elementsOptions[$elementType];
                if (is_array($callback)) {
                    $callback = $callback[1];
                }
                call_user_func($callback, $options, 'ElementStyle', self::getCustomizationsSelector($elementID), InternalThemes::OPTIONS_CONTEXT_ELEMENT, []);
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
     * @param string $elementID
     * @param string $containerID
     * @param array $values
     * @return void
     */
    static function setElementStyleValues(string $elementID, string $containerID, array $values): void
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
            if (is_array($containerData)) {
                $elementData = ElementsDataHelper::getContainerDataElement($containerData, $elementID, 'structural');
                if (is_array($elementData)) {
                    $elementType = $elementData['type'];
                    $oldElementStyle = isset($elementData['style']) ? $elementData['style'] : [];
                    $isStructural = true;
                }
            }
        }
        if ($elementType !== null) {
            $filesInOldStyle = InternalThemes::getFilesInValues($oldElementStyle);
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
            $filesInNewStyle = InternalThemes::getFilesInValues($newElementStyle, true);
            $filesToUpdate = [];
            $duplicatedDataKeys = [];
            $filesToKeep = [];
            foreach ($filesInNewStyle as $filename) {
                $filenameOptions = InternalData::getFilenameOptions($filename);
                $filenameWithoutOptions = InternalData::removeFilenameOptions($filename);
                $dataKey = InternalData::getFilenameDataKey($filenameWithoutOptions);
                if ($dataKey !== null && strpos($dataKey, '.temp/bearcms/files/elementstyleimage/') === 0) {
                    $newDataKey = 'bearcms/files/elementstyleimage/' . pathinfo($dataKey, PATHINFO_BASENAME);
                    if (!isset($duplicatedDataKeys[$dataKey])) {
                        $app->data->duplicate($dataKey, $newDataKey);
                        UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        $duplicatedDataKeys[$dataKey] = true;
                    }
                    $newFilenameWithOptions = InternalData::setFilenameOptions('data:' . $newDataKey, $filenameOptions);
                    $filesToUpdate[$filename] = $newFilenameWithOptions;
                    $filesToDelete[] = $filenameWithoutOptions;
                } else {
                    $filesToKeep[] = $filenameWithoutOptions;
                }
            }
            $filesToDelete = array_merge($filesToDelete, array_diff($filesInOldStyle, $filesToKeep));
            $newElementStyle = InternalThemes::updateFilesInValues($newElementStyle, $filesToUpdate);
            if ($isStructural) {
                if ($elementType === 'columns') { // Move elements to the last column if theirs is removed
                    $getColumnsCount = function (array $elementStyle): int {
                        $layout = isset($elementStyle['layout']) ? $elementStyle['layout'] : null;
                        $layoutValueDetails = Themes::getValueDetails($layout);
                        $widths = is_array($layoutValueDetails['value']) && isset($layoutValueDetails['value']['widths']) ? trim($layoutValueDetails['value']['widths']) : '';
                        if (!isset($widths[0])) {
                            $widths = ';';
                        }
                        return sizeof(explode(';', $widths));
                    };
                    $oldColumnsCount = $getColumnsCount($oldElementStyle);
                    $newColumnsCount = $getColumnsCount($newElementStyle);
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

                $containerData = ElementsDataHelper::setContainerDataElement($containerData, $elementData);
                InternalDataElements::setContainer($containerID, $containerData);
                InternalDataElements::dispatchContainerChangeEvent($containerID);
            } else {
                $elementData['style'] = $newElementStyle;
                if (empty($elementData['style'])) {
                    unset($elementData['style']);
                }
                InternalDataElements::setElement($elementID, $elementData);
                InternalDataElements::dispatchElementChangeEvent($elementID, $containerID);
            }
            ElementsDataHelper::deleteElementStyleFiles($filesToDelete);
        }
    }

    /**
     * 
     * @param string $content
     * @param string $elementType
     * @param array $elementStyleData
     * @param string $selector
     * @return string
     */
    static function applyCustomizations(string $content, string $elementType, array $elementStyleData, string $selector): string
    {
        if (isset(InternalThemes::$elementsOptions[$elementType])) {
            $options = new \BearCMS\Themes\Theme\Options();
            $callback = InternalThemes::$elementsOptions[$elementType];
            if (is_array($callback)) {
                $callback = $callback[1];
            }
            call_user_func($callback, $options, '', $selector, InternalThemes::OPTIONS_CONTEXT_ELEMENT, []);
            $options->setValues($elementStyleData);
            $htmlData = InternalThemes::getOptionsHTMLData($options->getList());
            return InternalThemes::processOptionsHTMLData($htmlData, $content);
        }
        return '';
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
                    $component->setAttribute('canStyle', 'true');
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
     * @param string $elementID
     * @return string
     */
    static function getHTMLElementID(string $elementID): string
    {
        return 'brelb' . md5($elementID);
    }

    /**
     * 
     * @param string $elementID
     * @return string
     */
    static function getCustomizationsClassName(string $elementID): string
    {
        return 'bearcms-element-style-' . md5($elementID);
    }

    /**
     * 
     * @param string $elementID
     * @return string
     */
    static function getCustomizationsSelector(string $elementID): string
    {
        $className = '.' . self::getCustomizationsClassName($elementID);
        return str_repeat($className, 3); // todo improve. Maybe use @layer; increase $times if conflicts
    }
}
