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

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('BearCMS', 'classes/BearCMS.php')
        ->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php')
        ->add('BearCMS\CurrentTheme', 'classes/BearCMS/CurrentTheme.php')
        ->add('BearCMS\CurrentThemeOptions', 'classes/BearCMS/CurrentThemeOptions.php')
        ->add('BearCMS\Data', 'classes/BearCMS/Data.php')
        ->add('BearCMS\Data\Addon', 'classes/BearCMS/Data/Addon.php')
        ->add('BearCMS\Data\Addons', 'classes/BearCMS/Data/Addons.php')
        ->add('BearCMS\Data\BlogPost', 'classes/BearCMS/Data/BlogPost.php')
        ->add('BearCMS\Data\BlogPosts', 'classes/BearCMS/Data/BlogPosts.php')
        ->add('BearCMS\Data\Comment', 'classes/BearCMS/Data/Comment.php')
        ->add('BearCMS\Data\Comments', 'classes/BearCMS/Data/Comments.php')
        ->add('BearCMS\Data\CommentsThread', 'classes/BearCMS/Data/CommentsThread.php')
        ->add('BearCMS\Data\CommentsThreads', 'classes/BearCMS/Data/CommentsThreads.php')
        ->add('BearCMS\Data\ForumPost', 'classes/BearCMS/Data/ForumPost.php')
        ->add('BearCMS\Data\ForumPosts', 'classes/BearCMS/Data/ForumPosts.php')
        ->add('BearCMS\Data\ForumPostReply', 'classes/BearCMS/Data/ForumPostReply.php')
        ->add('BearCMS\Data\ForumPostsReplies', 'classes/BearCMS/Data/ForumPostsReplies.php')
        ->add('BearCMS\Data\Page', 'classes/BearCMS/Data/Page.php')
        ->add('BearCMS\Data\Pages', 'classes/BearCMS/Data/Pages.php')
        ->add('BearCMS\Data\Settings', 'classes/BearCMS/Data/Settings.php')
        ->add('BearCMS\Data\Themes', 'classes/BearCMS/Data/Themes.php')
        ->add('BearCMS\Data\User', 'classes/BearCMS/Data/User.php')
        ->add('BearCMS\Data\Users', 'classes/BearCMS/Data/Users.php')
        ->add('BearCMS\DataList', 'classes/BearCMS/DataList.php')
        ->add('BearCMS\DataObject', 'classes/BearCMS/DataObject.php')
        ->add('BearCMS\DataSchema', 'classes/BearCMS/DataSchema.php')
        ->add('BearCMS\ElementsTypes', 'classes/BearCMS/ElementsTypes.php')
        ->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php')
        ->add('BearCMS\Internal\Data\BlogPosts', 'classes/BearCMS/Internal/Data/BlogPosts.php')
        ->add('BearCMS\Internal\Data\Comments', 'classes/BearCMS/Internal/Data/Comments.php')
        ->add('BearCMS\Internal\Data\Files', 'classes/BearCMS/Internal/Data/Files.php')
        ->add('BearCMS\Internal\Data\ForumPosts', 'classes/BearCMS/Internal/Data/ForumPosts.php')
        ->add('BearCMS\Internal\Data\ForumPostsReplies', 'classes/BearCMS/Internal/Data/ForumPostsReplies.php')
        ->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php')
        ->add('BearCMS\Internal\Data\Themes', 'classes/BearCMS/Internal/Data/Themes.php')
        ->add('BearCMS\Internal\Data\Users', 'classes/BearCMS/Internal/Data/Users.php')
        ->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php')
        ->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php')
        ->add('BearCMS\Internal\Dictionary', 'classes/BearCMS/Internal/Dictionary.php')
        ->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php')
        ->add('BearCMS\Internal\Localization', 'classes/BearCMS/Internal/Localization.php')
        ->add('BearCMS\Internal\Options', 'classes/BearCMS/Internal/Options.php')
        ->add('BearCMS\Internal\PublicProfile', 'classes/BearCMS/Internal/PublicProfile.php')
        ->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php')
        ->add('BearCMS\Internal\TempClientData', 'classes/BearCMS/Internal/TempClientData.php');

$context->assets
        ->addDir('assets')
        ->addDir('components/bearcmsCommentsElement/assets')
        ->addDir('components/bearcmsContactFormElement/assets')
        ->addDir('components/bearcmsForumPostsElement/assets');

$app->shortcuts
        ->add('bearCMS', function() {
            return new BearCMS();
        });

Options::set($app->addons->get('bearcms/bearframework-addon')->options);

$app->hooks->add('initialized', function() use ($app, $context) {

    if (Options::hasFeature('ELEMENTS') || Options::hasFeature('ELEMENTS_*')) {
        $contextDir = $context->dir;
        $app->components->addAlias('bearcms-elements', 'file:' . $contextDir . '/components/bearcmsElements.php');
        $app->bearCMS->elementsTypes
                ->add('heading', [
                    'componentSrc' => 'bearcms-heading-element',
                    'componentFilename' => $contextDir . '/components/bearcmsHeadingElement.php',
                    'fields' => [
                        [
                            'id' => 'size',
                            'type' => 'list',
                            'defaultValue' => 'large',
                            'options' => [
                                [
                                    'value' => 'large'
                                ],
                                [
                                    'value' => 'medium'
                                ],
                                [
                                    'value' => 'small'
                                ]
                            ]
                        ],
                        [
                            'id' => 'text',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('text', [
                    'componentSrc' => 'bearcms-text-element',
                    'componentFilename' => $contextDir . '/components/bearcmsTextElement.php',
                    'fields' => [
                        [
                            'id' => 'text',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('link', [
                    'componentSrc' => 'bearcms-link-element',
                    'componentFilename' => $contextDir . '/components/bearcmsLinkElement.php',
                    'fields' => [
                        [
                            'id' => 'url',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'text',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'title',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('video', [
                    'componentSrc' => 'bearcms-video-element',
                    'componentFilename' => $contextDir . '/components/bearcmsVideoElement.php',
                    'fields' => [
                        [
                            'id' => 'url',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'filename',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('image', [
                    'componentSrc' => 'bearcms-image-element',
                    'componentFilename' => $contextDir . '/components/bearcmsImageElement.php',
                    'fields' => [
                        [
                            'id' => 'filename',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'title',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'onClick',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'url',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('imageGallery', [
                    'componentSrc' => 'bearcms-image-gallery-element',
                    'componentFilename' => $contextDir . '/components/bearcmsImageGalleryElement.php',
                    'fields' => [
                        [
                            'id' => 'type',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'columnsCount',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'imageSize',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'imageAspectRatio',
                            'type' => 'textbox'
                        ]
                    ],
                    'updateComponentFromData' => function($component, $data) {
                        if (isset($data['files']) && is_array($data['files'])) {
                            $innerHTML = '';
                            foreach ($data['files'] as $file) {
                                if (isset($file['filename'])) {
                                    $innerHTML .= '<file filename="' . htmlentities($file['filename']) . '"/>';
                                }
                            }
                            $component->innerHTML = $innerHTML;
                        }
                        return $component;
                    },
                    'updateDataFromComponent' => function($component, $data) {
                        $domDocument = new IvoPetkov\HTML5DOMDocument();
                        $domDocument->loadHTML($component->innerHTML);
                        $files = [];
                        $filesElements = $domDocument->querySelectorAll('file');
                        foreach ($filesElements as $fileElement) {
                            $files[] = ['filename' => $fileElement->getAttribute('filename')];
                        }
                        $data['files'] = $files;
                        return $data;
                    }
                ])
                ->add('navigation', [
                    'componentSrc' => 'bearcms-navigation-element',
                    'componentFilename' => $contextDir . '/components/bearcmsNavigationElement.php',
                    'fields' => [
                        [
                            'id' => 'type',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'pageID',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('html', [
                    'componentSrc' => 'bearcms-html-element',
                    'componentFilename' => $contextDir . '/components/bearcmsHtmlElement.php',
                    'fields' => [
                        [
                            'id' => 'code',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('blogPosts', [
                    'componentSrc' => 'bearcms-blog-posts-element',
                    'componentFilename' => $contextDir . '/components/bearcmsBlogPostsElement.php',
                    'fields' => [
                        [
                            'id' => 'type',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'showDate',
                            'type' => 'checkbox'
                        ],
                        [
                            'id' => 'limit',
                            'type' => 'number'
                        ]
                    ]
                ])
                ->add('heading', [
                    'componentSrc' => 'bearcms-heading-element',
                    'componentFilename' => $contextDir . '/components/bearcmsHeadingElement.php',
                    'fields' => [
                        [
                            'id' => 'text',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('comments', [
                    'componentSrc' => 'bearcms-comments-element',
                    'componentFilename' => $contextDir . '/components/bearcmsCommentsElement.php',
                    'fields' => [
                        [
                            'id' => 'threadID',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'count',
                            'type' => 'number'
                        ]
                    ],
                    'onDelete' => function($data) use ($app) {
                        if (isset($data['threadID'])) {
                            $app->data->delete('bearcms/comments/thread/' . md5($data['threadID']) . '.json');
                        }
                    }
                ])
                ->add('contactForm', [
                    'componentSrc' => 'bearcms-contact-form-element',
                    'componentFilename' => $contextDir . '/components/bearcmsContactFormElement.php',
                    'fields' => [
                        [
                            'id' => 'email',
                            'type' => 'textbox'
                        ]
                    ]
                ])
                ->add('forumPosts', [
                    'componentSrc' => 'bearcms-forum-posts-element',
                    'componentFilename' => $contextDir . '/components/bearcmsForumPostsElement.php',
                    'fields' => [
                        [
                            'id' => 'categoryID',
                            'type' => 'textbox'
                        ]
                    ]
        ]);
    }

    // Load the CMS managed addons
    if (Options::hasFeature('ADDONS')) {
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
                $_addonOptions = $_addonData->options;
                if (isset($_addonOptions['bearCMS']) && is_array($_addonOptions['bearCMS']) && isset($_addonOptions['bearCMS']['assetsDirs']) && is_array($_addonOptions['bearCMS']['assetsDirs'])) {
                    foreach ($_addonOptions['bearCMS']['assetsDirs'] as $dir) {
                        if (is_string($dir)) {
                            $app->assets->addDir($addonDir . $dir);
                        }
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
    if (Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_DEFAULT'))) {
        $cookies = Cookies::getList(Cookies::TYPE_SERVER);
        if (isset($cookies['_a']) && !$app->bearCMS->currentUser->exists()) {
            Server::call('autologin', [], true);
        }
    }

    // Register the system pages
    if (Options::hasServer()) {
        if (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_DEFAULT')) {
            $app->routes->add([Options::$adminPagesPathPrefix . 'loggedin/'], function() use ($app) {
                return new App\Response\TemporaryRedirect($app->request->base . '/');
            });
            $app->routes->add([Options::$adminPagesPathPrefix, Options::$adminPagesPathPrefix . '*/'], ['BearCMS\Internal\Controller', 'handleAdminPage']);
            $app->routes->add([rtrim(Options::$adminPagesPathPrefix, '/'), Options::$adminPagesPathPrefix . '*'], function() use ($app) {
                return new App\Response\PermanentRedirect($app->request->base . $app->request->path . '/');
            });
        }
        if (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) {
            $app->routes->add('/-aj/', ['BearCMS\Internal\Controller', 'handleAjax'], ['POST']);
            $app->routes->add('/-au/', ['BearCMS\Internal\Controller', 'handleFileUpload'], ['POST']);
        }
    }

    // Register the file handlers
    if (Options::hasFeature('FILES')) {
        $app->routes
                ->add('/files/preview/*', ['BearCMS\Internal\Controller', 'handleFilePreview'])
                ->add('/files/download/*', ['BearCMS\Internal\Controller', 'handleFileDownload']);
    }

    // Register some other pages
    $app->routes
            ->add('/rss.xml', ['BearCMS\Internal\Controller', 'handleRSS'])
            ->add('/sitemap.xml', ['BearCMS\Internal\Controller', 'handleSitemap'])
            ->add('/robots.txt', ['BearCMS\Internal\Controller', 'handleRobots']);

    if (Options::hasFeature('COMMENTS')) {
        $app->serverRequests->add('bearcms-comments-load-more', function($data) use ($app, $context) {
            if (isset($data['serverData'], $data['listElementID'], $data['listCommentsCount'])) {
                $listElementID = (string) $data['listElementID'];
                $listCommentsCount = (int) $data['listCommentsCount'];
                $serverData = \BearCMS\Internal\TempClientData::get($data['serverData']);
                if (is_array($serverData) && isset($serverData['threadID'])) {
                    $threadID = $serverData['threadID'];
                    $listContent = $app->components->process('<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($listCommentsCount) . '" threadID="' . htmlentities($threadID) . '" />');
                    return json_encode([
                        'listElementID' => $listElementID,
                        'listContent' => $listContent
                    ]);
                }
            }
        });
    }

    if (Options::hasFeature('FORUMS')) {
        $app->routes
                ->add('/f/?/', [
                    [$app->bearCMS, 'disabledCheck'],
                    function() use ($app, $context) {
                        $forumCategoryID = $app->request->path->getSegment(1);
                        //todo slug
                        //$forumCategory = $app->bearCMS->data->forumCategories->get($forumCategoryID);
                        //if ($forumCategory !== null) {
                        $content = '<component src="form" filename="' . $context->dir . '/components/bearcmsForumPostsElement/forumPostNewForm.php" categoryID="' . htmlentities($forumCategoryID) . '" />';
                        $response = new App\Response\HTML($app->components->process($content));
                        $app->bearCMS->enableUI($response);
                        $app->bearCMS->applyTheme($response);
                        $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex'));
                        return $response;
                        //}
                    }
                ])
                ->add('/f/?/?/', [
                    [$app->bearCMS, 'disabledCheck'],
                    function() use ($app, $context) {
                        $forumPostSlug = $app->request->path->getSegment(1); // todo validate
                        $forumPostID = $app->request->path->getSegment(2);
                        $forumPost = $app->bearCMS->data->forumPosts->get($forumPostID);
                        if ($forumPost !== null) {
                            $content = '';
                            $content = '<div class="bearcms-forum-post-page-title-container"><h1 class="bearcms-forum-post-page-title">' . htmlspecialchars($forumPost->title) . '</h1></div>';
                            $content .= '<div class="bearcms-forum-post-page-date-container"><div class="bearcms-forum-post-page-date">' . BearCMS\Internal\Localization::getDate($forumPost->createdTime) . '</div></div>';
                            $content .= '<div class="bearcms-forum-post-page-content">' . htmlspecialchars($forumPost->text) . '</div>';

                            $content .= '<component src="file:' . $context->dir . '/components/bearcmsForumPostsElement/forumPostRepliesList.php" forumPostID="' . htmlentities($forumPost->id) . '" />';
                            $content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsForumPostsElement/forumPostReplyForm.php" forumPostID="' . htmlentities($forumPost->id) . '" />';
                            $response = new App\Response\HTML($content);
                            $app->bearCMS->enableUI($response);
                            $app->bearCMS->applyTheme($response);
                            $response->bearCMSForumPostID = $forumPost->id;
                            return $response;
                        }
                    }
        ]);
    }

    if (Options::hasFeature('BLOG')) {
        $app->routes
                ->add(Options::$blogPagesPathPrefix . '?/', [
                    [$app->bearCMS, 'disabledCheck'],
                    function() use ($app) {
                        $slug = (string) $app->request->path->getSegment(1);
                        $slugsList = InternalData\Blog::getSlugsList('published');
                        $blogPostID = array_search($slug, $slugsList);
                        if ($blogPostID === false && substr($slug, 0, 6) === 'draft-' && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) && $app->bearCMS->currentUser->exists()) {
                            $blogPost = $app->bearCMS->data->blogPosts->getPost(substr($slug, 6));
                            if ($blogPost !== null) {
                                $blogPostID = $blogPost['id'];
                            }
                        }
                        if ($blogPostID !== false) {
                            $blogPost = $app->bearCMS->data->blogPosts->getPost($blogPostID);
                            if ($blogPost !== null) {
                                $content = '<html><head>';
                                $title = isset($page->titleTagContent) ? trim($page->titleTagContent) : '';
                                if (!isset($title{0})) {
                                    $title = isset($page->title) ? trim($page->title) : '';
                                }
                                $description = isset($page->descriptionTagContent) ? trim($page->descriptionTagContent) : '';
                                $keywords = isset($page->keywordsTagContent) ? trim($page->keywordsTagContent) : '';
                                $content .= '<title>' . htmlspecialchars($title) . '</title>';
                                $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                                $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                                $content .= '</head><body>';
                                $content = '<div class="bearcms-blogpost-page-title-container"><h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost['title']) . '</h1></div>';
                                $content .= '<div class="bearcms-blogpost-page-date-container"><div class="bearcms-blogpost-page-date">' . ($blogPost['status'] === 'published' ? date('F j, Y', $blogPost['publishedTime']) : 'draft') . '</div></div>';
                                $content .= '<div class="bearcms-blogpost-page-content"><component src="bearcms-elements" id="bearcms-blogpost-' . $blogPostID . '"/></div>';
                                $content .= '</body></html>';
                                $response = new App\Response\HTML($content);
                                $app->bearCMS->enableUI($response);
                                $app->bearCMS->applyTheme($response);
                                return $response;
                            }
                        }
                    }
                ])
                ->add(Options::$blogPagesPathPrefix . '?', [
                    [$app->bearCMS, 'disabledCheck'],
                    function() use ($app) {
                        return new App\Response\PermanentRedirect($app->request->base . $app->request->path . '/');
                    }
        ]);
    }

    // Register a home page and the dynamic pages handler
    if (Options::hasFeature('PAGES')) {
        $app->routes
                ->add('*', function() use ($app) {
                    $path = (string) $app->request->path;
                    if ($path === '/') {
                        if (Options::$autoCreateHomePage) {
                            $pageID = 'home';
                        } else {
                            $pageID = false;
                        }
                    } else {
                        $hasSlash = substr($path, -1) === '/';
                        $pathsList = InternalData\Pages::getPathsList((Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) && $app->bearCMS->currentUser->exists() ? 'all' : 'published');
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
                        $response = $app->bearCMS->disabledCheck();
                        if ($response !== null) {
                            return $response;
                        }
                        $found = false;
                        if ($pageID === 'home') {
                            $settings = $app->bearCMS->data->settings->get();
                            $title = trim($settings->title);
                            $description = trim($settings->description);
                            $keywords = trim($settings->keywords);
                            $found = true;
                        } else {
                            $page = $app->bearCMS->data->pages->get($pageID);
                            if ($page !== null) {
                                $title = isset($page->titleTagContent) ? trim($page->titleTagContent) : '';
                                if (!isset($title{0})) {
                                    $title = isset($page->name) ? trim($page->name) : '';
                                }
                                $description = isset($page->descriptionTagContent) ? trim($page->descriptionTagContent) : '';
                                $keywords = isset($page->keywordsTagContent) ? trim($page->keywordsTagContent) : '';
                                $found = true;
                            }
                        }
                        if ($found) {
                            $content = '<html><head>';
                            $content .= '<title>' . htmlspecialchars($title) . '</title>';
                            $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                            $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                            $content .= '</head><body>';
                            $content .= '<component src="bearcms-elements" id="bearcms-page-' . $pageID . '" editable="true"/>';
                            $content .= '</body></html>';
                            $response = new App\Response\HTML($content);
                            $app->bearCMS->enableUI($response);
                            $app->bearCMS->applyTheme($response);
                            return $response;
                        }
                    }
                });
    }
});

$componentCreatedStatus = [];
// Updates the Bear CMS components when created
if (Options::hasFeature('ELEMENTS') || Options::hasFeature('ELEMENTS_*')) {
    $app->hooks
            ->add('componentCreated', function($component) use ($app, &$componentCreatedStatus) {
                if ($component->src === 'bearcms-elements') {
                    ElementsHelper::updateContainerComponent($component);

//            $componentHTML = (string) $component;
//            $cacheKey = 'bearcms-elements-' . $componentHTML;
//            if (!isset($componentCreatedStatus[$cacheKey])) {
//                $content = $app->cache->getValue($cacheKey);
//                if ($content === null) {
//                    $componentCreatedStatus[$cacheKey] = 1;
//                    $content = $app->components->process($componentHTML);
//                    unset($componentCreatedStatus[$cacheKey]);
//                    $app->cache->set($app->cache->make($cacheKey, $content));
//                }
//                $component->src = 'data:base64,' . base64_encode($content);
//            }
                } elseif (isset(BearCMS\Internal\ElementsHelper::$elementsTypesFilenames[$component->src])) {
                    $component->setAttribute('bearcms-internal-attribute-type', BearCMS\Internal\ElementsHelper::$elementsTypesCodes[$component->src]);
                    $component->setAttribute('bearcms-internal-attribute-filename', BearCMS\Internal\ElementsHelper::$elementsTypesFilenames[$component->src]);
                    ElementsHelper::updateElementComponent($component);

//            $componentHTML = (string) $component;
//            $cacheKey = 'bearcms-elements-' . $componentHTML;
//            if (!isset($componentCreatedStatus[$cacheKey])) {
//                $content = $app->cache->getValue($cacheKey);
//                if ($content === null) {
//                    $componentCreatedStatus[$cacheKey] = 1;
//                    $content = $app->components->process($componentHTML);
//                    unset($componentCreatedStatus[$cacheKey]);
//                    $app->cache->set($app->cache->make($cacheKey, $content));
//                }
//                $component->src = 'data:base64,' . base64_encode($content);
//            }
                }
            });
}

$app->hooks
        ->add('responseCreated', function($response) use ($app) {
            if ($response instanceof App\Response\NotFound) {
                $app->bearCMS->enableUI($response);
                $app->bearCMS->applyTheme($response);
                $response->headers->set($response->headers->make('Content-Type', 'text/html'));
            } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                $app->bearCMS->enableUI($response);
                $app->bearCMS->applyTheme($response);
                $response->headers->set($response->headers->make('Content-Type', 'text/html'));
            } elseif ($app->request->path === '/' && $response instanceof App\Response\HTML) {
                $app->bearCMS->enableUI($response);
                $app->bearCMS->applyTheme($response);
            }
            if ($response instanceof App\Response\HTML) {
                $response->content = $app->components->process($response->content);
            }
        })
        ->add('assetPrepare', function($data) use ($app, $context) {
            $serverUrl = \BearCMS\Internal\Options::$serverUrl;
            $matchingDir = $context->dir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 's' . DIRECTORY_SEPARATOR;
            if (strpos($data->filename, $matchingDir) === 0) {
                $fileServerUrl = $serverUrl . str_replace('\\', '/', str_replace($matchingDir, '', $data->filename));
                $data->filename = null;
                $fileInfo = pathinfo($fileServerUrl);
                if (isset($fileInfo['extension'])) {
                    $tempFileKey = '.temp/bearcms/serverfiles/' . md5($fileServerUrl) . '.' . $fileInfo['extension'];
                    $tempFilename = $app->data->getFilename($tempFileKey);
                    if ($app->data->exists($tempFileKey)) {
                        $data->filename = $tempFilename;
                    } else {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $fileServerUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        $response = curl_exec($ch);
                        if ((int) curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
                            $app->data->set($app->data->make($tempFileKey, $response));
                            $data->filename = $tempFilename;
                        }
                        curl_close($ch);
                    }
                }
            }
        });

require $context->dir . '/themes/default1/autoload.php';

if ($app->bearCMS->currentTheme->getID() === 'bearcms/default1') {
    require $context->dir . '/themes/default1/index.php';
}

$app->hooks
        ->add('responseCreated', function($response) use ($app, $context) {
            if (!isset($response->enableBearCMSUI)) {
                return;
            }

            $componentContent = '<html><head>';

            $currentUserExists = Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) ? $app->bearCMS->currentUser->exists() : false;
            $settings = $app->bearCMS->data->settings->get();
            if (!$response->headers->exists('Cache-Control')) {
                $response->headers->set($response->headers->make('Cache-Control', 'private, max-age=0'));
            }
            $componentContent .= '<meta name="generator" content="Bear Framework v' . App::VERSION . ', Bear CMS v' . \BearCMS::VERSION . '"/>';
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
            $componentContent .= '</head><body>';

            if ($response instanceof \BearFramework\App\Response\HTML) { // is not temporary disabled
                $externalLinksAreEnabled = !empty($settings['externalLinks']);
                if ($externalLinksAreEnabled || $currentUserExists) {
                    $componentContent .= '<script src="' . htmlentities($context->assets->getUrl('assets/externalLinks.min.js')) . '" async onload="bearCMS.externalLinks.initialize(' . ($externalLinksAreEnabled ? 1 : 0) . ',' . ($currentUserExists ? 1 : 0) . ');"></script>';
                }
            }
            $componentContent .= '</body></html>';

            $domDocument = new HTML5DOMDocument();
            $domDocument->loadHTML($componentContent);
            $domDocument->insertHTML($response->content);
            $response->content = $app->components->process($domDocument->saveHTML());

            if (!$currentUserExists) {
                return;
            }

            $serverCookies = Cookies::getList(Cookies::TYPE_SERVER);
            if (!empty($serverCookies['tmcs']) || !empty($serverCookies['tmpr'])) {
                ElementsHelper::$editorData = [];
            }

            $requestArguments = [];
            $requestArguments['hasEditableElements'] = empty(ElementsHelper::$editorData) ? '0' : '1';
            $requestArguments['hasEditableContainers'] = '0';
            $requestArguments['isDisabled'] = $settings->disabled ? '1' : '0';
            foreach (ElementsHelper::$editorData as $itemData) {
                if ($itemData[0] === 'container') {
                    $requestArguments['hasEditableContainers'] = '1';
                }
            }

            $cacheKey = json_encode([
                'adminUI',
                $app->request->base,
                $requestArguments,
                $app->bearCMS->currentUser->getSessionKey(),
                $app->bearCMS->currentUser->getPermissions(),
                get_class_vars('\BearCMS\Internal\Options'),
                $serverCookies,
                uniqid()//todo temp
            ]);

            $adminUIData = $app->cache->getValue($cacheKey);
            if (!is_array($adminUIData)) {
                $adminUIData = Server::call('adminui', $requestArguments, true);
                $cacheItem = $app->cache->make($cacheKey, $adminUIData);
                $cacheItem->ttl = is_array($adminUIData) && isset($adminUIData['result']) ? 99999 : 10;
                $app->cache->set($cacheItem);
            }

            if (is_array($adminUIData) && isset($adminUIData['result']) && is_array($adminUIData['result']) && isset($adminUIData['result']['content']) && strlen($adminUIData['result']['content']) > 0) {
                $content = $adminUIData['result']['content'];
                $contentToInsert = null;
                if ((Options::hasFeature('ELEMENTS') || Options::hasFeature('ELEMENTS_*')) && !empty(ElementsHelper::$editorData)) {
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
                    $elementsEditorData = $app->cache->getValue($cacheKey);
                    if (!is_array($elementsEditorData)) {
                        $elementsEditorData = Server::call('elementseditor', $requestArguments, true);
                        $cacheItem = $app->cache->make($cacheKey, $elementsEditorData);
                        $cacheItem->ttl = is_array($elementsEditorData) && isset($elementsEditorData['result']) ? 99999 : 10;
                        $app->cache->set($cacheItem);
                    }

                    if (is_array($elementsEditorData) && isset($elementsEditorData['result']) && is_array($elementsEditorData['result']) && isset($elementsEditorData['result']['content'])) {
                        $contentToInsert = $elementsEditorData['result']['content'];
                    } else {
                        //$response = new App\Response\TemporaryUnavailable();
                    }
                }
                // It's needed even when there is no editable zone on the current page (editing a blog post for instance)
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML($content);
                if ($contentToInsert !== null) {
                    $domDocument->insertHTML($contentToInsert);
                }
                $domDocument->insertHTML('<html><body><script src="' . htmlentities($context->assets->getUrl('assets/HTML5DOMDocument.min.js')) . '"></script></body></html>');
                $content = $domDocument->saveHTML();

                $content = Server::updateAssetsUrls($content, false);
                if (strpos($content, '{body}') !== false) {
                    $content = str_replace('{body}', '<component src="data:base64,' . base64_encode($response->content) . '"/>', $content);
                } elseif (strpos($content, '{jsonEncodedBody}') !== false) {
                    $content = str_replace('{jsonEncodedBody}', json_encode($app->components->process($response->content)), $content);
                }
                $response->content = $app->components->process($content);
            } else {
                //$response = new App\Response\TemporaryUnavailable();
            }
        }, ['priority' => 1000]);

if (Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*'))) {
    $app->hooks
            ->add('responseCreated', function($response) {
                Cookies::update($response);
            }, ['priority' => 1001]);
}
