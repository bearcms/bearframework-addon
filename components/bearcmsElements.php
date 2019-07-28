<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal;

$app = BearFramework\App::get();
$context = $app->contexts->get(__FILE__);

$lazyLimit = 70;
$contextData = Internal\ElementsHelper::getComponentContextData($component);
$editable = $component->editable === 'true';
$group = $component->group;

$containerData = Internal\ElementsHelper::getContainerData($component->id);

$elements = $containerData['elements'];
$hasLazyLoading = sizeof($elements) > $lazyLimit;

$columnID = (string) $component->getAttribute('bearcms-internal-attribute-columns-id');
$floatingBoxID = (string) $component->getAttribute('bearcms-internal-attribute-floatingbox-id');
$inContainer = $component->getAttribute('bearcms-internal-attribute-container') !== 'none';
$renderElementsContainer = $inContainer && !isset($columnID[0]) && !isset($floatingBoxID[0]);

$outputType = (string) $component->getAttribute('output-type');
$outputType = isset($outputType[0]) ? $outputType : 'full-html';
if ($outputType !== 'full-html') {
    $editable = false;
}

if ($hasLazyLoading) {
    $remainingLazyLoadElements = (string) $component->getAttribute('bearcms-internal-attribute-remaining-lazy-load-elements');
    if ($remainingLazyLoadElements === '') {
        $remainingLazyLoadElements = [];
        foreach ($elements as $elementContainerData) {
            $remainingLazyLoadElements[] = $elementContainerData['id'];
        }
    } else {
        $remainingLazyLoadElements = explode(',', $remainingLazyLoadElements);
    }
    $elementsToLoad = [];
}
if (empty($elements)) {
    $elementsRawData = [];
} else {
    $elementsIDs = [];
    if (isset($columnID[0])) {
        foreach ($elements as $elementContainerData) {
            if ($elementContainerData['id'] === $columnID) {
                $elements = [$elementContainerData];
                break;
            }
        }
    } elseif (isset($floatingBoxID[0])) {
        foreach ($elements as $elementContainerData) {
            if ($elementContainerData['id'] === $floatingBoxID) {
                $elements = [$elementContainerData];
                break;
            }
        }
    } else {
        if ($hasLazyLoading) {
            $lazyElementsCounter = 0;
        }
        foreach ($elements as $elementContainerData) {
            if ($hasLazyLoading) {
                $elementToLoadIndex = array_search($elementContainerData['id'], $remainingLazyLoadElements);
                if ($elementToLoadIndex === false) {
                    continue;
                }
            }
            if (isset($elementContainerData['data'], $elementContainerData['data']['type']) && ($elementContainerData['data']['type'] === 'column' || $elementContainerData['data']['type'] === 'columns')) {
                // columns element
            } elseif (isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'floatingBox') {
                // floating box element
            } else {
                $elementsIDs[] = $elementContainerData['id'];
            }
            if ($hasLazyLoading) {
                $elementsToLoad[] = $elementContainerData['id'];
                unset($remainingLazyLoadElements[$elementToLoadIndex]);
                $lazyElementsCounter++;
                if ($lazyElementsCounter >= $lazyLimit) {
                    break;
                }
            }
        }
    }
    $elementsRawData = Internal\ElementsHelper::getElementsRawData($elementsIDs);
}

if ($hasLazyLoading) {
    $lazyLoadServerData = '';
    if (!empty($remainingLazyLoadElements)) {
        $loadMoreComponent = clone ($component);
        $loadMoreComponent->setAttribute('bearcms-internal-attribute-remaining-lazy-load-elements', implode(',', $remainingLazyLoadElements));
        $loadMoreComponent->setAttribute('bearcms-internal-attribute-container', 'none');
        $lazyLoadServerData = \BearCMS\Internal\TempClientData::set(['componentHTML' => (string) $loadMoreComponent]);
        Internal\ElementsHelper::$lastLoadMoreServerData = $lazyLoadServerData;
    }
}

$styles = '';

if ($renderElementsContainer) {
    $spacing = $component->spacing;
    $width = $component->width;
    $className = 'bre' . md5($spacing . '$' . $width);
    $attributes = '';
    if ($editable) {
        $htmlElementID = 'brela' . md5($component->id);
        Internal\ElementsHelper::$editorData[] = ['container', $component->id, $contextData, $group];
        $attributes .= ' id="' . $htmlElementID . '"';
    }

    $styles .= '.' . $className . '{width:' . $width . ';text-align:left;}';
    $styles .= '.' . $className . '>div{margin-bottom:' . $spacing . ';display:block;clear:both;zoom:1;}';
    $styles .= '.' . $className . '>div:last-child{margin-bottom:0;}';
    $styles .= '.' . $className . '>div:empty{display:none;}';

    $spacingSelector = 's' . $spacing;
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div:empty{display:none;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div{display:inline-block;vertical-align:top;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div{margin-bottom:' . $spacing . ';display:block;clear:both;zoom:1;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div:last-child{margin-bottom:0;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div:empty{display:none;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div{margin-bottom:' . $spacing . ';display:block;zoom:1;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div:last-child{margin-bottom:0;}';

    if ($outputType === 'full-html') {
        $attributes .= ' class="bearcms-elements ' . $className . (strlen($component->class) > 0 ? ' ' . $component->class : '') . '"';
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
    $childrenContextData['width'] = '100%';
    $childrenContextData['inElementsContainer'] = '1';
    foreach ($elements as $elementContainerData) {
        if ($hasLazyLoading && array_search($elementContainerData['id'], $elementsToLoad) === false) {
            continue;
        }
        if (isset($elementContainerData['data'], $elementContainerData['data']['type']) && ($elementContainerData['data']['type'] === 'column' || $elementContainerData['data']['type'] === 'columns')) {
            echo Internal\ElementsHelper::renderColumn($elementContainerData, $editable, $childrenContextData, !(isset($columnID[0]) && !$inContainer), $outputType);
        } elseif (isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'floatingBox') {
            echo Internal\ElementsHelper::renderFloatingBox($elementContainerData, $editable, $childrenContextData, !(isset($floatingBoxID[0]) && !$inContainer), $outputType);
        } else {
            $elementRawData = $elementsRawData[$elementContainerData['id']];
            if ($elementRawData !== null) {
                echo Internal\ElementsHelper::renderElement($elementRawData, $editable, $childrenContextData, $outputType);
            }
        }
    }
}
if ($renderElementsContainer) {
    echo '</div>';
    if ($editable) {
        echo '</div>';
    }
}
echo '</body>';
echo '</html>';
