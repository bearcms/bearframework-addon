<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();

$selectedPath = '';
if (strlen($component->selectedPath) > 0) {
    $selectedPath = $component->selectedPath;
}

$buildTree = function($pages, $recursive = false, $level = 0) use ($app, $selectedPath, &$buildTree) {
    $itemsHtml = [];
    foreach ($pages as $page) {
        $classNames = 'bearcms-navigation-element-item';
        if ($page->path === $selectedPath) {
            $classNames .= ' bearcms-navigation-element-item-selected';
        } elseif ($page->id !== '_home' && strpos($selectedPath, $page->path) === 0) {
            $classNames .= ' bearcms-navigation-element-item-in-path';
        }
        $itemsHtml[] = '<li class="' . $classNames . '"><a href="' . $app->request->base . $page->path . '">' . htmlspecialchars($page->name) . '</a>';
        if ($recursive && isset($page->children)) {
            $itemsHtml[] = $buildTree($page->children, true, $level + 1);
        }
        $itemsHtml[] = '</li>';
    }
    if (empty($itemsHtml)) {
        return '';
    }

    if ($level === 0) {
        $attributes = ' class="bearcms-navigation-element"';
    } else {
        $attributes = ' class="bearcms-navigation-element-item-children"';
    }
    return '<ul' . $attributes . '>' . implode('', $itemsHtml) . '</ul>';
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
        $menuType = $component->menuType;
    }
}

$showHomeButton = false;
if (strlen($component->showHomeButton) > 0) {
    if ($component->showHomeButton === 'true') {
        $showHomeButton = true;
        $homeButtomText = strlen($component->homeButtonText) > 0 ? $component->homeButtonText : 'Home';
    }
}

$pages = null;
if ($type === 'top' || $type === 'tree') {
    $pages = $app->bearCMS->data->pages->getList()
            ->filterBy('parentID', '')
            ->filterBy('status', 'published');
} elseif ($type === 'children') {
    $pages = $app->bearCMS->data->pages->getList()
            ->filterBy('parentID', (string) $component->pageID)
            ->filterBy('status', 'published');
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

if ($pages !== null && $showHomeButton) {
    $pages->unshift(new \BearCMS\DataObject(['id' => '_home', 'path' => '/', 'name' => $homeButtomText, 'parentID' => '', 'status' => 'published']));
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
    if ($pages === null || $pages->length === 0) {
        $itemsHtml = '';
    } else {
        $itemsHtml = $buildTree($pages, $type === 'tree');
    }
}
$content = '';
if (isset($itemsHtml{0})) {
    $content = '<component src="navigation-menu"' . $attributes . '>' . $itemsHtml . '</component>';
}
?><html>
    <body><?= $content ?></body>
</html>