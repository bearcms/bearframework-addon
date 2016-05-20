<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\Data as InternalData;
use BearCMS\Internal\Server;
use BearCMS\Internal\ElementsHelper;
use IvoPetkov\HTML5DOMDocument;
use \BearCMS\Internal\Features;

$context->classes->add('BearCMS', 'classes/BearCMS.php');

$context->classes->add('BearCMS\Data', 'classes/BearCMS/Data.php');
$context->classes->add('BearCMS\Data\Addons', 'classes/BearCMS/Data/Addons.php');
$context->classes->add('BearCMS\Data\Blog', 'classes/BearCMS/Data/Blog.php');
$context->classes->add('BearCMS\Data\Pages', 'classes/BearCMS/Data/Pages.php');
$context->classes->add('BearCMS\Data\Settings', 'classes/BearCMS/Data/Settings.php');
$context->classes->add('BearCMS\Data\Templates', 'classes/BearCMS/Data/Templates.php');
$context->classes->add('BearCMS\Data\Users', 'classes/BearCMS/Data/Users.php');

$context->classes->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php');
$context->classes->add('BearCMS\Internal\Data\Blog', 'classes/BearCMS/Internal/Data/Blog.php');
$context->classes->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php');
$context->classes->add('BearCMS\Internal\Data\Templates', 'classes/BearCMS/Internal/Data/Templates.php');
$context->classes->add('BearCMS\Internal\Data\Users', 'classes/BearCMS/Internal/Data/Users.php');
$context->classes->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php');
$context->classes->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php');
$context->classes->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php');
$context->classes->add('BearCMS\Internal\Features', 'classes/BearCMS/Internal/Features.php');
$context->classes->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php');
$context->classes->add('BearCMS\Internal\ServerCommands', 'classes/BearCMS/Internal/ServerCommands.php');

$context->classes->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php');
$context->classes->add('BearCMS\CurrentTemplate', 'classes/BearCMS/CurrentTemplate.php');

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

$context->assets->addDir('assets');

$app->container->set('bearCMS', \BearCMS::class);

if (!isset($context->options['serverUrl'])) {
    throw new Exception('serverUrl option is not set in bearcms/bearcms-bearframework-addon');
}
\BearCMS\Internal\Server::$url = $context->options['serverUrl'];

$features = [];
if (isset($context->options['features'])) {
    $walkFeatures = function($list, $prefix = '') use (&$walkFeatures, &$features) {
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if ($value === true) {
                    $features[] = strtolower($prefix . $key);
                    $features[] = strtolower($prefix . $key) . '.all';
                } elseif (is_array($value)) {
                    $features[] = strtolower($prefix . $key);
                    $walkFeatures($value, $prefix . $key . '.');
                }
            }
        }
    };
    $walkFeatures($context->options['features']);
}
if (empty($features)) {
    $features[] = 'all';
}
Features::$data = $features;

if (Features::enabled('users')) {
    $app->routes->add(['/admin/', '/admin/*'], ['BearCMS\Internal\Controller', 'handleAdminPage']);
    $app->routes->add('/-aj/', ['BearCMS\Internal\Controller', 'handleAjax'], ['POST']);
    $app->routes->add('/-au/', ['BearCMS\Internal\Controller', 'handleFileUpload'], ['POST']);
}

if (Features::enabled('addons')) {
    $addonsDir = $context->options['addonsDir'];
    $addons = InternalData\Addons::getList();
    $addonsDir = rtrim($addonsDir, ' /') . '/';
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
}

if (Features::enabled('users')) {
    $app->routes->add('*', function() use ($app) {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        if (isset($cookies['_a']) && !$app->bearCMS->currentUser->exists()) {
            Server::call('autologin', null, true);
        }
    });
}

if (Features::enabled('blog')) {
    $app->routes->add('/b/?/', function() use ($app) {
        $slug = (string) $app->request->path[1];
        $slugsList = InternalData\Blog::getSlugsList('published');
        $blogPostID = array_search($slug, $slugsList);
        if ($blogPostID === false && substr($slug, 0, 6) === 'draft-' && Features::enabled('users') && $app->bearCMS->currentUser->exists()) {
            $blogPost = $app->bearCMS->data->blog->getPost(substr($slug, 6));
            if ($blogPost !== null) {
                $blogPostID = $blogPost['id'];
            }
        }
        if ($blogPostID !== false) {
            $blogPost = $app->bearCMS->data->blog->getPost($blogPostID);

            $content = '<h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost['title']) . '</h1>';
            $content .= '<div class="bearcms-blogpost-page-date">' . ($blogPost['status'] === 'published' ? date('F j, Y', $blogPost['publishedTime']) : 'draft') . '</div>';
            $content .= '<component src="bearcms-elements" id="bearcms-blogpost-' . $blogPostID . '"/>';

            $response = new App\Response\HTML($content);
            $response->enableBearCMS = true;
            $response->applyBearCMSTemplate = true;
            $response->bearCMSBlogPostID = $blogPostID;
            return $response;
        }
    });
}

if (Features::enabled('pages')) {
    $app->routes->add('*', function() use ($app) {
        $path = (string) $app->request->path;
        $pathsList = InternalData\Pages::getPathsList(Features::enabled('users') && $app->bearCMS->currentUser->exists() ? 'all' : 'published');
        $pageID = array_search($path, $pathsList);
        if ($pageID !== false) {
            $content = '<component src="bearcms-elements" id="bearcms-page-' . $pageID . '" editable="true"/>';
            $response = new App\Response\HTML($content);
            $response->enableBearCMS = true;
            $response->applyBearCMSTemplate = true;
            $response->bearCMSPageID = $pageID;
            return $response;
        }
    });
}

if (Features::enabled('elements')) {
    $app->hooks->add('componentCreated', function($component) {
        if ($component->src === 'bearcms-elements') {
            ElementsHelper::updateContainerComponent($component);
        } elseif (array_search($component->src, ['bearcms-heading-element', 'bearcms-text-element', 'bearcms-link-element', 'bearcms-video-element', 'bearcms-image-element', 'bearcms-image-gallery-element', 'bearcms-navigation-element', 'bearcms-html-element', 'bearcms-blog-posts-element']) !== false) {
            ElementsHelper::updateElementComponent($component);
        }
    });
}

$app->hooks->add('responseCreated', function($response) use ($app, $context) {
    if ($response instanceof App\Response\NotFound) {
        $response->enableBearCMS = true;
        $response->applyBearCMSTemplate = true;
        $response->setContentType('text/html');
    } elseif ($response instanceof App\Response\TemporaryUnavailable) {
        $response->enableBearCMS = true;
        $response->applyBearCMSTemplate = true;
        $response->setContentType('text/html');
    } elseif ($app->request->path[0] === null) {
        $response->enableBearCMS = true;
        $response->applyBearCMSTemplate = true;
    }

    if (!isset($response->enableBearCMS)) {
        $response->enableBearCMS = false;
    }
    if (!isset($response->applyBearCMSTemplate)) {
        $response->applyBearCMSTemplate = false;
    }

    if ($response->applyBearCMSTemplate && $app->bearCMS->currentTemplate->getID() === 'bearcms/default') {
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
});

$app->hooks->add('responseCreated', function($response) use ($app, $context) {

    if (!(isset($response->enableBearCMS) && $response->enableBearCMS)) {
        return;
    }

    $componentContent = '<html><head>';

    $settings = $app->bearCMS->data->settings->get();
    $title = '';
    $descrption = '';
    $keywords = '';
    if (isset($response->bearCMSPageID)) {
        $page = $app->bearCMS->data->pages->getPage($response->bearCMSPageID);
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
        $filename = $app->bearCMS->data->getRealFilename($icon);
        $mimeType = $app->assets->getMimeType($filename);
        $typeAttribute = $mimeType !== null ? ' type="' . $mimeType . '"' : '';
        $componentContent .= '<link rel="apple-touch-icon" sizes="57x57" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 57, 'height' => 57])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="60x60" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 60, 'height' => 60])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="72x72" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 72, 'height' => 72])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="76x76" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 76, 'height' => 76])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="114x114" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 114, 'height' => 114])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="120x120" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 120, 'height' => 120])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="144x144" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 144, 'height' => 144])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="152x152" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 152, 'height' => 152])) . '">';
        $componentContent .= '<link rel="apple-touch-icon" sizes="180x180" href="' . htmlentities($app->assets->getUrl($filename, ['width' => 180, 'height' => 180])) . '">';
        $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($filename, ['width' => 32, 'height' => 32])) . '" sizes="32x32">';
        $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($filename, ['width' => 192, 'height' => 192])) . '" sizes="192x192">';
        $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($filename, ['width' => 96, 'height' => 96])) . '" sizes="96x96">';
        $componentContent .= '<link rel="icon"' . $typeAttribute . ' href="' . htmlentities($app->assets->getUrl($filename, ['width' => 16, 'height' => 16])) . '" sizes="16x16">';
    }
    if (empty($settings['allowSearchEngines'])) {
        $componentContent .= '<meta name="robots" content="noindex">';
    }
    $componentContent .= '<link rel="canonical" href="' . htmlentities(rtrim($app->request->base . $app->request->path, '/') . '/') . '"/>';
    $componentContent .= '</head><body></body></html>';

    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($response->content);
    $domDocument->insertHTML('<component src="data:base64,' . base64_encode($componentContent) . '"/>');
    $response->content = $app->components->process($domDocument->saveHTML());

    $currentUserExists = Features::enabled('users') ? $app->bearCMS->currentUser->exists() : false;
    $externalLinksAreEnabled = !empty($settings['externalLinks']);
    if ($externalLinksAreEnabled) {
        $domDocument = new HTML5DOMDocument();
        $domDocument->loadHTML($response->content);
        $domDocument->insertHTML('<html><body><script src="' . htmlentities($context->assets->getUrl('assets/externalLinks.js')) . '"></script><script>bearCMS.externalLinks.initialize(' . ($externalLinksAreEnabled ? 1 : 0) . ',' . ($currentUserExists ? 1 : 0) . ');</script></body></html>');
        $response->content = $domDocument->saveHTML();
    }

    if (!$currentUserExists) {
        return;
    }

    $serverCookies = Cookies::getList(Cookies::TYPE_SERVER);
    if (!empty($serverCookies['tmcs']) || !empty($serverCookies['tmpr'])) {
        ElementsHelper::$editorData = [];
    }

    $requestArguments = [];
    $requestArguments['hasEditableElements'] = empty(ElementsHelper::$editorData) ? '0' : '1';

    $cacheKey = json_encode([
        'adminUI',
        $app->request->base,
        $requestArguments,
        $app->bearCMS->currentUser->getKey(),
        $app->bearCMS->currentUser->getPermissions(),
        Features::$data,
        $serverCookies,
        rand()
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
                $app->bearCMS->currentUser->getKey(),
                $app->bearCMS->currentUser->getPermissions(),
                Features::$data,
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
        $content = $app->components->process($content);

        $response->content = $content;
    } else {
        $response = new App\Response\TemporaryUnavailable();
    }
}, ['priority' => 1000]);

if (Features::enabled('users')) {
    $app->hooks->add('responseCreated', function() {
        Cookies::update();
    }, ['priority' => 1001]);
}
