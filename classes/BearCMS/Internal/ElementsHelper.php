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
use BearCMS\Internal\Data\Elements as InternalDataElements;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsHelper
{

    static $editorData = [];
    static $renderedData = [];
    static $elementsTypeDefinitions = [];
    static $elementsTypeComponents = [];
    static $lastLoadMoreServerData = null;

    /**
     * 
     * @param string $type
     * @return ElementType|null
     */
    static function getElementTypeDefinition(string $type): ?ElementType
    {
        $componentName = array_search($type, ElementsHelper::$elementsTypeComponents);
        if ($componentName !== false) {
            return ElementsHelper::$elementsTypeDefinitions[$componentName];
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
            if ($add && isset(self::$elementsTypeDefinitions[$component->src])) {
                $elementTypeDefinition = self::$elementsTypeDefinitions[$component->src];
                foreach ($elementTypeDefinition->properties as $property) {
                    if (strtolower($key) === strtolower($property['id'])) {
                        $add = false;
                        break;
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
        $componentName = array_search($elementData['type'], self::$elementsTypeComponents);
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

        $elementID = $elementContainerData['id'];
        $elementStyleID = isset($elementContainerData['styleID']) ? $elementContainerData['styleID'] : null;
        $elementStyleValue = isset($elementContainerData['style']) ? $elementContainerData['style'] : null;
        $elementTags = isset($elementContainerData['tags']) ? $elementContainerData['tags'] : [];
        $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('columns', true)['layout']['value'];
        list($styleID, $styleValue) = ElementStylesHelper::getElementRealStyleData($elementStyleID, $elementStyleValue, ElementsDataHelper::getDefaultElementStyle('columns'));

        $widths = ';';
        if (is_array($styleValue) && isset($styleValue['layout'])) {
            $widthsValues = InternalThemes::getValueCSSPropertyValues($styleValue['layout'], 'widths'); // must be only one
            if (isset($widthsValues[0])) {
                $widths = $widthsValues[0];
            }
        };

        $columnsWidths = explode(';', $widths);
        $columnsCount = sizeof($columnsWidths);

        $innerContent = '';
        $columnsElements = isset($elementContainerData['elements']) ? $elementContainerData['elements'] : [];
        $extraColumns = array_filter(array_keys($columnsElements), function ($index) use ($columnsCount) {
            return $index >= $columnsCount;
        });
        sort($extraColumns);
        $lastColumnIndex = $columnsCount - 1;
        for ($i = 0; $i < $columnsCount; $i++) {
            $columnContent = '';
            $addColumnsContent = function ($columnElementsData) use (&$columnContent, $editable, $contextData, $outputType) {
                if (!empty($columnElementsData)) {
                    $columnContent .= self::renderContainerElements($columnElementsData, $editable, $contextData, $outputType);
                }
            };
            if (isset($columnsElements[$i])) {
                $addColumnsContent($columnsElements[$i]);
            }
            if ($lastColumnIndex === $i && !empty($extraColumns)) {
                foreach ($extraColumns as $extraColumnIndex) {
                    $addColumnsContent($columnsElements[$extraColumnIndex]);
                }
            }
            $innerContent .= '<div>' . $columnContent . '</div>';
        }

        $content = '<html>'
            . '<head>';

        $styleSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementID) . '"';
                self::$editorData[] = ['columns', $elementID, $contextData];
            } else {
                self::$renderedData[] = ['columns', $elementID];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-columns-element';
                $styleSelector = ElementStylesHelper::getElementStyleSelector($elementID, $styleID);
                if ($styleSelector !== null) {
                    $classAttributeValue .= ' ' . ElementStylesHelper::getElementStyleClassName($elementID, $styleID);
                }
                $attributes .= ' class="' . $classAttributeValue . '"';

                $content .= '<style>'
                    . '.bearcms-columns-element{display:flex;flex-direction:row;gap:var(--bearcms-elements-spacing);--css-to-attribute-data-bearcms-columns-widths:' . InternalThemes::escapeCSSValue($defaultLayoutValue['widths']) . ';--css-to-attribute-data-bearcms-columns-direction:' . $defaultLayoutValue['direction'] . ';}'
                    . '.bearcms-columns-element>div{display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-columns-element[data-bearcms-columns-direction="horizontal"]{flex-direction:row;}'
                    . '.bearcms-columns-element[data-bearcms-columns-direction="vertical"]{flex-direction:column;}'
                    . '.bearcms-columns-element[data-bearcms-columns-direction="vertical-reverse"]{flex-direction:column-reverse;}'
                    . ($editable ? '.bearcms-columns-element[data-rvr-editable]>div{min-width:15px;}' : '')
                    . '</style>';

                $getWidthsCSS = function (string $widths) use ($editable): string {
                    $result = '';
                    $columnsWidths = explode(';', $widths);
                    $columnsCount = sizeof($columnsWidths);
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
                        $columnsStyles[$i] = $isFixedWidth ? 'flex:0 0 auto;width:' . $columnWidth . ';' : 'flex:1 0 auto;max-width:calc(' . $columnWidth . ' - (var(--bearcms-elements-spacing)*' . ($columnsCount - 1) . '/' . $columnsCount . '));';
                    }

                    $selectorPrefix = '.bearcms-columns-element[data-bearcms-columns-widths="' . InternalThemes::escapeCSSValue($widths, true) . '"]';
                    $notEditableSelector = $editable ? ':not([data-rvr-editable])' : '';

                    $emptySelectorPart = '';
                    foreach ($columnsStyles as $index => $columnStyle) {
                        $result .= $selectorPrefix . '[data-bearcms-columns-direction="horizontal"]>div:nth-child(' . ($index + 1) . '){' . $columnStyle . '}';
                        $result .= $selectorPrefix . '[data-bearcms-columns-direction="vertical"]' . $notEditableSelector . '>div:nth-child(' . ($index + 1) . '):empty{display:none;}';
                        $result .= $selectorPrefix . '[data-bearcms-columns-direction="vertical-reverse"]' . $notEditableSelector . '>div:nth-child(' . ($index + 1) . '):empty{display:none;}';
                        $emptySelectorPart .= ':has(> div:nth-child(' . ($index + 1) . '):empty)';
                    }
                    $result .= $selectorPrefix . $notEditableSelector . $emptySelectorPart . '{display:none;}'; // if all columns are empty
                    return $result;
                };

                $content .= '<style>'
                    . $getWidthsCSS($widths)
                    . '</style>';
            }
        }
        if ($styleSelector !== null) {
            $content .= self::getStyleHTML('columns', $styleValue, $styleSelector, true, !$editable);
        }
        if (!empty($elementTags)) {
            $attributes .= self::getTagsHTMLAttributes($elementTags);
        }
        $content .= '<link rel="client-packages-embed" name="cssToAttributes">'
            . '</head>'
            . '<body>'
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

        $attributes = '';

        $elementID = $elementContainerData['id'];
        $elementStyleID = isset($elementContainerData['styleID']) ? $elementContainerData['styleID'] : null;
        $elementStyleValue = isset($elementContainerData['style']) ? $elementContainerData['style'] : null;
        $elementTags = isset($elementContainerData['tags']) ? $elementContainerData['tags'] : [];
        $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('floatingBox', true)['layout']['value'];
        list($styleID, $styleValue) = ElementStylesHelper::getElementRealStyleData($elementStyleID, $elementStyleValue, ElementsDataHelper::getDefaultElementStyle('floatingBox'));

        $widths = ['50%'];
        if (is_array($styleValue) && isset($styleValue['layout'])) {
            $widths = InternalThemes::getValueCSSPropertyValues($styleValue['layout'], 'width');
        };

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

        $content = '<html>'
            . '<head>';

        $styleSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementID) . '"';
                self::$editorData[] = ['floatingBox', $elementID, $contextData];
            } else {
                self::$renderedData[] = ['floatingBox', $elementID];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-floating-box-element';
                $styleSelector = ElementStylesHelper::getElementStyleSelector($elementID, $styleID);
                if ($styleSelector !== null) {
                    $classAttributeValue .= ' ' . ElementStylesHelper::getElementStyleClassName($elementID, $styleID);
                }
                $attributes .= ' class="' . $classAttributeValue . '"';

                $notEditableSelector = $editable ? ':not([data-rvr-editable])' : '';
                $content .= '<style>'
                    . '.bearcms-floating-box-element{--css-to-attribute-data-bearcms-floating-box-position:' . $defaultLayoutValue['position'] . ';--css-to-attribute-data-bearcms-floating-box-width:' . $defaultLayoutValue['width'] . ';}'
                    . '.bearcms-floating-box-element' . $notEditableSelector . ':has(> div:first-child:empty):has(> div:last-child:empty){display:none;}'
                    . '.bearcms-floating-box-element>div:first-child{max-width:100%;}'
                    . '.bearcms-floating-box-element' . $notEditableSelector . '>div:first-child:empty{display:none;}'
                    . '.bearcms-floating-box-element' . $notEditableSelector . '>div:last-child:empty{display:none;}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]>div:first-child{float:left;margin-right:var(--bearcms-elements-spacing);margin-left:0;display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]>div:last-child{display:block;}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]>div:last-child>*:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]>div:first-child{float:right;margin-left:var(--bearcms-elements-spacing);margin-right:0;display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]>div:last-child{display:block;}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]>div:last-child>*:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="above"]{display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="above"]>div{width:100%;display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="below"]{display:flex;flex-direction:column-reverse;gap:var(--bearcms-elements-spacing);}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="below"]>div{width:100%;display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}'
                    . ($editable ? '.bearcms-floating-box-element[data-rvr-editable]>div:first-child{min-width:15px;}' : '')
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="left"]:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}'
                    . '.bearcms-floating-box-element[data-bearcms-floating-box-position="right"]:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}'
                    . '</style>';

                $getWidthCSS = function (string $width) use ($editable): string {
                    $result = '';
                    foreach (['left', 'right'] as $selectorPosition) {
                        $selector = '.bearcms-floating-box-element[data-bearcms-floating-box-position="' . $selectorPosition . '"][data-bearcms-floating-box-width="' . htmlentities($width) . '"]>div:first-child';
                        if (preg_match("/^[0-9\.]*%$/", $width) === 1 && $width !== '100%') {
                            $result .= $selector . '{width:calc(' . $width . ' - var(--bearcms-elements-spacing)/2);}';
                        } else {
                            $result .= $selector . '{width:' . $width . ';}';
                        }
                    }
                    return $result;
                };

                foreach ($widths as $width) {
                    $content .= '<style>'
                        . $getWidthCSS($width)
                        . '</style>';
                }
            }
        }
        if ($styleSelector !== null) {
            $content .= self::getStyleHTML('floatingBox', $styleValue, $styleSelector, true, !$editable);
        }
        if (!empty($elementTags)) {
            $attributes .= self::getTagsHTMLAttributes($elementTags);
        }
        $content .= '<link rel="client-packages-embed" name="cssToAttributes">'
            . '</head>'
            . '<body>'
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

        $attributes = '';

        $elementID = $elementContainerData['id'];
        $canStyle = isset($contextData['canStyle']) && $contextData['canStyle'] === 'true';
        $elementStyleID = isset($elementContainerData['styleID']) ? $elementContainerData['styleID'] : null;
        $elementStyleValue = isset($elementContainerData['style']) ? $elementContainerData['style'] : null;
        $elementTags = isset($elementContainerData['tags']) ? $elementContainerData['tags'] : [];
        $defaultLayoutValue = ElementsDataHelper::getDefaultElementStyle('flexibleBox', true)['layout']['value'];
        list($styleID, $styleValue) = ElementStylesHelper::getElementRealStyleData($elementStyleID, $elementStyleValue, ElementsDataHelper::getDefaultElementStyle('flexibleBox'));

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
            $attributes .= ' tabindex="0"';
            $attributes .= ' onkeydown="if(event.keyCode===13){this.lastChild.click();}"';
            list($linkURL, $linkOnClick, $linkHTML) = \BearCMS\Internal\Links::updateURL($linkURL);
            $attributes .= ' role="button"';
            if ($linkTitle !== null) {
                $attributes .= ' title="' . htmlentities($linkTitle) . '"';
            }
            $innerContent .= '<a' . ($linkURL !== '' ? ' href="' . htmlentities($linkURL) . '"' : '') . ($linkOnClick !== null ? ' onclick="' . htmlentities($linkOnClick) . '"' : '') . '></a>';
        }

        $content = '<html>'
            . '<head>';

        $styleSelector = null;
        if ($inContainer) {
            if ($editable) {
                $attributes .= ' id="' . self::getHTMLElementID($elementID) . '"';
                self::$editorData[] = ['flexibleBox', $elementID, $contextData];
            } else {
                self::$renderedData[] = ['flexibleBox', $elementID];
            }
            if ($outputType === 'full-html') {
                $classAttributeValue = 'bearcms-element bearcms-flexible-box-element';
                if ($canStyle) {
                    $styleSelector = ElementStylesHelper::getElementStyleSelector($elementID, $styleID);
                    if ($styleSelector !== null) {
                        $classAttributeValue .= ' ' . ElementStylesHelper::getElementStyleClassName($elementID, $styleID);
                    }
                }
                $attributes .= ' class="' . $classAttributeValue . '"';

                $notEditableSelector = $editable ? ':not([data-rvr-editable])' : '';
                $content .= '<style>'
                    . '.bearcms-flexible-box-element{position:relative;box-sizing:border-box;display:flex;flex-direction:column;--css-to-attribute-data-bearcms-flexible-box-direction:' . $defaultLayoutValue['direction'] . ';--css-to-attribute-data-bearcms-flexible-box-alignment:' . $defaultLayoutValue['alignment'] . ';}'
                    . '.bearcms-flexible-box-element' . $notEditableSelector . ':not([class*="bearcms-element-style-"]):has(> div:empty){display:none;}'
                    . '.bearcms-flexible-box-element>a{width:100%;height:100%;position:absolute;top:0;left:0;display:block;cursor:pointer;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-link]' . $notEditableSelector . '>div{pointer-events:none;}'
                    . '.bearcms-flexible-box-element>div{display:flex;flex:1 1 auto;flex-direction:column;gap:var(--bearcms-elements-spacing);}' // Must be here when canStyle=false // flex:1 1 auto; is needed to fill the container when there is height specified
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical-reverse"]>div{flex-direction:column-reverse;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal"]>div{flex-direction:row;flex-wrap:wrap;align-items:flex-start;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal-reverse"]>div{flex-direction:row-reverse;flex-wrap:wrap;align-items:flex-start;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal"]>div>div{min-width:15px;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="horizontal-reverse"]>div>div{min-width:15px;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical"]>div>*{width:100%}' // the default size when elements have margin-left/right=auto;
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-direction="vertical-reverse"]>div>*{width:100%}' // the default size when elements have margin-left/right=auto;
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="start"]>div{justify-content:flex-start;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="center"]>div{justify-content:center;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="end"]>div{justify-content:flex-end;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-between"]>div{justify-content:space-between;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-around"]>div{justify-content:space-around;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-alignment="space-evenly"]>div{justify-content:space-evenly;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="start"]>div{align-items:flex-start;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="center"]>div{align-items:center;}'
                    . '.bearcms-flexible-box-element[data-bearcms-flexible-box-cross-alignment="end"]>div{align-items:flex-end;}'
                    . '</style>';
            }
        }
        if ($styleSelector !== null) {
            $content .= self::getStyleHTML('flexibleBox', $styleValue, $styleSelector, true, !$editable);
        }
        if (!empty($elementTags)) {
            $attributes .= self::getTagsHTMLAttributes($elementTags);
        }
        $content .= '<link rel="client-packages-embed" name="cssToAttributes">'
            . '</head>'
            . '<body>'
            . ($inContainer ? '<div' . $attributes . '>' . $innerContent . '</div>' : $innerContent)
            . ($linkHTML !== null ? $linkHTML : '') // todo why cant be outsite the html??
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
     * @param string $elementType
     * @param array|null $styleValue
     * @param string $selector
     * @param boolean $returnHeadContentOnly
     * @param boolean $optimizeForCompatibility
     * @return string
     */
    static function getStyleHTML(string $elementType, array $styleValue = null, string $selector, bool $returnHeadContentOnly = false, bool $optimizeForCompatibility = false): string
    {
        if (isset(InternalThemes::$elementsOptions[$elementType])) {
            $options = new \BearCMS\Themes\Theme\Options();
            $callback = InternalThemes::$elementsOptions[$elementType];
            if (is_array($callback)) {
                $callback = $callback[1];
            }
            call_user_func($callback, $options, '', $selector, InternalThemes::OPTIONS_CONTEXT_ELEMENT, []);
            if (is_array($styleValue)) {
                $options->setValues($styleValue);
            }
            $htmlData = InternalThemes::getOptionsHTMLData($options->getList(), false, $optimizeForCompatibility);
            $content = InternalThemes::processOptionsHTMLData($htmlData);
            if ($returnHeadContentOnly) {
                $content = substr($content, strpos($content, '<head>') + 6);
                $content = substr($content, 0, strpos($content, '</head>'));
            }
            return $content;
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

            $updateIDAttributeFromRawData = function ($component, bool $setRawAttributeIfMissing) {
                $rawData = (string)$component->getAttribute('bearcms-internal-attribute-raw-data');
                if (strlen($rawData) > 0) {
                    $elementData = InternalDataElements::decodeElementRawData($rawData);
                    if (is_array($elementData)) {
                        $component->id = $elementData['id'];
                    }
                } elseif ($setRawAttributeIfMissing && $component->id !== null && strlen($component->id) > 0) {
                    $elementRawData = InternalDataElements::getElementRawData($component->id);
                    $component->setAttribute('bearcms-internal-attribute-raw-data', $elementRawData);
                }
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
            } elseif (isset(self::$elementsTypeDefinitions[$name])) {
                $component->setAttribute('bearcms-internal-attribute-type', self::$elementsTypeComponents[$name]);
                $component->setAttribute('bearcms-internal-attribute-filename', self::$elementsTypeDefinitions[$name]->componentFilename);
                $updateIDAttributeFromRawData($component, true);
                $updateEditableAttribute($component);
                $updateContextAttributes($component);
                if ($component->canStyle === 'true' && isset(self::$elementsTypeDefinitions[$component->src])) { // Check if element supports styling
                    $canStyle = false;
                    $elementTypeDefinition = self::$elementsTypeDefinitions[$component->src];
                    if ($elementTypeDefinition->canStyle) {
                        $canStyle = true;
                    }
                    if (!$canStyle) {
                        $component->canStyle = 'false';
                    }
                }
            } elseif ($name === 'bearcms-missing-element') {
                $component->setAttribute('bearcms-internal-attribute-type', 'missing');
                $updateIDAttributeFromRawData($component, false);
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
     * @param array $tags
     * @return string
     */
    static function getTagsHTMLAttributes(array $tags): string
    {
        if (empty($tags)) {
            return '';
        }
        return ' data-bearcms-tags="' . htmlentities(implode(' ', $tags)) . '"';
    }
}
