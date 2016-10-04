<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */


$selectedPath = '';
if (strlen($component->selectedPath) > 0) {
    $selectedPath = $component->selectedPath;
}

$buildTree = function($pages, $parentID) use ($app, $selectedPath, &$buildTree) {
    $items = [];
    foreach ($pages as $page) {
        if ($page['parentID'] === $parentID) {
            $classNames = 'bearcms-navigation-element-item';
            if ($page['path'] === $selectedPath) {
                $classNames .= ' bearcms-navigation-element-item-selected';
            } elseif ($page['id'] !== '_home' && strpos($selectedPath, $page['path']) === 0) {
                $classNames .= ' bearcms-navigation-element-item-in-path';
            }
            $items[] = '<li class="' . $classNames . '"><a href="' . $app->request->base . $page['path'] . '">' . htmlspecialchars($page['name']) . '</a>' . $buildTree($pages, $page['id']) . '</li>';
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

$menuType = 'list-vertical';
if (strlen($component->menuType) > 0) {
    if (array_search($component->menuType, ['horizontal-down', 'list-vertical']) !== false) {
        $menuType = $component->type;
    }
}

$showHomeButton = false;
if (strlen($component->showHomeButton) > 0) {
    if ($component->showHomeButton === 'true') {
        $showHomeButton = true;
        $homeButtomText = strlen($component->homeButtonText) > 0 ? $component->homeButtonText : 'Home';
    }
}

$pages = [];
if ($type === 'top') {
    $structure = $app->bearCMS->data->pages->getStructure();
    $pages = $app->bearCMS->data->pages->getList(['PUBLISHED_ONLY']);
    $temp = [];
    foreach ($pages as $page) {
        if ($page['parentID'] === '') {
            $temp[] = $page;
        }
    }
    $pages = $temp;
} elseif ($type === 'children') {
    $structure = $app->bearCMS->data->pages->getStructure();
    $pages = $app->bearCMS->data->pages->getList(['PUBLISHED_ONLY']);
    $temp = [];
    $parentID = strlen($component->pageID) > 0 ? $component->pageID : '';
    foreach ($pages as $page) {
        if ($page['parentID'] === $parentID) {
            $temp[] = $page;
        }
    }
    $pages = $temp;
} elseif ($type === 'tree') {
    $structure = $app->bearCMS->data->pages->getStructure();
    $pages = $app->bearCMS->data->pages->getList(['PUBLISHED_ONLY']);
}

// sort pages
if (isset($structure, $pages)) {
    $flatStructure = [];
    $walkStructure = function($structure) use (&$flatStructure, &$walkStructure) {
        foreach ($structure as $item) {
            if (isset($item['id'])) {
                $flatStructure[] = $item['id'];
            }
            if (isset($item['children'])) {
                $walkStructure($item['children']);
            }
        }
    };
    $walkStructure($structure);
    usort($pages, function($a, $b) use ($flatStructure) {
        if (isset($a['id'], $b['id'])) {
            $aIndex = array_search($a['id'], $flatStructure);
            $bIndex = array_search($b['id'], $flatStructure);
            if ($aIndex == $bIndex) {
                return 0;
            }
            return ($aIndex < $bIndex) ? -1 : 1;
        }
    });
    unset($flatStructure);
    unset($walkStructure);
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

if ($showHomeButton) {
    array_unshift($pages, ['id' => '_home', 'path' => '/', 'name' => $homeButtomText, 'parentID' => '', 'status' => 'published']);
}

$itemsHtml = (string) $component->innerHTML;
if (isset($itemsHtml{0})) {
    $domDocument = new IvoPetkov\HTML5DOMDocument();
    $domDocument->loadHTML($itemsHtml);
    $ulElements = $domDocument->querySelectorAll('ul');
    foreach ($ulElements as $index => $ulElement) {
        $ulElement->setAttribute('class', trim($ulElement->getAttribute('class') . ' ' . ($index === 0 ? 'bearcms-navigation-element' : 'bearcms-navigation-element-item-children')));
    }
    $liElements = $domDocument->querySelectorAll('li');
    foreach ($liElements as $index => $liElement) {
        $liClasssName = 'bearcms-navigation-element-item';
        if ($liElement->firstChild) {
            $liPath = str_replace($app->request->base, '', $liElement->firstChild->getAttribute('href'));
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
    if (empty($pages)) {
        $itemsHtml = '';
    } else {
        $itemsHtml = $buildTree($pages, (string) $component->pageID);
    }
}
$content = '';
if (isset($itemsHtml{0})) {
    $content = '<component src="navigation-menu"' . $attributes . '>' . $itemsHtml . '</component>';
}

$content = \BearCMS\Internal\ElementsHelper::getElementComponentContent($component, 'navigation', $content);
?><html>
    <body><?= $content ?></body>
</html>