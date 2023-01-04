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

        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $widths = isset($elementStyle['widths']) ? $elementStyle['widths'] : ','; //50%,50%
        $autoVerticalWidth = isset($elementStyle['autoVerticalWidth']) ? $elementStyle['autoVerticalWidth'] : '500px';
        $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;

        $columnsWidths = explode(',', $widths);
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
            if ($outputType === 'full-html') {
                $innerContent .= '<div class="bearcms-elements-columns-column">' . $columnContent . '</div>';
            } else {
                $innerContent .= '<div>' . $columnContent . '</div>';
            }
        }

        $styles = '';
        if ($inContainer) {
            $attributes = '';
            if ($editable) {
                $htmlElementID = self::getHTMLElementID($elementContainerData['id']);
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
                    $columnsStyles[$i] = 'min-width:15px;' . ($isFixedWidth ? 'flex:0 0 auto;width:' . $columnWidth : 'flex:1 0 auto;max-width:calc(' . $columnWidth . ' - (var(--bearcms-elements-spacing)*' . ($columnsCount - 1) . '/' . $columnsCount . '))') . ';margin-right:' . ($columnsCount > $i + 1 ? 'var(--bearcms-elements-spacing)' : '0') . ';';
                }

                $className = 'bre' . md5('columns$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

                $styles .= '.' . $className . '{display:flex !important;flex-direction:row;}';
                $styles .= '.' . $className . '>div>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                foreach ($columnsStyles as $index => $columnStyle) {
                    $styles .= '.' . $className . '>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
                }
                $styles .= '.' . $className . '[data-columns-auto-vertical="1"]{flex-direction:column;}';
                foreach ($columnsStyles as $index => $columnStyle) {
                    $styles .= '.' . $className . '[data-columns-auto-vertical="1"]>div:nth-child(' . ($index + 1) . '){flex:1 0 auto;width:100%;max-width:100%;margin-right:0;}';
                }
                $styles .= '.' . $className . '[data-columns-auto-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . '[data-rvr-editable][data-columns-auto-vertical="1"]>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';

                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-columns ' . $className . '"';
                if ($autoVerticalWidthInPx !== null) {
                    $attributes .= ' data-responsive-attributes="w<=' . $autoVerticalWidthInPx . '=>data-columns-auto-vertical=1"';
                }
                if (isset($elementStyle['elementsSpacing']) && strlen($elementStyle['elementsSpacing']) > 0) {
                    $attributes .= ' style="--bearcms-elements-spacing:' . $elementStyle['elementsSpacing'] . '"';
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
        $elementContainerData = ElementsDataHelper::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $position = isset($elementStyle['position']) ? $elementStyle['position'] : 'left';
        if ($position !== 'left') {
            $position = 'right';
        }
        $width = isset($elementStyle['width']) ? $elementStyle['width'] : '50%';
        $autoVerticalWidth = isset($elementStyle['autoVerticalWidth']) ? $elementStyle['autoVerticalWidth'] : '500px';
        $autoVerticalWidthInPx = strpos($autoVerticalWidth, 'px') !== false ? (int)str_replace('px', '', $autoVerticalWidth) : null;

        $getElementsContent = function ($location) use ($elementContainerData, $contextData, $editable, $outputType) {
            $content = '';
            $elementsContainerData = isset($elementContainerData['elements'][$location]) ? $elementContainerData['elements'][$location] : [];
            if (!empty($elementsContainerData)) {
                $content .= self::renderContainerElements($elementsContainerData, $editable, $contextData, $outputType);
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
                $htmlElementID = self::getHTMLElementID($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                self::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
            }
            if ($outputType === 'full-html') {
                $className = 'bre' . md5('floatingbox$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));

                $styles .= '.' . $className . '{--bearcms-floating-box-width:' . (substr($width, -1) === '%' && $width !== '100%' ? 'calc(' . $width . ' - var(--bearcms-elements-spacing)/2)' : $width) . ';}';
                $styles .= '.' . $className . '>div:first-child{max-width:100%;}';
                $styles .= '.' . $className . '[data-floating-box-position="left"]:not([data-floating-box-auto-vertical="1"]):not([data-floating-box-vertical="1"])>div:first-child{width:var(--bearcms-floating-box-width);float:left;margin-right:var(--bearcms-elements-spacing);margin-left:0;}';
                $styles .= '.' . $className . '[data-floating-box-position="right"]:not([data-floating-box-auto-vertical="1"]):not([data-floating-box-vertical="1"])>div:first-child{width:var(--bearcms-floating-box-width);float:right;margin-left:var(--bearcms-elements-spacing);margin-right:0;}';
                $styles .= '.' . $className . '>div:last-child{display:block;}';
                $styles .= '.' . $className . '[data-rvr-editable]>div:first-child{min-width:15px;}';
                $styles .= '.' . $className . '>div>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . '[data-floating-box-auto-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . '[data-floating-box-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . '[data-rvr-editable][data-floating-box-auto-vertical="1"]>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . '[data-rvr-editable][data-floating-box-vertical="1"]>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
                $styles .= '.' . $className . ':after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}';

                $attributes .= ' class="bearcms-elements-element-container bearcms-elements-floating-box ' . $className . '"';
                $attributes .= ' data-floating-box-position="' . $position . '"';
                $attributes .= ' data-responsive-attributes="' . ($autoVerticalWidthInPx !== null ? 'w<=' . $autoVerticalWidthInPx . '=>data-floating-box-auto-vertical=1,' : '') . 'f(' . $responsiveFunctionName . ')=>data-floating-box-vertical=1"';

                if (isset($elementStyle['elementsSpacing']) && strlen($elementStyle['elementsSpacing']) > 0) {
                    $attributes .= ' style="--bearcms-elements-spacing:' . $elementStyle['elementsSpacing'] . '"';
                }
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
        $elementContainerData = ElementsDataHelper::getUpdatedStructuralElementData($elementContainerData);
        if ($elementContainerData === null) {
            return '';
        }

        $canStyle = isset($contextData['canStyle']) && $contextData['canStyle'] === 'true';
        $elementStyle = isset($elementContainerData['style']) && is_array($elementContainerData['style']) ? $elementContainerData['style'] : [];

        $linkURL = isset($elementContainerData['data'], $elementContainerData['data']['url']) ? $elementContainerData['data']['url'] : null;
        $linkTitle = $linkURL !== null && isset($elementContainerData['data'], $elementContainerData['data']['title']) ? $elementContainerData['data']['title'] : null;

        $innerContent = '<div>';
        $elementsContainerData = $elementContainerData['elements'];
        if (!empty($elementsContainerData)) {
            $innerContent .= self::renderContainerElements($elementsContainerData, $editable, $contextData, $outputType);
        }
        $innerContent .= '</div>';
        if ($linkURL !== null) {
            $innerContent .= '<a href="' . htmlentities($linkURL) . '"' . ($linkTitle !== null ? ' title="' . htmlentities($linkTitle) . '"' : '') . ' style="width:100%;height:100%;position:absolute;top:0;left:0;display:block;"></a>';
        }

        $classAttributeValue = null;
        $customizationsSelector = null;
        if ($inContainer) {

            $attributes = '';

            $hasStyle = $canStyle && !empty($elementStyle);

            $classAttributeValue = 'bearcms-elements-element-container bearcms-elements-flexible-box';

            if ($editable) {
                $htmlElementID = self::getHTMLElementID($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                self::$editorData[] = ['flexibleBox', $elementContainerData['id'], $contextData];
            }
            if ($hasStyle) {
                $styleClassName = 'bearcms-elements-element-style-' . md5($elementContainerData['id']);
                $classAttributeValue .= ' ' . $styleClassName;
                $customizationsSelector = '.' . $styleClassName;
            }
        }
        if ($classAttributeValue !== null) {
            $attributes .= ' class="' . $classAttributeValue . '"';
        }
        $content = '<html>';
        if ($outputType === 'full-html' && $inContainer) {
            $styles = '';
            $styles .= '.bearcms-elements-flexible-box{position:relative;}';
            $styles .= '.bearcms-elements-flexible-box>div{display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'; // Must be here when canStyle=false
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-direction="verticalReverse"]>div{flex-direction:column-reverse;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-direction="horizontal"]>div{flex-direction:row;flex-wrap:wrap;align-items:flex-start;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-direction="horizontalReverse"]>div{flex-direction:row-reverse;flex-wrap:wrap;align-items:flex-start;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-direction="horizontal"]>div>div{min-width:15px;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-direction="horizontalReverse"]>div>div{min-width:15px;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-alignment="start"]>div{justify-content:flex-start;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-alignment="center"]>div{justify-content:center;}';
            $styles .= '.bearcms-elements-flexible-box[data-flexible-box-alignment="end"]>div{justify-content:flex-end;}';
            $content .= '<head><style>' . $styles . '</style></head>';
        }
        $content .= '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
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
        //$app = App::get();
        //$app->logs->log('debug', 'ElementsHelper::getElementStyleOptions - ' . $elementID . ' - ' . $containerID);
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
                call_user_func($callback, $options, 'ElementStyle', '.bearcms-elements-element-style-' . md5($elementID), InternalThemes::OPTIONS_CONTEXT_ELEMENT, []);
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
        //$app->logs->log('debug', 'ElementsHelper::setElementStyleValues - ' . $elementID . ' - ' . $containerID . "\n" . print_r($values, true));
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
}
