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
use \BearCMS\Internal\Options;

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
$context->classes->add('BearCMS\Internal\Data\Files', 'classes/BearCMS/Internal/Data/Files.php');
$context->classes->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php');
$context->classes->add('BearCMS\Internal\Data\Templates', 'classes/BearCMS/Internal/Data/Templates.php');
$context->classes->add('BearCMS\Internal\Data\Users', 'classes/BearCMS/Internal/Data/Users.php');
$context->classes->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php');
$context->classes->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php');
$context->classes->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php');
$context->classes->add('BearCMS\Internal\Options', 'classes/BearCMS/Internal/Options.php');
$context->classes->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php');
$context->classes->add('BearCMS\Internal\ServerCommands', 'classes/BearCMS/Internal/ServerCommands.php');

$context->classes->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php');
$context->classes->add('BearCMS\CurrentTemplate', 'classes/BearCMS/CurrentTemplate.php');
$context->classes->add('BearCMS\CurrentTemplateOptions', 'classes/BearCMS/CurrentTemplateOptions.php');

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

Options::set($context->options);

$app->hooks->add('initialized', function() use ($app) {

    // Load the CMS managed addons
    if (Options::hasFeature('addons')) {
        $addons = InternalData\Addons::getList();
        foreach ($addons as $addonData) {
            $addonID = $addonData['id'];
            $addonDir = Options::$addonsDir . DIRECTORY_SEPARATOR . $addonID . DIRECTORY_SEPARATOR;
            if (is_file($addonDir . 'autoload.php')) {
                include $addonDir . 'autoload.php';
            } else {
                throw new Exception('Cannot find autoload.php file for ' . $addonID);
            }
            if (\BearFramework\Addons::exists($addonID)) {
                $_addonData = \BearFramework\Addons::get($addonID);
                $options = $_addonData['options'];
                if (isset($options['bearCMS']) && is_array($options['bearCMS']) && isset($options['bearCMS']['assetsDirs'])) {
                    foreach ($options['bearCMS']['assetsDirs'] as $dir) {
                        $app->assets->addDir($addonDir . $dir);
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

    // Automatically log in the user
    if (Options::hasServer() && Options::hasFeature('users')) {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        if (isset($cookies['_a']) && !$app->bearCMS->currentUser->exists()) {
            Server::call('autologin', null, true);
        }
    }

    // Register the system pages
    if (Options::hasServer() && Options::hasFeature('users')) {
        $app->routes->add(['/admin/loggedin/'], function() use ($app) {
            return new App\Response\TemporaryRedirect($app->request->base . '/');
        });
        $app->routes->add(['/admin/', '/admin/*/'], ['BearCMS\Internal\Controller', 'handleAdminPage']);
        $app->routes->add(['/admin', '/admin/*'], function() use ($app) {
            return new App\Response\PermanentRedirect($app->request->base . $app->request->path . '/');
        });
        $app->routes->add('/-aj/', ['BearCMS\Internal\Controller', 'handleAjax'], ['POST']);
        $app->routes->add('/-au/', ['BearCMS\Internal\Controller', 'handleFileUpload'], ['POST']);
    }

    // Register the file handlers
    if (Options::hasFeature('files')) {
        $app->routes->add('/files/preview/*', ['BearCMS\Internal\Controller', 'handleFilePreview']);
        $app->routes->add('/files/download/*', ['BearCMS\Internal\Controller', 'handleFileDownload']);
    }

    // Register some other pages
    $app->routes->add('/rss.xml', ['BearCMS\Internal\Controller', 'handleRSS']);
    $app->routes->add('/sitemap.xml', ['BearCMS\Internal\Controller', 'handleSitemap']);
    $app->routes->add('/robots.txt', ['BearCMS\Internal\Controller', 'handleRobots']);

    // Register the blog posts page handlers
    if (Options::hasFeature('blog')) {
        $app->routes->add('/b/?/', function() use ($app) {
            $slug = (string) $app->request->path[1];
            $slugsList = InternalData\Blog::getSlugsList('published');
            $blogPostID = array_search($slug, $slugsList);
            if ($blogPostID === false && substr($slug, 0, 6) === 'draft-' && Options::hasFeature('users') && $app->bearCMS->currentUser->exists()) {
                $blogPost = $app->bearCMS->data->blog->getPost(substr($slug, 6));
                if ($blogPost !== null) {
                    $blogPostID = $blogPost['id'];
                }
            }
            if ($blogPostID !== false) {
                $blogPost = $app->bearCMS->data->blog->getPost($blogPostID);

                $content = '<div class="bearcms-blogpost-page-title-container"><h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost['title']) . '</h1></div>';
                $content .= '<div class="bearcms-blogpost-page-date-container"><div class="bearcms-blogpost-page-date">' . ($blogPost['status'] === 'published' ? date('F j, Y', $blogPost['publishedTime']) : 'draft') . '</div></div>';
                $content .= '<div class="bearcms-blogpost-page-content"><component src="bearcms-elements" id="bearcms-blogpost-' . $blogPostID . '"/></div>';

                $response = new App\Response\HTML($content);
                $response->enableBearCMS = true;
                $response->bearCMSBlogPostID = $blogPostID;
                return $response;
            }
        });
        $app->routes->add('/b/?', function() use ($app) {
            return new App\Response\PermanentRedirect($app->request->base . $app->request->path . '/');
        });
    }

    // Register a home page and the dynamic pages handler
    if (Options::hasFeature('pages')) {
        $app->routes->add('*', function() use ($app) {
            $path = (string) $app->request->path;
            if ($path === '/') {
                $pageID = 'home';
            } else {
                $hasSlash = substr($path, -1) === '/';
                $pathsList = InternalData\Pages::getPathsList(Options::hasFeature('users') && $app->bearCMS->currentUser->exists() ? 'all' : 'published');
                if ($hasSlash) {
                    $pageID = array_search($path, $pathsList);
                } else {
                    $pageID = array_search($path . '/', $pathsList);
                    if ($pageID !== false) {
                        return new App\Response\PermanentRedirect($app->request->base . $app->request->path . '/');
                    }
                }
            }
            if ($pageID !== false) {
                $content = '<component src="bearcms-elements" id="bearcms-page-' . $pageID . '" editable="true"/>';
                $response = new App\Response\HTML($content);
                $response->enableBearCMS = true;
                if ($pageID !== 'home') {
                    $response->bearCMSPageID = $pageID;
                }
                return $response;
            }
        });
    }
});

// Updates the Bear CMS components when created
if (Options::hasFeature('elements')) {
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
        $response->setContentType('text/html');
    } elseif ($response instanceof App\Response\TemporaryUnavailable) {
        $response->enableBearCMS = true;
        $response->setContentType('text/html');
    } elseif ($app->request->path === '/' && $response instanceof App\Response\HTML) {
        $response->enableBearCMS = true;
    }

    if (!isset($response->enableBearCMS)) {
        $response->enableBearCMS = false;
    }

    // Apply the template if the default theme is selected
    if ($app->bearCMS->currentTemplate->getID() === 'bearcms/default1') {
        if (empty($response->bearCMSSystemPage)) {
            $template = null;
            if ($response instanceof App\Response\HTML) {
                $template = '<component src="file:' . $context->dir . '/components/bearcms-default-template-1.php"/>';
            } elseif ($response instanceof App\Response\NotFound) {
                $template = '<component src="file:' . $context->dir . '/components/bearcms-default-template-1.php" mode="notFound"/>';
            } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                $template = '<component src="file:' . $context->dir . '/components/bearcms-default-template-1.php" mode="temporaryUnavailable"/>';
            }
            if ($template !== null) {
                $template = $app->components->process($template, ['recursive' => false]);
                $object = new ArrayObject();
                $object->content = $template;
                $app->hooks->execute('bearCMSDefaultTemplate1Created', $object);
                $template = $object->content;
                $template = $app->components->process($template);
                $object = new ArrayObject();
                $object->content = $template;
                $app->hooks->execute('bearCMSDefaultTemplate1Ready', $object);

                $content = $response->content;
                $object = new ArrayObject();
                $object->content = $content;
                $app->hooks->execute('bearCMSDefaultTemplate1ContentCreated', $object);
                $content = $object->content;
                $content = $app->components->process($content);
                $object = new ArrayObject();
                $object->content = $content;
                $app->hooks->execute('bearCMSDefaultTemplate1ContentReady', $object);

                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML(str_replace('{body}', $domDocument->createInsertTarget('templateBody'), $template));
                $domDocument->insertHTML($content, 'templateBody');
                $response->content = $domDocument->saveHTML();
            }
        }
    }
});

$app->hooks->add('responseCreated', function($response) use ($app, $context) {

    if (!(isset($response->enableBearCMS) && $response->enableBearCMS)) {
        return;
    }
    if (!empty($response->bearCMSSystemPage)) {
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
            $title = isset($page['titleTagContent']) ? trim($page['titleTagContent']) : '';
            if (!isset($title{0})) {
                $title = isset($page['name']) ? trim($page['name']) : '';
            }
            $descrption = isset($page['descriptionTagContent']) ? trim($page['descriptionTagContent']) : '';
            $keywords = isset($page['keywordsTagContent']) ? trim($page['keywordsTagContent']) : '';
        }
    } elseif (isset($response->bearCMSBlogPostID)) {
        $blogPost = $app->bearCMS->data->blog->getPost($response->bearCMSBlogPostID);
        if (is_array($blogPost)) {
            $title = isset($blogPost['titleTagContent']) ? trim($blogPost['titleTagContent']) : '';
            if (!isset($title{0})) {
                $title = isset($blogPost['title']) ? trim($blogPost['title']) : '';
            }
            $descrption = isset($blogPost['descriptionTagContent']) ? trim($blogPost['descriptionTagContent']) : '';
            $keywords = isset($blogPost['keywordsTagContent']) ? trim($blogPost['keywordsTagContent']) : '';
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
    $componentContent .= '<meta name="generator" content="Bear CMS ' . \BearCMS::VERSION . '"/>';
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
    $componentContent .= '<link rel="alternate" type="application/rss+xml" title="' . (isset($settings['title']) ? trim($settings['title']) : '') . '" href="' . $app->request->base . '/rss.xml" />';
    $componentContent .= '</head><body></body></html>';

    $domDocument = new HTML5DOMDocument();
    $domDocument->loadHTML($componentContent);
    $domDocument->insertHTML($response->content);
    $response->content = $app->components->process($domDocument->saveHTML());

    $currentUserExists = Options::hasServer() && Options::hasFeature('users') ? $app->bearCMS->currentUser->exists() : false;

    $externalLinksAreEnabled = !empty($settings['externalLinks']);
    if ($externalLinksAreEnabled) {
        $domDocument = new HTML5DOMDocument();
        $domDocument->loadHTML($response->content);
        $domDocument->insertHTML('<html><body><script src="' . htmlentities($context->assets->getUrl('assets/externalLinks.min.js')) . '"></script><script>bearCMS.externalLinks.initialize(' . ($externalLinksAreEnabled ? 1 : 0) . ',' . ($currentUserExists ? 1 : 0) . ');</script></body></html>');
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
        $app->bearCMS->currentUser->getSessionKey(),
        $app->bearCMS->currentUser->getPermissions(),
        get_class_vars('\BearCMS\Internal\Options'),
        $serverCookies
    ]);

    $adminUIData = $app->cache->get($cacheKey);
    if (!is_array($adminUIData)) {
        $adminUIData = Server::call('adminui', $requestArguments, true);
        $app->cache->set($cacheKey, $adminUIData, is_array($adminUIData) && isset($adminUIData['result']) ? 99999 : 10);
    }

    if (is_array($adminUIData) && isset($adminUIData['result']) && is_array($adminUIData['result']) && isset($adminUIData['result']['content']) && strlen($adminUIData['result']['content']) > 0) {
        $content = $adminUIData['result']['content'];
        if (Options::hasFeature('elements') && !empty(ElementsHelper::$editorData)) {
            $requestArguments = [];
            $requestArguments['data'] = json_encode(ElementsHelper::$editorData);

            $cacheKey = json_encode([
                'elementsEditor',
                $app->request->base,
                $requestArguments,
                $app->bearCMS->currentUser->getSessionKey(),
                $app->bearCMS->currentUser->getPermissions(),
                get_class_vars('\BearCMS\Internal\Options'),
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
                $domDocument->insertHTML('<html><body><script src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js')) . '"></script></body></html>');
                $content = $domDocument->saveHTML();
            } else {
                $response = new App\Response\TemporaryUnavailable();
            }
        }

        $content = Server::updateAssetsUrls($content, false);
        if (strpos($content, '{body}') !== false) {
            $content = str_replace('{body}', '<component src="data:base64,' . base64_encode($response->content) . '"/>', $content);
        } elseif (strpos($content, '{jsonEncodedBody}') !== false) {
            $content = str_replace('{jsonEncodedBody}', json_encode($app->components->process($response->content)), $content);
        }
        $response->content = $app->components->process($content);
    } else {
        $response = new App\Response\TemporaryUnavailable();
    }
}, ['priority' => 1000]);

if (Options::hasServer() && Options::hasFeature('users')) {
    $app->hooks->add('responseCreated', function() {
        Cookies::update();
    }, ['priority' => 1001]);
}
