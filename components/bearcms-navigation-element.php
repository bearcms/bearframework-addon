<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

//$moreElementHtml = '<li class="bearcms-navigation-element-item bearcms-navigation-element-item-more"><a>&nbsp;</a><ul class="bearcms-navigation-element-item-children"></ul><li>';

$buildTreeFunction = function($pages, $parentID) use ($app, &$buildTreeFunction) {
    $items = [];
    $currentPath = (string) $app->request->path;
    foreach ($pages as $page) {
        if ($page['parentID'] === $parentID) {
            if (BearCMS\CurrentUser::exists() || (!BearCMS\CurrentUser::exists() && $page['status'] === 'published')) {
                $classNames = 'bearcms-navigation-element-item';
                if ($page['path'] === $currentPath) {
                    $classNames .= ' bearcms-navigation-element-item-selected';
                } elseif ($page['id'] !== '_home' && strpos($currentPath, $page['path']) === 0) {
                    $classNames .= ' bearcms-navigation-element-item-in-path';
                }
                $items[] = '<li class="' . $classNames . '"><a href="' . $app->request->base . $page['path'] . '">' . htmlspecialchars($page['name']) . '</a>' . $buildTreeFunction($pages, $page['id']) . '</li>';
            }
        }
    }
    if (empty($items)) {
        return '';
    }

    if ($parentID === '') {
        $attributes = ' class="bearcms-navigation-element"';
    } else {
        $attributes = ' class="bearcms-navigation-element-item-children"';
    }
    return '<ul' . $attributes . '>' . implode('', $items) . '</ul>';
};

$type = 'top';
if (strlen($component->type) > 0) {
    if (array_search($component->type, ['top', 'children', 'tree']) !== false) {
        $type = $component->type;
    }
}

$menuType = 'horizontal-down';
if (strlen($component->menuType) > 0) {
    if (array_search($component->menuType, ['horizontal-down']) !== false) {
        $menuType = $component->type;
    }
}

$showHomeButton = false;
if (strlen($component->showHomeButton) > 0) {
    if ($component->showHomeButton === 'true') {
        $showHomeButton = true;
    }
}

$pages = [];
if ($type === 'top') {
    $pages = BearCMS\Data\Pages::getList();
    $temp = [];
    foreach ($pages as $page) {
        if ($page['parentID'] === '') {
            $temp[] = $page;
        }
    }
    $pages = $temp;
} elseif ($type === 'children') {
    $pages = BearCMS\Data\Pages::getList();
    $temp = [];
    $parentID = strlen($component->pageID) > 0 ? $component->pageID : '';
    foreach ($pages as $page) {
        if ($page['parentID'] === $parentID) {
            $temp[] = $page;
        }
    }
    $pages = $temp;
} elseif ($type === 'tree') {
    $pages = BearCMS\Data\Pages::getList();
}

$attributes = '';
$attributes .= ' type="' . $menuType . '"';
if (strlen($component->class) > 0) {
    $attributes .= ' class="' . htmlentities($component->class) . '"';
}
$attributes .= ' moreItemHtml="' . htmlentities('<li class="bearcms-navigation-element-item bearcms-navigation-element-item-more"><a></a><ul class="bearcms-navigation-element-item-children"></ul></li>') . '"';

if ($showHomeButton) {
    array_unshift($pages, ['id' => '_home', 'path' => '/', 'name' => 'Home', 'parentID' => '', 'status' => 'published']);
}
$content = '<component src="navigation-menu"' . $attributes . '>' . $buildTreeFunction($pages, (string) $component->pageID) . '</component>';

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'navigation', $content);
?><html>
    <body><?= $content ?></body>
</html>