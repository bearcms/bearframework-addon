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
use BearCMS\Internal\Options;
use BearCMS\Internal\CurrentTheme;
use BearCMS\Internal\Themes;
use IvoPetkov\HTML5DOMDocument;

$app = App::get();
$context = $app->context->get(__FILE__);
$options = $app->addons->get('bearcms/bearframework-addon')->options;

$context->classes
        ->add('BearCMS', 'classes/BearCMS.php')
        ->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php')
        ->add('BearCMS\Data', 'classes/BearCMS/Data.php')
        ->add('BearCMS\Data\Addon', 'classes/BearCMS/Data/Addon.php')
        ->add('BearCMS\Data\Addons', 'classes/BearCMS/Data/Addons.php')
        ->add('BearCMS\Data\BlogPost', 'classes/BearCMS/Data/BlogPost.php')
        ->add('BearCMS\Data\BlogPosts', 'classes/BearCMS/Data/BlogPosts.php')
        ->add('BearCMS\Data\Comment', 'classes/BearCMS/Data/Comment.php')
        ->add('BearCMS\Data\Comments', 'classes/BearCMS/Data/Comments.php')
        ->add('BearCMS\Data\CommentsThread', 'classes/BearCMS/Data/CommentsThread.php')
        ->add('BearCMS\Data\CommentsThreads', 'classes/BearCMS/Data/CommentsThreads.php')
        ->add('BearCMS\Data\ForumCategories', 'classes/BearCMS/Data/ForumCategories.php')
        ->add('BearCMS\Data\ForumCategory', 'classes/BearCMS/Data/ForumCategory.php')
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
        ->add('BearCMS\Themes', 'classes/BearCMS/Themes.php')
        ->add('BearCMS\Themes\Options', 'classes/BearCMS/Themes/Options.php')
        ->add('BearCMS\ElementsTypes', 'classes/BearCMS/ElementsTypes.php')
        ->add('BearCMS\Internal\Data', 'classes/BearCMS/Internal/Data.php')
        ->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php')
        ->add('BearCMS\Internal\Data\BlogPosts', 'classes/BearCMS/Internal/Data/BlogPosts.php')
        ->add('BearCMS\Internal\Data\Comments', 'classes/BearCMS/Internal/Data/Comments.php')
        ->add('BearCMS\Internal\Data\Files', 'classes/BearCMS/Internal/Data/Files.php')
        ->add('BearCMS\Internal\Data\ForumPosts', 'classes/BearCMS/Internal/Data/ForumPosts.php')
        ->add('BearCMS\Internal\Data\ForumPostsReplies', 'classes/BearCMS/Internal/Data/ForumPostsReplies.php')
        ->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php')
        ->add('BearCMS\Internal\Data\UploadsSize', 'classes/BearCMS/Internal/Data/UploadsSize.php')
        ->add('BearCMS\Internal\Data\Users', 'classes/BearCMS/Internal/Data/Users.php')
        ->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php')
        ->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php')
        ->add('BearCMS\Internal\CurrentTheme', 'classes/BearCMS/Internal/CurrentTheme.php')
        ->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php')
        ->add('BearCMS\Internal\Localization', 'classes/BearCMS/Internal/Localization.php')
        ->add('BearCMS\Internal\Options', 'classes/BearCMS/Internal/Options.php')
        ->add('BearCMS\Internal\PublicProfile', 'classes/BearCMS/Internal/PublicProfile.php')
        ->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php')
        ->add('BearCMS\Internal\TempClientData', 'classes/BearCMS/Internal/TempClientData.php')
        ->add('BearCMS\Internal\Themes', 'classes/BearCMS/Internal/Themes.php');

Options::set($options);

$app->addons->add('ivopetkov/users-bearframework-addon', [
    'useDataCache' => Options::$useDataCache
]);

$context->assets
        ->addDir('assets')
        ->addDir('components/bearcmsBlogPostsElement/assets')
        ->addDir('components/bearcmsCommentsElement/assets')
        ->addDir('components/bearcmsContactFormElement/assets')
        ->addDir('components/bearcmsForumPostsElement/assets');

$app->localization
        ->addDictionary('en', function() use ($context) {
            return include $context->dir . '/locales/en.php';
        })
        ->addDictionary('bg', function() use ($context) {
            return include $context->dir . '/locales/bg.php';
        })
        ->addDictionary('ru', function() use ($context) {
            return include $context->dir . '/locales/ru.php';
        });

$app->shortcuts
        ->add('bearCMS', function() {
            return new BearCMS();
        });

if ($app->request->method === 'GET') {
    if (strlen($app->config->assetsPathPrefix) > 0 && strpos($app->request->path, $app->config->assetsPathPrefix) === 0) {
        // skip
    } else {
        $cacheBundlePath = $app->request->path->get();
        InternalData::loadCacheBundle($cacheBundlePath);
        $app->hooks->add('responseSent', function() use ($cacheBundlePath) {
            InternalData::saveCacheBundle($cacheBundlePath);
        });
    }
}

$app->hooks
        ->add('dataItemChanged', function($key) use (&$app) {
            $prefixes = [
                'bearcms/pages/page/',
                'bearcms/blog/post/'
            ];
            foreach ($prefixes as $prefix) {
                if (strpos($key, $prefix) === 0) {
                    $dataBundleID = 'bearcmsdataprefix-' . $prefix;
                    if ($app->data->exists($key)) {
                        $app->dataBundle->addItem($dataBundleID, $key);
                    } else {
                        $app->dataBundle->removeItem($dataBundleID, $key);
                    }
                    break;
                }
            }
        });

if (Options::hasFeature('ELEMENTS') || Options::hasFeature('ELEMENTS_*')) {
    $contextDir = $context->dir;
    $app->components
            ->addAlias('bearcms-elements', 'file:' . $contextDir . '/components/bearcmsElements.php');
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
                    ],
                    [
                        'id' => 'width',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'align',
                        'type' => 'list',
                        'defaultValue' => 'left',
                        'options' => [
                            [
                                'value' => 'left'
                            ],
                            [
                                'value' => 'center'
                            ],
                            [
                                'value' => 'right'
                            ]
                        ]
                    ],
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
                    ],
                    [
                        'id' => 'width',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'align',
                        'type' => 'list',
                        'defaultValue' => 'left',
                        'options' => [
                            [
                                'value' => 'left'
                            ],
                            [
                                'value' => 'center'
                            ],
                            [
                                'value' => 'right'
                            ]
                        ]
                    ],
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
                    $domDocument = new HTML5DOMDocument();
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
                        'id' => 'source',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'sourceParentPageID',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'showHomeLink',
                        'type' => 'checkbox'
                    ],
                    [
                        'id' => 'homeLinkText',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'itemsType',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'items',
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
                    ],
                    [
                        'id' => 'count',
                        'type' => 'number'
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
        ->add('/rss.xml', [
            [$app->bearCMS, 'disabledCheck'],
            function() use ($app) {
                $settings = $app->bearCMS->data->settings->get();
                if ($settings['enableRSS']) {
                    return BearCMS\Internal\Controller::handleRSS();
                }
            }
        ])
        ->add('/sitemap.xml', [
            [$app->bearCMS, 'disabledCheck'],
            function() {
                return BearCMS\Internal\Controller::handleSitemap();
            }
        ])
        ->add('/robots.txt', [
            [$app->bearCMS, 'disabledCheck'],
            function() {
                return BearCMS\Internal\Controller::handleRobots();
            }
        ])
        ->add('/-link-rel-icon-*', [
            [$app->bearCMS, 'disabledCheck'],
            function() use ($app) {
                $size = str_replace('/-link-rel-icon-', '', (string) $app->request->path);
                if (is_numeric($size)) {
                    $settings = $app->bearCMS->data->settings->get();
                    $icon = $settings['icon'];
                    if (isset($icon{0})) {
                        $filename = $app->bearCMS->data->getRealFilename($icon);
                        $url = $app->assets->getUrl($filename, ['cacheMaxAge' => 999999999, 'width' => (int) $size, 'height' => (int) $size]);
                        return new App\Response\TemporaryRedirect($url);
                    }
                }
            }
        ]);

if (Options::hasFeature('COMMENTS')) {
    $app->serverRequests
            ->add('bearcms-comments-load-more', function($data) use ($app, $context) {
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
                    $forumCategory = $app->bearCMS->data->forumCategories->get($forumCategoryID);
                    if ($forumCategory !== null) {
                        $content = '<html>';
                        $content .= '<head>';
                        $content .= '<title>' . sprintf(__('bearcms.New post in %s'), htmlspecialchars($forumCategory->name)) . '</title>';
                        $content .= '</head>';
                        $content .= '<body>';
                        $content .= '<div class="bearcms-forum-post-page-title-container"><h1 class="bearcms-forum-post-page-title">' . sprintf(__('bearcms.New post in %s'), htmlspecialchars($forumCategory->name)) . '</h1></div>';
                        $content .= '<div class="bearcms-forum-post-page-content">';
                        $content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsForumPostsElement/forumPostNewForm.php" categoryID="' . htmlentities($forumCategoryID) . '" />';
                        $content .= '</div>';
                        $content .= '</body>';
                        $content .= '</html>';

                        $app->hooks->execute('bearCMSForumCategoryPageContentCreated', $content, $forumCategoryID);

                        $response = new App\Response\HTML($app->components->process($content));
                        $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex'));
                        $app->hooks->execute('bearCMSResponseCreated', $response);
                        $app->bearCMS->apply($response);
                        return $response;
                    }
                }
            ])
            ->add('/f/?/?/', [
                [$app->bearCMS, 'disabledCheck'],
                function() use ($app, $context) {
                    $forumPostSlug = $app->request->path->getSegment(1); // todo validate
                    $forumPostID = $app->request->path->getSegment(2);
                    $forumPost = $app->bearCMS->data->forumPosts->get($forumPostID);
                    if ($forumPost !== null) {
                        $content = '<html>';
                        $content .= '<head>';
                        $content .= '<title>' . htmlspecialchars($forumPost->title) . '</title>';
                        $content .= '</head>';
                        $content .= '<body>';
                        $content .= '<div class="bearcms-forum-post-page-title-container"><h1 class="bearcms-forum-post-page-title">' . htmlspecialchars($forumPost->title) . '</h1></div>';
                        //$content .= '<div class="bearcms-forum-post-page-date-container"><div class="bearcms-forum-post-page-date">' . BearCMS\Internal\Localization::getDate($forumPost->createdTime) . '</div></div>';
                        $content .= '<div class="bearcms-forum-post-page-content">';
                        $content .= '<component src="file:' . $context->dir . '/components/bearcmsForumPostsElement/forumPostRepliesList.php" includePost="true" forumPostID="' . htmlentities($forumPost->id) . '" />';
                        $content .= '</div>';
                        $content .= '<component src="form" filename="' . $context->dir . '/components/bearcmsForumPostsElement/forumPostReplyForm.php" forumPostID="' . htmlentities($forumPost->id) . '" />';
                        $content .= '</body>';
                        $content .= '</html>';

                        $forumPostID = $forumPost->id;
                        $app->hooks->execute('bearCMSForumPostPageContentCreated', $content, $forumPostID);

                        $response = new App\Response\HTML($app->components->process($content));
                        $app->hooks->execute('bearCMSResponseCreated', $response);
                        $app->bearCMS->apply($response);
                        return $response;
                    }
                }
    ]);
    $app->serverRequests
            ->add('bearcms-forumposts-load-more', function($data) use ($app, $context) {
                if (isset($data['serverData'], $data['serverData'])) {
                    $serverData = \BearCMS\Internal\TempClientData::get($data['serverData']);
                    if (is_array($serverData) && isset($serverData['componentHTML'])) {
                        $content = $app->components->process($serverData['componentHTML']);
                        return json_encode([
                            'content' => $content
                        ]);
                    }
                }
            });
}

if (Options::hasFeature('BLOG')) {
    $app->routes
            ->add(Options::$blogPagesPathPrefix . '?/', [
                [$app->bearCMS, 'disabledCheck'],
                function() use ($app) {
                    $slug = (string) $app->request->path->getSegment(1);
                    $slugsList = InternalData\BlogPosts::getSlugsList('published');
                    $blogPostID = array_search($slug, $slugsList);
                    if ($blogPostID === false && substr($slug, 0, 6) === 'draft-' && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) && $app->bearCMS->currentUser->exists()) {
                        $blogPost = $app->bearCMS->data->blogPosts->get(substr($slug, 6));
                        if ($blogPost !== null) {
                            $blogPostID = $blogPost['id'];
                        }
                    }
                    if ($blogPostID !== false) {
                        $blogPost = $app->bearCMS->data->blogPosts->get($blogPostID);
                        if ($blogPost !== null) {
                            $content = '<html><head>';
                            $title = isset($blogPost->titleTagContent) ? trim($blogPost->titleTagContent) : '';
                            if (!isset($title{0})) {
                                $title = isset($blogPost->title) ? trim($blogPost->title) : '';
                            }
                            $description = isset($blogPost->descriptionTagContent) ? trim($blogPost->descriptionTagContent) : '';
                            $keywords = isset($blogPost->keywordsTagContent) ? trim($blogPost->keywordsTagContent) : '';
                            if (isset($title{0})) {
                                $content .= '<title>' . htmlspecialchars($title) . '</title>';
                            }
                            if (isset($description{0})) {
                                $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                            }
                            if (isset($keywords{0})) {
                                $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                            }
                            $content .= '</head><body>';
                            $content .= '<div class="bearcms-blogpost-page-title-container"><h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost['title']) . '</h1></div>';
                            $content .= '<div class="bearcms-blogpost-page-date-container"><div class="bearcms-blogpost-page-date">' . ($blogPost['status'] === 'published' ? \BearCMS\Internal\Localization::getDate($blogPost['publishedTime']) : 'draft') . '</div></div>';
                            $content .= '<div class="bearcms-blogpost-page-content"><component src="bearcms-elements" id="bearcms-blogpost-' . $blogPostID . '"/></div>';
                            $content .= '</body></html>';

                            $app->hooks->execute('bearCMSBlogPostPageContentCreated', $content, $blogPostID);

                            $response = new App\Response\HTML($app->components->process($content));
                            $app->hooks->execute('bearCMSResponseCreated', $response);
                            $app->bearCMS->apply($response);
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
    $app->serverRequests
            ->add('bearcms-blogposts-load-more', function($data) use ($app, $context) {
                if (isset($data['serverData'], $data['serverData'])) {
                    $serverData = \BearCMS\Internal\TempClientData::get($data['serverData']);
                    if (is_array($serverData) && isset($serverData['componentHTML'])) {
                        $content = $app->components->process($serverData['componentHTML']);
                        return json_encode([
                            'content' => $content
                        ]);
                    }
                }
            });
}

// Register a home page and the dynamic pages handler
if (Options::hasFeature('PAGES')) {
    $app->routes
            ->add('*', [
                [$app->bearCMS, 'disabledCheck'],
                function() use ($app) {
                    $path = (string) $app->request->path;
                    //echo $path."\n\n";
                    if ($path === '/') {
                        if (Options::$autoCreateHomePage) {
                            $pageID = 'home';
                        } else {
                            $pageID = false;
                        }
                    } else {
                        $hasSlash = substr($path, -1) === '/';
                        $pathsList = InternalData\Pages::getPathsList((Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*')) && $app->bearCMS->currentUser->exists() ? 'all' : 'published');
                        array_walk($pathsList, function(&$value) {
                                    $value = implode('/', array_map('urlencode', explode('/', $value)));
                                });
                                //print_r($pathsList);exit;
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
                            if (isset($title{0})) {
                                $content .= '<title>' . htmlspecialchars($title) . '</title>';
                            }
                            if (isset($description{0})) {
                                $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                            }
                            if (isset($keywords{0})) {
                                $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                            }
                            $content .= '</head><body>';
                            $content .= '<component src="bearcms-elements" id="bearcms-page-' . $pageID . '" editable="true"/>';
                            $content .= '</body></html>';

                            $app->hooks->execute('bearCMSPageContentCreated', $content, $pageID);

                            $response = new App\Response\HTML($app->components->process($content));
                            $app->hooks->execute('bearCMSResponseCreated', $response);
                            $app->bearCMS->apply($response);
                            return $response;
                        }
                    }
                }
    ]);
}

if (Options::hasFeature('ELEMENTS') || Options::hasFeature('ELEMENTS_*')) {
    $app->hooks
            ->add('componentCreated', function($component) {
                // Updates the Bear CMS components when created
                if ($component->src === 'bearcms-elements') {
                    ElementsHelper::updateContainerComponent($component);
                } elseif (isset(BearCMS\Internal\ElementsHelper::$elementsTypesFilenames[$component->src])) {
                    $component->setAttribute('bearcms-internal-attribute-type', BearCMS\Internal\ElementsHelper::$elementsTypesCodes[$component->src]);
                    $component->setAttribute('bearcms-internal-attribute-filename', BearCMS\Internal\ElementsHelper::$elementsTypesFilenames[$component->src]);
                    ElementsHelper::updateElementComponent($component);
                }
            });
    $app->serverRequests
            ->add('bearcms-elements-load-more', function($data) use ($app) {
                if (isset($data['serverData'])) {
                    $serverData = \BearCMS\Internal\TempClientData::get($data['serverData']);
                    if (is_array($serverData) && isset($serverData['componentHTML'])) {
                        $content = $app->components->process($serverData['componentHTML']);
                        $editorContent = ElementsHelper::getEditableElementsHtml();
                        return json_encode([
                            'content' => $content,
                            'editorContent' => (isset($editorContent[0]) ? $editorContent : ''),
                            'nextLazyLoadData' => (string) ElementsHelper::$lastLoadMoreServerData
                        ]);
                    }
                }
            });
}

$app->hooks
        ->add('responseCreated', function($response) use ($app) {
            if ($response instanceof App\Response\NotFound) {
                $response->headers->set($response->headers->make('Content-Type', 'text/html'));
                $app->bearCMS->apply($response);
            } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                $response->headers->set($response->headers->make('Content-Type', 'text/html'));
                $app->bearCMS->apply($response);
            }
        })
        ->add('assetPrepare', function($filename, $options, &$returnValue, &$preventDefault) use ($app, $context) { // Download the server files
            $serverUrl = \BearCMS\Internal\Options::$serverUrl;
            $matchingDir = $context->dir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 's' . DIRECTORY_SEPARATOR;
            if (strpos($filename, $matchingDir) === 0) {
                $preventDefault = true;
                $fileServerUrl = $serverUrl . str_replace('\\', '/', str_replace($matchingDir, '', $filename));
                $returnValue = null;
                $fileInfo = pathinfo($fileServerUrl);
                if (isset($fileInfo['extension'])) {
                    $tempFileKey = '.temp/bearcms/serverfiles/' . md5($fileServerUrl) . '.' . $fileInfo['extension'];
                    $tempFilename = $app->data->getFilename($tempFileKey);
                    if ($app->data->exists($tempFileKey) && false) {
                        $returnValue = $tempFilename;
                    } else {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $fileServerUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        $response = curl_exec($ch);
                        if ((int) curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200 && strlen($response) > 0) {
                            $app->data->set($app->data->make($tempFileKey, $response));
                            $returnValue = $tempFilename;
                        } else {
                            throw new Exception('Cannot download Bear CMS Server file (' . $fileServerUrl . ')');
                        }
                        curl_close($ch);
                    }
                }
            }
        });

if (!(isset($options['addDefaultThemes']) && $options['addDefaultThemes'] === false)) {
    require $context->dir . '/themes/theme1/index.php';
}

if (Options::hasServer() && (Options::hasFeature('USERS') || Options::hasFeature('USERS_LOGIN_*'))) {
    $app->hooks
            ->add('responseCreated', function($response) use ($app) {
                Cookies::apply($response);
                if (InternalData::$hasContentChange) {
                    $app->hooks->execute('bearCMSContentChanged');
                }
            });
}