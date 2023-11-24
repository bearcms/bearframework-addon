<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Config;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\ElementsDataHelper;
use BearCMS\Internal\Data\Elements as InternalDataElements;

$app = BearFramework\App::get();
$context = $app->contexts->get(__DIR__);

$lazyLimit = Config::$elementsLazyLoadingOffset;
$contextData = ElementsHelper::getComponentContextData($component);
$editable = $component->editable === 'true';
$group = $component->group;

$containerID = $component->id;
$containerData = InternalDataElements::getContainer($containerID, true);

$elements = $containerData['elements'];
$hasLazyLoading = sizeof($elements) > $lazyLimit;

$columnID = (string) $component->getAttribute('bearcms-internal-attribute-columns-id');
$floatingBoxID = (string) $component->getAttribute('bearcms-internal-attribute-floatingbox-id');
$flexibleBoxID = (string) $component->getAttribute('bearcms-internal-attribute-flexiblebox-id');
$sliderID = (string) $component->getAttribute('bearcms-internal-attribute-slider-id');
$inContainer = $component->getAttribute('bearcms-internal-attribute-container') !== 'none';
$renderElementsContainer = $inContainer && !isset($columnID[0]) && !isset($floatingBoxID[0]) && !isset($flexibleBoxID[0]) && !isset($sliderID[0]);

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
if ($outputType !== 'full-html') {
    $editable = false;
}

$lazyLoadServerData = '';

if (!empty($elements)) {
    if (isset($columnID[0])) {
        $columnsElement = ElementsDataHelper::getContainerDataElement($containerData, $columnID, 'columns');
        $elements = $columnsElement !== null ? [$columnsElement] : [];
    } elseif (isset($floatingBoxID[0])) {
        $floatingBoxElement = ElementsDataHelper::getContainerDataElement($containerData, $floatingBoxID, 'floatingBox');
        $elements = $floatingBoxElement !== null ? [$floatingBoxElement] : [];
    } elseif (isset($flexibleBoxID[0])) {
        $flexibleBoxElement = ElementsDataHelper::getContainerDataElement($containerData, $flexibleBoxID, 'flexibleBox');
        $elements = $flexibleBoxElement !== null ? [$flexibleBoxElement] : [];
    } elseif (isset($sliderID[0])) {
        $sliderElement = ElementsDataHelper::getContainerDataElement($containerData, $sliderID, 'slider');
        $elements = $sliderElement !== null ? [$sliderElement] : [];
    } else if ($hasLazyLoading) {
        $remainingLazyLoadElements = (string) $component->getAttribute('bearcms-internal-attribute-remaining-lazy-load-elements');
        if ($remainingLazyLoadElements === '') {
            $remainingLazyLoadElements = [];
            foreach ($elements as $elementContainerData) {
                $remainingLazyLoadElements[] = $elementContainerData['id'];
            }
        } else {
            $remainingLazyLoadElements = explode(',', $remainingLazyLoadElements);
        }
        $tempElements = [];
        foreach ($elements as $elementContainerData) {
            $remainingLazyLoadElementIndex = array_search($elementContainerData['id'], $remainingLazyLoadElements);
            if ($remainingLazyLoadElementIndex === false) {
                continue;
            }
            $tempElements[] = $elementContainerData;
            $elementsToLoad[] = $elementContainerData['id'];
            unset($remainingLazyLoadElements[$remainingLazyLoadElementIndex]);
            if (sizeof($tempElements) >= $lazyLimit) {
                break;
            }
        }
        $elements = $tempElements;
        unset($tempElements);
        if (!empty($remainingLazyLoadElements)) {
            $loadMoreComponent = clone ($component);
            $loadMoreComponent->setAttribute('bearcms-internal-attribute-remaining-lazy-load-elements', implode(',', $remainingLazyLoadElements));
            $loadMoreComponent->setAttribute('bearcms-internal-attribute-container', 'none');
            $lazyLoadServerData = \BearCMS\Internal\TempClientData::set(['componentHTML' => (string) $loadMoreComponent]);
            ElementsHelper::$lastLoadMoreServerData = $lazyLoadServerData;
        }
    }
}

$styles = '';

if ($renderElementsContainer) {
    $spacing = (string)$component->spacing;
    if ($spacing === '') {
        $spacing = '1rem';
    }
    $width = (string)$component->width;
    if ($width === '') {
        $width = '100%';
    }
    $className = 'bre' . md5($spacing . '$' . $width);
    $attributes = '';
    if ($editable) {
        $htmlElementID = 'brela' . md5($containerID);
        ElementsHelper::$editorData[] = ['container', $containerID, $contextData, $group];
        $attributes .= ' id="' . $htmlElementID . '"';
    } else {
        ElementsHelper::$renderedData[] = ['container', $containerID];
    }

    $styles .= '.' . $className . '{--bearcms-elements-spacing:' . $spacing . ';width:' . $width . ';text-align:left;display:flex;flex-direction:column;gap:var(--bearcms-elements-spacing);}';
    $styles .= '.' . $className . '>*{width:100%;}'; // the default size when elements have margin-left/right=auto;

    if ($outputType === 'full-html') {
        $componentClass = (string)$component->class;
        $attributes .= ' class="bearcms-elements ' . $className . (strlen($componentClass) > 0 ? ' ' . $componentClass : '') . '"';
    }

    if ($hasLazyLoading && isset($lazyLoadServerData[0])) {
        $attributes .= ' data-bearcms-elements-lazy-load="' . htmlentities($lazyLoadServerData) . '"';
    }
}
echo '<html>';

if ($outputType === 'full-html') {
    echo '<head>';
    if ($renderElementsContainer) {
        echo '<style>' . $styles . '</style>';
        if ($hasLazyLoading) {
            echo '<link rel="client-packages-prepare" name="-bearcms-elements-lazy-load">';
            echo '<script>clientPackages.get(\'-bearcms-elements-lazy-load\')</script>';
        }
    }
    echo '</head>';
}

echo '<body>';
if ($renderElementsContainer) {
    if ($editable) {
        echo '<div>';
    }
    echo '<div' . $attributes . '>';
}

if (!empty($elements)) {
    $childrenContextData = $contextData;
    $childrenContextData['inElementsContainer'] = '1';
    if (isset($columnID[0])) {
        echo ElementsHelper::renderColumns($elements[0], $editable, $childrenContextData, $inContainer, $outputType);
    } elseif (isset($floatingBoxID[0])) {
        echo ElementsHelper::renderFloatingBox($elements[0], $editable, $childrenContextData, $inContainer, $outputType);
    } elseif (isset($flexibleBoxID[0])) {
        echo ElementsHelper::renderFlexibleBox($elements[0], $editable, $childrenContextData, $inContainer, $outputType);
    } elseif (isset($sliderID[0])) {
        echo ElementsHelper::renderSlider($elements[0], $editable, $childrenContextData, $inContainer, $outputType);
    } else {
        echo ElementsHelper::renderContainerElements($elements, $editable, $childrenContextData, $outputType);
    }
}
if ($renderElementsContainer) {
    echo '</div>';
    if ($editable) {
        echo '</div>';
    }
}
echo '</body></html>';
