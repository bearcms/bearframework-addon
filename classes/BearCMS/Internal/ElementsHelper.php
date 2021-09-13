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

        if ($component->canStyle === 'true') {
            if (isset(self::$elementsTypesOptions[$component->src])) { // Check if element supports styling
                $canStyle = false;
                $elementTypeOptions = self::$elementsTypesOptions[$component->src];
                if (isset($elementTypeOptions['canStyle']) && $elementTypeOptions['canStyle']) {
                    $canStyle = true;
                }
                if (!$canStyle) {
                    $component->canStyle = 'false';
                }
            }
        }
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
        if ($component->getAttribute('canStyle', '') === '') {
            $currentThemeID = Internal\CurrentTheme::getID();
            $theme = Internal\Themes::get($currentThemeID);
            if ($theme !== null) { // just in case it's registered later or other
                if ($theme->canStyleElements) {
                    $component->setAttribute('canStyle', 'true');
                }
            }
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
        $elementContainerData = ElementsHelper::getUpdatedStructuralElementData($elementContainerData);
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
            } elseif ($outputType === 'simple-html') {
                $innerContent .= '<div>' . $columnContent . '</div>';
            }
        }

        $styles = '';
        if ($inContainer) {
            $attributes = '';
            if ($editable) {
                $htmlElementID = 'brelb' . md5($elementContainerData['id']);
                $attributes .= ' id="' . $htmlElementID . '"';
                ElementsHelper::$editorData[] = ['columns', $elementContainerData['id'], $contextData];
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
                . (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="-bearcms-responsive-attributes">' : '')
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
        $elementContainerData = ElementsHelper::getUpdatedStructuralElementData($elementContainerData);
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
        } elseif ($outputType === 'simple-html') {
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
                ElementsHelper::$editorData[] = ['floatingBox', $elementContainerData['id'], $contextData];
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
            $content .= (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="-bearcms-responsive-attributes">' : '');
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
        $elementContainerData = ElementsHelper::getUpdatedStructuralElementData($elementContainerData);
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
                    ElementsHelper::$editorData[] = ['flexibleBox', $elementContainerData['id'], $contextData];
                }
            }

            if ($outputType === 'full-html') {
                $className = 'bre' . md5('flexiblebox$' . (isset($elementContainerData['id']) ? $elementContainerData['id'] : uniqid()));
                $classAttributeValue = 'bearcms-elements-element-container bearcms-elements-flexible-box ' . $className;

                if ($hasElementStyle) {
                    $styleClassName = 'bearcms-elements-element-style-' . md5($elementContainerData['id']);
                    $classAttributeValue .= ' ' . $styleClassName;
                    if (isset($elementStyle['css'])) {
                        $innerContent .= ElementsHelper::getElementStyleHTML('flexibleBox', $elementStyle, '#' . $htmlElementID . '.' . $styleClassName);
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
                . (($editable || $autoVerticalWidthInPx !== null) ? '<link rel="client-packages-embed" name="-bearcms-responsive-attributes">' : '')
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
        $elementsRawData = self::getElementsRawData($elementsIDs);
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
    static function isColumnsElementContainerData(array $elementContainerData): bool
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
    static function isFloatingBoxElementContainerData(array $elementContainerData): bool
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
    static function isFlexibleBoxElementContainerData(array $elementContainerData): bool
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
    static function isStructuralElementContainerData(array $elementContainerData): bool
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
     * @param array $elementsIDs
     * @return array
     */
    static function getElementsRawData(array $elementsIDs): array
    {
        $result = [];
        $elementsIDs = array_values($elementsIDs);
        foreach ($elementsIDs as $elementID) {
            $result[$elementID] = Internal\Data::getValue(self::getElementDataKey($elementID));
        }
        return $result;
    }

    /**
     * 
     * @param string $elementID
     * @return array|null
     */
    static function getElementData(string $elementID): ?array
    {
        $data = self::getElementsRawData([$elementID]);
        return $data[$elementID] !== null ? self::decodeElementRawData($data[$elementID]) : null;
    }

    /**
     * 
     * @param string $elementID
     * @param array $data
     * @return void
     */
    static function setElementData(string $elementID, array $data): void
    {
        $app = App::get();
        $app->data->setValue('bearcms/elements/element/' . md5($elementID) . '.json', json_encode($data));
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
        $elementData = self::getElementData($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
            $htmlElementID = 'brelc' . md5($elementData['id']);
        }
        if ($elementType === null) {
            $containerData = self::getContainerData($containerID);
            $elementData = self::getStructuralElement($containerData, $elementID);
            if (is_array($elementData)) {
                $elementType = $elementData['type'];
                $elementStyle = isset($elementData['style']) ? $elementData['style'] : [];
                $htmlElementID = 'brelb' . md5($elementData['id']);
            }
        }
        if ($elementType !== null) {
            if (isset(Internal\Themes::$elementsOptions[$elementType])) {
                $app = App::get();
                $previousLocale = $app->localization->getLocale();
                if ($previousLocale !== Config::$language) {
                    $app->localization->setLocale(Config::$language);
                } else {
                    $previousLocale = null;
                }
                if ($elementType === 'flexibleBox') {
                    $themeID = null;
                    $themeOptionsSelectors = null;
                } else {
                    $themeID = Internal\Themes::getActiveThemeID();
                    $themeOptionsSelectors = Internal\Themes::getElementsOptionsSelectors($themeID, $elementType);
                }
                $options = new \BearCMS\Themes\Theme\Options();
                call_user_func(Internal\Themes::$elementsOptions[$elementType], $options, 'ElementStyle', '#' . $htmlElementID . '.bearcms-elements-element-style-' . md5($elementID), Internal\Themes::OPTIONS_CONTEXT_ELEMENT);
                $values = [];
                foreach ($elementStyle as $name => $value) {
                    $values['ElementStyle' . $name] = $value;
                }
                if ($previousLocale !== null) {
                    $app->localization->setLocale($previousLocale);
                }
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
    static function setElementStyleOptionsValues(string $containerID, string $elementID, array $values)
    {
        $app = App::get();
        $elementType = null;
        $isStructural = false;
        $elementData = self::getElementData($elementID);
        if (is_array($elementData) && isset($elementData['type'])) {
            $elementType = $elementData['type'];
            $oldElementStyle = isset($elementData['style']) ? $elementData['style'] : [];
        }
        if ($elementType === null) {
            $containerData = self::getContainerData($containerID);
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
            $filesInNewStyle = Internal\Themes::getFilesInValues($newElementStyle);
            $filesKeysToUpdate = [];
            foreach ($filesInNewStyle as $key) {
                if (strpos($key, 'data:') === 0) {
                    $dataKay = substr($key, 5);
                    if (strpos($dataKay, '.temp/bearcms/files/elementstyleimage/') === 0) {
                        $newDataKey = 'bearcms/files/elementstyleimage/' . pathinfo($dataKay, PATHINFO_BASENAME);
                        $app->data->duplicate($dataKay, $newDataKey);
                        UploadsSize::add($newDataKey, filesize($app->data->getFilename($newDataKey)));
                        $filesKeysToUpdate['data:' . $dataKay] = 'data:' . $newDataKey;
                        $filesToDelete[] = $key;
                    }
                }
            }
            $filesToDelete = array_merge($filesToDelete, array_diff($filesInOldStyle, $filesInNewStyle));
            $newElementStyle = Internal\Themes::updateFilesInValues($newElementStyle, $filesKeysToUpdate);
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
                self::setContainerData($containerID, $containerData);
            } else {
                $elementData['style'] = $newElementStyle;
                if (empty($elementData['style'])) {
                    unset($elementData['style']);
                }
                self::setElementData($elementID, $elementData);
            }
            self::deleteElementStyleFiles($filesToDelete);
        }
    }

    static function getElementStyleHTML(string $elementType, array $elementStyleData, string $cssSelector): string
    {
        if (isset(Internal\Themes::$elementsOptions[$elementType])) {
            $options = new \BearCMS\Themes\Theme\Options();
            call_user_func(Internal\Themes::$elementsOptions[$elementType], $options, '', $cssSelector, Internal\Themes::OPTIONS_CONTEXT_ELEMENT);
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
     * @return void
     */
    static function deleteElement(string $elementID): void
    {
        $app = App::get();
        $elementData = self::getElementData($elementID);
        if ($elementData !== null) {
            $app->data->delete(self::getElementDataKey($elementID));
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
     * @param array $fileKeys
     * @return void
     */
    static private function deleteElementStyleFiles(array $fileKeys): void
    {
        if (!empty($fileKeys)) {
            $app = App::get();
            $recycleBinPrefix = '.recyclebin/bearcms/element-style-changes-' . str_replace('.', '-', microtime(true)) . '/';
            foreach ($fileKeys as $fileKey) {
                if (substr($fileKey, 0, 5) === 'data:') {
                    $dataKay = substr($fileKey, 5);
                    if ($app->data->exists($dataKay)) {
                        $app->data->rename($dataKay, $recycleBinPrefix . $dataKay);
                    }
                    UploadsSize::remove($dataKay);
                }
            }
        }
    }

    /**
     * 
     * @param string $elementID
     * @return string
     */
    static function getElementDataKey(string $elementID): string
    {
        return 'bearcms/elements/element/' . md5($elementID) . '.json';
    }

    /**
     * 
     * @param string $id
     * @return array
     * @throws \Exception
     */
    static function getContainerData(string $id): array
    {
        $container = Internal\Data::getValue(self::getContainerDataKey($id));
        $data = $container !== null ? json_decode($container, true) : [];
        if (!isset($data['elements'])) {
            $data['elements'] = [];
        }
        if (!is_array($data['elements'])) {
            throw new \Exception('');
        }
        return $data;
    }

    /**
     * 
     * @param string $id
     * @param array $data
     */
    static function setContainerData(string $id, array $data): void
    {
        $app = App::get();
        $app->data->setValue(self::getContainerDataKey($id), json_encode($data));
    }

    /**
     * 
     * @param string $id
     * @return string
     */
    static function getContainerDataKey(string $id): string
    {
        return 'bearcms/elements/container/' . md5($id) . '.json';
    }

    /**
     * Returns a list of element IDs in rendered order (from top left)
     * @param string $id
     * @return array
     */
    static function getContainerElementsIDs(string $id): array
    {
        $containerData = self::getContainerData($id);
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

    static function getLastModifiedDetails(string $elementsContainerID): array
    {
        $dates = [];
        $dataKeys = [];
        $dataKeys[] = ElementsHelper::getContainerDataKey($elementsContainerID);
        $containerData = ElementsHelper::getContainerData($elementsContainerID);
        if (is_array($containerData)) {
            if (isset($containerData['lastChangeTime'])) {
                $dates[] = $containerData['lastChangeTime'];
            }
            $elementsIDs = ElementsHelper::getContainerElementsIDs($elementsContainerID);
            foreach ($elementsIDs as $elementID) {
                $dataKeys[] = ElementsHelper::getElementDataKey($elementID);
                $elementData = ElementsHelper::getElementData($elementID);
                if (is_array($elementData)) {
                    if (isset($elementData['lastChangeTime'])) {
                        $dates[] = $elementData['lastChangeTime'];
                    }
                }
            }
        }
        return ['dates' => $dates, 'dataKeys' => $dataKeys];
    }

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

    static function setStructuralElement(array $containerData, array $newElementData)
    {
        $elementID = $newElementData['id'];
        $walkElements = function ($elements) use (&$walkElements, $elementID, $newElementData) {
            foreach ($elements as $index => $elementData) {
                $structuralElementData = self::getUpdatedStructuralElementData($elementData);
                if ($structuralElementData !== null) {
                    if ($structuralElementData['id'] === $elementID) {
                        $elements[$index] = $newElementData;
                        return $elements;
                    }
                    if ($structuralElementData['type'] === 'columns') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $i => $columnElements) {
                                $elements[$index]['elements'][$i] = $walkElements($columnElements);
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'floatingBox') {
                        if (isset($structuralElementData['elements'])) {
                            foreach ($structuralElementData['elements'] as $i => $boxElements) {
                                $elements[$index]['elements'][$i] = $walkElements($boxElements);
                            }
                        }
                    } elseif ($structuralElementData['type'] === 'flexibleBox') {
                        if (isset($structuralElementData['elements'])) {
                            $elements[$index]['elements'] = $walkElements($structuralElementData['elements']);
                        }
                    }
                }
            }
            return $elements;
        };
        $containerData['elements'] = $walkElements($containerData['elements']);
        return $containerData;
    }

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
}
