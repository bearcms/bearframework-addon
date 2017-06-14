<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\ElementsHelper;

$app = BearFramework\App::get();
$context = $app->context->get(__FILE__);

$lazyLimit = 70;
$contextData = ElementsHelper::getComponentContextData($component);
$editable = $component->editable === 'true';
$group = $component->group;

$containerData = ElementsHelper::getContainerData($component->id);

$elements = $containerData['elements'];
$hasLazyLoading = sizeof($elements) > $lazyLimit;

$columnID = (string) $component->getAttribute('bearcms-internal-attribute-columns-id');
$floatingBoxID = (string) $component->getAttribute('bearcms-internal-attribute-floatingbox-id');
$inContainer = $component->getAttribute('bearcms-internal-attribute-container') !== 'none';
$renderElementsContainer = $inContainer && !isset($columnID{0}) && !isset($floatingBoxID{0});

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
    if (isset($columnID{0})) {
        foreach ($elements as $elementContainerData) {
            if ($elementContainerData['id'] === $columnID) {
                $elements = [$elementContainerData];
                break;
            }
        }
    } elseif (isset($floatingBoxID{0})) {
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
    $elementsRawData = ElementsHelper::getElementsRawData($elementsIDs);
}

if ($hasLazyLoading) {
    $lazyLoadServerData = '';
    if (!empty($remainingLazyLoadElements)) {
        $loadMoreComponent = clone($component);
        $loadMoreComponent->setAttribute('bearcms-internal-attribute-remaining-lazy-load-elements', implode(',', $remainingLazyLoadElements));
        $loadMoreComponent->setAttribute('bearcms-internal-attribute-container', 'none');
        $lazyLoadServerData = \BearCMS\Internal\TempClientData::set(['componentHTML' => (string) $loadMoreComponent]);
        ElementsHelper::$lastLoadMoreServerData = $lazyLoadServerData;
    }
}

$styles = '';

if ($renderElementsContainer) {
    $className = 'bre' . md5(uniqid());
    $attributes = '';
    if ($editable) {
        $htmlElementID = 'brela' . md5($component->id);
        ElementsHelper::$editorData[] = ['container', $component->id, $contextData, $group];
        $attributes .= ' id="' . $htmlElementID . '"';
    }

    $styles .= '.' . $className . '{width:' . $component->width . ';word-wrap:break-word;text-align:left;}';
    $styles .= '.' . $className . '>div{margin-bottom:' . $component->spacing . ';display:block;clear:both;zoom:1;}';
    $styles .= '.' . $className . '>div:last-child{margin-bottom:0;}';
    $styles .= '.' . $className . '>div:empty{display:none;}';

    $spacingSelector = 's' . $component->spacing;
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div:empty{display:none;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div{display:inline-block;vertical-align:top;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div{margin-bottom:' . $component->spacing . ';display:block;clear:both;zoom:1;}';
    $styles .= '.' . $className . '>[data-srvri~="t2"][data-srvri~="' . $spacingSelector . '"]>div>div:last-child{margin-bottom:0;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div:empty{display:none;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div{margin-bottom:' . $component->spacing . ';display:block;zoom:1;}';
    $styles .= '.' . $className . '>[data-srvri~="t3"][data-srvri~="' . $spacingSelector . '"]>div>div:last-child{margin-bottom:0;}';

    $attributes .= ' class="bearcms-elements ' . $className . (strlen($component->class) > 0 ? ' ' . $component->class : '') . '"';

    if ($hasLazyLoading && isset($lazyLoadServerData[0])) {
        $attributes .= ' data-bearcms-elements-lazy-load="' . htmlentities($lazyLoadServerData) . '"';
    }
}
?><html>
    <head><?php
        if ($renderElementsContainer) {
            echo '<style>' . $styles . '</style>';
            if ($hasLazyLoading) {
                $lazyLoadInitializeData = [];
                $lazyLoadInitializeData[] = __('bearcms.elements.LoadingMore');
                echo '<script id="bearcms-bearframework-addon-script-3" src="' . htmlentities($context->assets->getUrl('assets/elementsLazyLoad.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '" async onload="' . htmlentities('bearCMS.elementsLazyLoad.initialize(' . json_encode($lazyLoadInitializeData) . ');') . '"></script>';
                echo '<script id="bearcms-bearframework-addon-script-4" src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '" async></script>';
            }
        }
        ?></head>
    <body><?php
        if ($renderElementsContainer) {
            if ($editable) {
                echo '<div>';
            }
            echo '<div' . $attributes . '>';
        }
        if (!empty($elements)) {
            $childrenContextData = $contextData;
            $childrenContextData['width'] = '100%';
            foreach ($elements as $elementContainerData) {
                if ($hasLazyLoading && array_search($elementContainerData['id'], $elementsToLoad) === false) {
                    continue;
                }
                if (isset($elementContainerData['data'], $elementContainerData['data']['type']) && ($elementContainerData['data']['type'] === 'column' || $elementContainerData['data']['type'] === 'columns')) {
                    echo ElementsHelper::renderColumn($elementContainerData, $editable, $childrenContextData, !(isset($columnID{0}) && !$inContainer));
                } elseif (isset($elementContainerData['data'], $elementContainerData['data']['type']) && $elementContainerData['data']['type'] === 'floatingBox') {
                    echo ElementsHelper::renderFloatingBox($elementContainerData, $editable, $childrenContextData, !(isset($floatingBoxID{0}) && !$inContainer));
                } else {
                    echo ElementsHelper::renderElement($elementsRawData[$elementContainerData['id']], $editable, $childrenContextData);
                }
            }
        }
        if ($renderElementsContainer) {
            echo '</div>';
            if ($editable) {
                echo '</div>';
            }
        }
        ?></body>
</html>