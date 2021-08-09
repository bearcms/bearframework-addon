<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use IvoPetkov\HTML5DOMDocument;

$app = App::get();

$selectedPath = '';
if (strlen($component->selectedPath) > 0) {
    $selectedPath = $component->selectedPath;
}

$menuType = 'list-vertical';
if (strlen($component->menuType) > 0) {
    if (array_search($component->menuType, ['horizontal-down', 'vertical-left', 'vertical-right', 'list-vertical']) !== false) {
        $menuType = $component->menuType;
    }
}

$attributes = '';
$attributes .= ' type="' . $menuType . '"';
if (strlen($component->class) > 0) {
    $attributes .= ' class="' . htmlentities($component->class) . '"';
}
$attributes .= ' moreItemHtml="' . htmlentities('<li class="bearcms-navigation-element-item bearcms-navigation-element-item-more"><a></a><ul class="bearcms-navigation-element-item-children"></ul></li>') . '"';

$dataResponsiveAttributes = $component->getAttribute('data-responsive-attributes');
if (strlen($dataResponsiveAttributes) > 0) {
    $attributes .= ' data-responsive-attributes="' . htmlentities(str_replace('=>menuType=', '=>type=', $dataResponsiveAttributes)) . '"';
}

$showStoreCartButton = false;

$itemsHtml = (string) $component->innerHTML;
if (isset($itemsHtml[0])) {
    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($itemsHtml, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
    $ulElements = $domDocument->querySelectorAll('ul');
    foreach ($ulElements as $index => $ulElement) {
        $ulElement->setAttribute('class', trim($ulElement->getAttribute('class') . ' ' . ($index === 0 ? 'bearcms-navigation-element' : 'bearcms-navigation-element-item-children')));
    }
    $liElements = $domDocument->querySelectorAll('li');
    $requestBase = $app->request->base;
    foreach ($liElements as $index => $liElement) {
        $liClasssName = 'bearcms-navigation-element-item';
        if ($liElement->firstChild) {
            $liPath = str_replace($requestBase, '', $liElement->firstChild->getAttribute('href'));
            if ($liPath === $selectedPath) {
                $liClasssName .= ' bearcms-navigation-element-item-selected';
            } elseif ($liPath !== '/' && strpos($selectedPath, $liPath) === 0) {
                $liClasssName .= ' bearcms-navigation-element-item-in-path';
            }
        }
        $liElement->setAttribute('class', trim($liElement->getAttribute('class') . ' ' . $liClasssName));
    }
    $rootULElement = $domDocument->querySelector('ul');
    if ($rootULElement) {
        $itemsHtml = $rootULElement->outerHTML;
    }
} else {
    $source = 'topPages';
    if (strlen($component->source) > 0 && array_search($component->source, ['allPages', 'pageChildren', 'topPages', 'pageAllChildren']) !== false) {
        $source = $component->source;
    }

    $showHomeLink = false;
    $sourceParentPageID = null;
    if ($source === 'pageChildren' || $source === 'pageAllChildren') {
        $sourceParentPageID = (string) $component->sourceParentPageID;
    } elseif ($source === 'allPages' || $source === 'topPages') {
        $showHomeLink = $component->showHomeLink === 'true';
        $homeLinkText = strlen($component->homeLinkText) > 0 ? $component->homeLinkText : __('bearcms.navigation.home');
        if ($app->bearCMS->addons->exists('bearcms/store-addon')) {
            $showStoreCartButton = $component->showStoreCartButton === 'true';
        }
    }

    $itemsType = (string) $component->itemsType === 'onlySelected' ? 'onlySelected' : 'allExcept';
    $items = strlen($component->items) > 0 ? explode(';', $component->items) : [];

    $dataKey = md5(json_encode([$source, $sourceParentPageID, $itemsType, $items]));

    $optimizedPages = null;

    $requestBase = $app->request->base;

    $cacheKey = 'bearcms-navigation-' . $dataKey;
    $tempDataKey = '.temp/bearcms/navigation-element-cache/' . $dataKey;
    $updateCache = false;

    $optimizedPages = $app->cache->getValue($cacheKey);
    if ($optimizedPages !== null) {
        $optimizedPages = json_decode($optimizedPages, true);
    }
    if (!is_array($optimizedPages)) {
        $optimizedPages = $app->data->getValue($tempDataKey);
        if ($optimizedPages !== null) {
            $optimizedPages = json_decode($optimizedPages, true);
        }
        $updateCache = true;
    }
    if (!is_array($optimizedPages)) {
        $appURLs = $app->urls;
        $optimizePages = function ($pages, $recursive = false) use (&$optimizePages, $itemsType, $items, $appURLs, $requestBase) {
            $result = [];
            foreach ($pages as $page) {
                if ($page->id === 'home') {
                    continue;
                }
                if ($page->status !== 'public') {
                    continue;
                }
                $pageID = $page->id;
                if ($itemsType === 'allExcept' && array_search($pageID, $items) !== false) {
                    continue;
                }
                if ($itemsType === 'onlySelected' && array_search($pageID, $items) === false) {
                    continue;
                }
                $pagePath = $page->path;
                $item = [
                    0 => $page->path,
                    1 => '<a href="' . htmlentities(str_replace($requestBase, '', $appURLs->get($pagePath))) . '">' . htmlspecialchars($page->name) . '</a>'
                ];
                if ($recursive && isset($page->children)) {
                    $children = $optimizePages($page->children, true);
                    if (!empty($children)) {
                        $item[2] = $children;
                    }
                }
                $result[] = $item;
            }
            return $result;
        };
        $pages = null;
        if ($source === 'topPages' || $source === 'allPages') {
            $pages = \BearCMS\Internal\Data\Pages::getChildrenList(null); // Used instead of $app->bearCMS->data->pages->getList() for better performance
        } elseif ($source === 'pageChildren' || $source === 'pageAllChildren') {
            $pages = \BearCMS\Internal\Data\Pages::getChildrenList($sourceParentPageID); // Used instead of $app->bearCMS->data->pages->getList() for better performance
        }
        $optimizedPages = $pages !== null ? $optimizePages($pages, $source === 'allPages' || $source === 'pageAllChildren') : [];
        $app->data->setValue($tempDataKey, json_encode($optimizedPages));
        $updateCache = true;
    }
    if ($updateCache) {
        $app->cache->set($app->cache->make($cacheKey, json_encode($optimizedPages)));
    }
    if ($showHomeLink) {
        array_unshift($optimizedPages, [0 => '/', 1 => '<a href="/">' . htmlspecialchars($homeLinkText) . '</a>']);
    }

    if ($showStoreCartButton) {
        $optimizedPages[] = [0 => null, 1 => '<a href="javascript:void(0);" onclick="bearCMS.store.openCart();"></a>', 3 => true, 'bearcms-navigation-element-item bearcms-navigation-element-item-store-cart'];
    }

    if ($optimizedPages === null || empty($optimizedPages)) {
        $itemsHtml = '';
    } else {
        $buildTree = function ($optimizedPages, $level = 0) use ($selectedPath, &$buildTree) {
            $itemsHtml = [];
            foreach ($optimizedPages as $page) {
                $pagePath = $page[0];
                $alwaysVisible = isset($page[3]) && $page[3];
                $classNames = isset($page[4]) ? $page[4] : 'bearcms-navigation-element-item';
                if ($pagePath !== null) {
                    if ($pagePath === $selectedPath) {
                        $classNames .= ' bearcms-navigation-element-item-selected';
                    } elseif ($pagePath !== '/' && strpos($selectedPath, $pagePath) === 0) {
                        $classNames .= ' bearcms-navigation-element-item-in-path';
                    }
                }
                $itemsHtml[] = '<li class="' . $classNames . '"' . ($alwaysVisible ? ' data-navigation-visible="always"' : '') . '>' . $page[1];
                if (isset($page[2])) {
                    $itemsHtml[] = $buildTree($page[2], $level + 1);
                }
                $itemsHtml[] = '</li>';
            }
            if (empty($itemsHtml)) {
                return '';
            }
            return '<ul class="' . ($level === 0 ? 'bearcms-navigation-element' : 'bearcms-navigation-element-item-children') . '">' . implode('', $itemsHtml) . '</ul>';
        };
        $itemsHtml = str_replace('<a href="', '<a href="' . $requestBase, $buildTree($optimizedPages));
    }
}

$content = '';
if (isset($itemsHtml[0])) {
    $content = '<component src="navigation-menu"' . $attributes . '>' . $itemsHtml . '</component>';
}
echo '<html><head>';
if ($showStoreCartButton) {
    echo '<link rel="client-packages-embed" name="-bearcms-store">';
}
echo '<style>';
echo '.bearcms-navigation-element-item{word-break:break-word;}';
echo '</style></head><body>';
echo $content;
echo '</body></html>';
