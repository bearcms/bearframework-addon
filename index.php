<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\CurrentUser;
use BearCMS\Data;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\Server;
use BearCMS\Internal\ElementsHelper;
use BearCMS\CurrentTemplate;
use IvoPetkov\HTML5DOMDocument;

$context->classes->add('BearCMS', 'classes/BearCMS.php');

$context->classes->add('BearCMS\Data\Addons', 'classes/BearCMS/Data/Addons.php');
$context->classes->add('BearCMS\Data\BlogPosts', 'classes/BearCMS/Data/BlogPosts.php');
$context->classes->add('BearCMS\Data\Pages', 'classes/BearCMS/Data/Pages.php');
$context->classes->add('BearCMS\Data\Settings', 'classes/BearCMS/Data/Settings.php');
$context->classes->add('BearCMS\Data\Templates', 'classes/BearCMS/Data/Templates.php');
$context->classes->add('BearCMS\Data\Users', 'classes/BearCMS/Data/Users.php');

$context->classes->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php');
$context->classes->add('BearCMS\Internal\Data\BlogPosts', 'classes/BearCMS/Internal/Data/BlogPosts.php');
$context->classes->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php');
$context->classes->add('BearCMS\Internal\Data\Templates', 'classes/BearCMS/Internal/Data/Templates.php');
$context->classes->add('BearCMS\Internal\Data\Users', 'classes/BearCMS/Internal/Data/Users.php');
$context->classes->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php');
$context->classes->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php');
$context->classes->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php');
$context->classes->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php');
$context->classes->add('BearCMS\Internal\ServerCommands', 'classes/BearCMS/Internal/ServerCommands.php');

$context->classes->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php');
$context->classes->add('BearCMS\CurrentTemplate', 'classes/BearCMS/CurrentTemplate.php');
$context->classes->add('BearCMS\Fonts', 'classes/BearCMS/Fonts.php');

$app->components->addAlias('bearcms-elements', 'file:' . $context->dir . '/components/bearcms-elements.php');
$app->components->addAlias('bearcms-heading-element', 'file:' . $context->dir . '/components/bearcms-heading-element.php');
$app->components->addAlias('bearcms-text-element', 'file:' . $context->dir . '/components/bearcms-text-element.php');
$app->components->addAlias('bearcms-link-element', 'file:' . $context->dir . '/components/bearcms-link-element.php');
$app->components->addAlias('bearcms-video-element', 'file:' . $context->dir . '/components/bearcms-video-element.php');
$app->components->addAlias('bearcms-image-element', 'file:' . $context->dir . '/components/bearcms-image-element.php');
$app->components->addAlias('bearcms-image-gallery-element', 'file:' . $context->dir . '/components/bearcms-image-gallery-element.php');
$app->components->addAlias('bearcms-navigation-element', 'file:' . $context->dir . '/components/bearcms-navigation-element.php');
$app->components->addAlias('bearcms-html-element', 'file:' . $context->dir . '/components/bearcms-html-element.php');
$app->components->addAlias('bearcms-blog-posts-element', 'file:' . $context->dir . '/components/bearcms-blog-posts-element.php');

$app->routes->add(['/admin/', '/admin/*'], ['BearCMS\Internal\Controller', 'handleAdminPage']);
$app->routes->add('/-aj/', ['BearCMS\Internal\Controller', 'handleAjax'], ['POST']);
$app->routes->add('/-au/', ['BearCMS\Internal\Controller', 'handleFileUpload'], ['POST']);

$context->assets->addDir('assets');

if (!isset($context->options['serverUrl'])) {
    throw new Exception('serverUrl option is not set in bearcms/bearcms-bearframework-addon');
}
\BearCMS\Internal\Server::$url = $context->options['serverUrl'];

$addons = InternalData\Addons::getList();
$addonsDir = rtrim($app->maintenance->addonsDir, ' /') . '/';
foreach ($addons as $addonData) {
    $addonID = $addonData['id'];
    $addonDir = $addonsDir . $addonID;
    if (is_file($addonDir . DIRECTORY_SEPARATOR . 'autoload.php')) {
        include $addonDir . DIRECTORY_SEPARATOR . 'autoload.php';
    } else {
        throw new Exception('Cannot find autoload.php file for ' . $addonID);
    }
    if (\BearFramework\Addons::exists($addonID)) {
        $options = \BearFramework\Addons::getOptions($addonID);
        if (isset($options['bearCMS']) && is_array($options['bearCMS']) && isset($options['bearCMS']['assetsDirs'])) {
            foreach ($options['bearCMS']['assetsDirs'] as $dir) {
                $app->assets->addDir($addonDir . DIRECTORY_SEPARATOR . $dir);
            }
        }
        if ($addonData['enabled']) {
            $app->addons->add($addonID, ['addedByBearCMS' => true]);
        }
    } else {
        throw new Exception('Addon ' . $addonID . ' not available');
    }
}

$app->routes->add('*', function() {
    $cookies = Cookies::getList(Cookies::TYPE_SERVER);
    if (isset($cookies['_a']) && !CurrentUser::exists()) {
        Server::call('autologin', null, true);
    }
});

$app->routes->add('/b/?/', function() use ($app) {
    $slug = (string) $app->request->path[1];
    $slugsList = InternalData\BlogPosts::getSlugsList('published');
    $blogPostID = array_search($slug, $slugsList);
    if ($blogPostID === false && substr($slug, 0, 6) === 'draft-' && CurrentUser::exists()) {
        $blogPost = Data\BlogPosts::getPost(substr($slug, 6));
        if ($blogPost !== null) {
            $blogPostID = $blogPost['id'];
        }
    }
    if ($blogPostID !== false) {
        $blogPost = Data\BlogPosts::getPost($blogPostID);

        $content = '<h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost['title']) . '</h1>';
        $content .= '<div class="bearcms-blogpost-page-date">' . ($blogPost['status'] === 'published' ? date('F j, Y', $blogPost['publishedTime']) : 'draft') . '</div>';
        $content .= '<component src="bearcms-elements" id="bearcms-blogpost-' . $blogPostID . '"/>';

        $response = new App\Response\HTML($content);
        $response->bearCMSBlogPostID = $blogPostID;
        $response->bearCMSType = 'page';
        return $response;
    }
});

$app->routes->add('*', function() use ($app) {
    $path = (string) $app->request->path;
    $pathsList = InternalData\Pages::getPathsList(CurrentUser::exists() ? 'all' : 'published');
    $pageID = array_search($path, $pathsList);
    if ($pageID !== false) {
        $content = '<component src="bearcms-elements" id="bearcms-page-' . $pageID . '" editable="true"/>';
        $response = new App\Response\HTML($content);
        $response->bearCMSPageID = $pageID;
        $response->bearCMSType = 'page';
        return $response;
    }
});

$app->hooks->add('componentCreated', function($component) {
    if ($component->src === 'bearcms-elements') {
        ElementsHelper::updateContainerComponent($component);
    } elseif (array_search($component->src, ['bearcms-heading-element', 'bearcms-text-element', 'bearcms-link-element', 'bearcms-video-element', 'bearcms-image-element', 'bearcms-image-gallery-element', 'bearcms-navigation-element', 'bearcms-html-element', 'bearcms-blog-posts-element']) !== false) {
        ElementsHelper::updateElementComponent($component);
    }
});

$app->hooks->add('responseCreated', function($response) use ($app, $context) {

    if ($response instanceof App\Response\NotFound) {
        $response->bearCMSType = 'notFound';
        $response->setContentType('text/html');
    } elseif ($response instanceof App\Response\TemporaryUnavailable) {
        $response->bearCMSType = 'temporaryUnavailable';
        $response->setContentType('text/html');
    } elseif ($app->request->path[0] === null) {
        $response->bearCMSType = 'page';
    } elseif ($app->request->path[0] === 'admin') {
        $response->bearCMSType = 'admin';
    }

    if (CurrentTemplate::getID() === 'bearcms/default') {
        if (isset($response->bearCMSType) && $response->bearCMSType === 'page') {
            $template = null;
            if ($response instanceof App\Response\HTML) {
                $template = $app->components->process('<component src="file:' . $context->dir . '/components/defaultTemplate.php"/>');
            } elseif ($response instanceof App\Response\NotFound) {
                $template = $app->components->process('<component src="file:' . $context->dir . '/components/defaultTemplate.php" mode="notFound"/>');
            } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                $template = $app->components->process('<component src="file:' . $context->dir . '/components/defaultTemplate.php" mode="temporaryUnavailable"/>');
            }
            if ($template !== null) {
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML(str_replace('{body}', $domDocument->createInsertTarget('templateBody'), $template));
                $domDocument->insertHTML($response->content, 'templateBody');
                $response->content = $domDocument->saveHTML();
            }
        }
    }

    if ($response instanceof App\Response\HTML) {
        if (!(isset($response->bearCMSType) && $response->bearCMSType === 'page')) {
            return;
        }

        $componentContent = '<html><head>';

        $settings = Data\Settings::get();
        $title = '';
        $descrption = '';
        $keywords = '';
        if (isset($response->bearCMSPageID)) {
            $page = Data\Pages::getPage($response->bearCMSPageID);
            if (is_array($page)) {
                $title = isset($page['title']) ? trim($page['title']) : '';
                if (!isset($title{0})) {
                    $title = isset($page['name']) ? trim($page['name']) : '';
                }
                $descrption = isset($page['description']) ? trim($page['description']) : '';
                $keywords = isset($page['keywords']) ? trim($page['keywords']) : '';
            }
        } else {
            $title = trim($settings['title']);
            $descrption = trim($settings['description']);
            $keywords = trim($settings['keywords']);
        }
        if (isset($title{0})) {
            $componentContent .= '<title>' . htmlspecialchars($title) . '</title>';
        }
        if (isset($descrption{0})) {
            $componentContent .= '<meta name="description" content="' . htmlentities($descrption) . '"/>';
        }
        if (isset($keywords{0})) {
            $componentContent .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
        }
        $componentContent .= '<meta name="generator" content="BearCMS"/>';
        $icon = $settings['icon'];
        if (isset($icon{0})) {
            $objectKey = 'bearcms/files/icon/' . $icon;
            $mimeType = $app->assets->getMimeType($app->data->getFilename($objectKey));
            $typeAttribute = $mimeType !== null ? ' type="' . $mimeType . '"' : '';
            $componentContent .= '<link rel="apple-touch-icon" sizes="57x57" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 57, 'height' => 57])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="60x60" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 60, 'height' => 60])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="72x72" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 72, 'height' => 72])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="76x76" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 76, 'height' => 76])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="114x114" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 114, 'height' => 114])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="120x120" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 120, 'height' => 120])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="144x144" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 144, 'height' => 144])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="152x152" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 152, 'height' => 152])) . '">';
            $componentContent .= '<link rel="apple-touch-icon" sizes="180x180" href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 180, 'height' => 180])) . '">';
            $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 32, 'height' => 32])) . '" sizes="32x32">';
            $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 192, 'height' => 192])) . '" sizes="192x192">';
            $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 96, 'height' => 96])) . '" sizes="96x96">';
            $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($app->data->getFilename($objectKey), ['width' => 16, 'height' => 16])) . '" sizes="16x16">';
        }
        $componentContent .= '<link rel="canonical" href="' . htmlentities(rtrim($app->request->base . $app->request->path, '/') . '/') . '"/>';
        $componentContent .= '</head><body></body></html>';

        $domDocument = new HTML5DOMDocument();
        $domDocument->loadHTML($response->content);
        $domDocument->insertHTML('<component src="data:base64,' . base64_encode($componentContent) . '"/>');
        $response->content = $app->components->process($domDocument->saveHTML());

        if (!CurrentUser::exists()) {
            return;
        }
        $requestArguments = [];
        $requestArguments['hasEditableElements'] = empty(ElementsHelper::$editorData) ? '0' : '1';

        $cacheKey = json_encode([
            'adminUI',
            $app->request->base,
            $requestArguments,
            CurrentUser::getKey(),
            CurrentUser::getPermissions(),
            Cookies::getList(Cookies::TYPE_SERVER)
        ]);

        $adminUIData = $app->cache->get($cacheKey);
        if (!is_array($adminUIData)) {
            $adminUIData = Server::call('adminui', $requestArguments, true);
            $app->cache->set($cacheKey, $adminUIData, is_array($adminUIData) && isset($adminUIData['result']) ? 99999 : 10);
        }

        if (is_array($adminUIData) && isset($adminUIData['result']) && is_array($adminUIData['result']) && isset($adminUIData['result']['content'])) {
            $content = $adminUIData['result']['content'];
            if (!empty(ElementsHelper::$editorData)) {
                $requestArguments = [];
                $requestArguments['data'] = json_encode(ElementsHelper::$editorData);

                $cacheKey = json_encode([
                    'elementsEditor',
                    $app->request->base,
                    $requestArguments,
                    CurrentUser::getKey(),
                    CurrentUser::getPermissions(),
                    Cookies::getList(Cookies::TYPE_SERVER)
                ]);
                $elementsEditorData = $app->cache->get($cacheKey);
                if (!is_array($elementsEditorData)) {
                    $elementsEditorData = Server::call('elementseditor', $requestArguments, true);
                    $app->cache->set($cacheKey, $elementsEditorData, is_array($elementsEditorData) && isset($elementsEditorData['result']) ? 99999 : 10);
                }
                if (is_array($elementsEditorData) && isset($elementsEditorData['result']) && is_array($elementsEditorData['result']) && isset($elementsEditorData['result']['content'])) {
                    $domDocument = new HTML5DOMDocument();
                    $domDocument->loadHTML($content);
                    $domDocument->insertHTML($elementsEditorData['result']['content']);
                    $domDocument->insertHTML('<html><body><script src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.js')) . '"></script></body></html>');
                    $content = $domDocument->saveHTML();
                } else {
                    $response = new App\Response\TemporaryUnavailable();
                }
            }

            $content = Server::updateAssetsUrls($content, false);
            $content = str_replace('{body}', '<component src="data:base64,' . base64_encode($response->content) . '"/>', $content);
            $response->content = $app->components->process($content);
        } else {
            $response = new App\Response\TemporaryUnavailable();
        }
    }
});

$app->hooks->add('responseCreated', function() {
    Cookies::update();
});
