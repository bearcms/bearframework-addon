<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\ApplyContext;
use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;
use IvoPetkov\HTML5DOMDocument;
use BearCMS\Internal\Blog;
use BearCMS\Internal\Comments;
use BearCMS\Internal\Elements;
use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\ElementsTypes;
use BearCMS\Internal\Pages;
use BearCMS\Internal\Sitemap;
use BearCMS\Internal\CommentsLocations;
use BearCMS\Internal\TextUtilities;

/**
 * 
 * @property-read \BearCMS\CurrentUser $currentUser Information about the current CMS administrator.
 * @property-read \BearCMS\Themes $themes Information about the enabled Bear CMS themes.
 * @property-read \BearCMS\Addons $addons Information about the enabled Bear CMS addons.
 * @property-read \BearCMS\Data $data Access to the CMS data.
 */
class BearCMS
{

    use \IvoPetkov\DataObjectTrait;
    use \BearFramework\EventsTrait;

    /**
     *
     * @var \BearFramework\App 
     */
    private $app;

    /**
     *
     * @var \BearFramework\App\Context 
     */
    private $context;

    /**
     * Constructs a new Bear CMS instance.
     */
    function __construct()
    {
        $this
            ->defineProperty('currentUser', [
                'init' => function () {
                    return new \BearCMS\CurrentUser();
                },
                'readonly' => true
            ])
            ->defineProperty('themes', [
                'init' => function () {
                    return new \BearCMS\Themes();
                },
                'readonly' => true
            ])
            ->defineProperty('addons', [
                'init' => function () {
                    return new \BearCMS\Addons();
                },
                'readonly' => true
            ])
            ->defineProperty('data', [
                'init' => function () {
                    return new \BearCMS\Data();
                },
                'readonly' => true
            ]);

        $this->app = App::get();
        $this->context = $this->app->contexts->get(__DIR__);
    }

    /**
     * Initializes the Bear CMS instance.
     * 
     * @param array $config A list of configuration variables.
     * @return void
     */
    public function initialize(array $config): void
    {
        Config::set($config);

        $hasServer = Config::hasServer();

        // Automatically log in the user
        if ($hasServer && (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_DEFAULT'))) {
            $cookies = Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER);
            if (isset($cookies['_a']) && !$this->currentUser->exists()) {
                $data = Internal\Server::call('autologin', [], true);
                if (isset($data['error'])) {
                    $this->currentUser->logout(); // kill the autologin cookie
                }
            }
        }

        $hasElements = Config::hasFeature('ELEMENTS') || Config::hasFeature('ELEMENTS_*');
        $hasPages = Config::hasFeature('PAGES');
        $hasBlog = Config::hasFeature('BLOG');
        $hasThemes = Config::hasFeature('THEMES');

        // Enable elements
        if ($hasElements) {
            $this->app->components
                ->addAlias('bearcms-elements', 'file:' . $this->context->dir . '/components/bearcmsElements.php')
                ->addTag('bearcms-elements', 'file:' . $this->context->dir . '/components/bearcmsElements.php')
                ->addAlias('bearcms-missing-element', 'file:' . $this->context->dir . '/components/bearcmsElement.php')
                ->addAlias('bearcms-unknown-element', 'file:' . $this->context->dir . '/components/bearcmsUnknownElement.php')
                ->addEventListener('makeComponent', function ($details) {
                    ElementsHelper::updateComponent($details->component);
                });

            ElementsTypes::addDefault();

            $this->app->serverRequests
                ->add('bearcms-elements-load-more', function ($data) {
                    return Elements::handleLoadMoreServerRequest($data);
                });

            $this->app->clientPackages
                ->add('-bearcms-elements-lazy-load', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    // $package->addJSCode(file_get_contents(__DIR__ . '/../dev/elementsLazyLoad.js')); // dev mode
                    $package->addJSFile($this->context->assets->getURL('assets/elementsLazyLoad.min.js', ['cacheMaxAge' => 999999999, 'version' => 6]));
                    $package->get = 'bearCMS.elementsLazyLoad.initialize(' . json_encode([__('bearcms.elements.LoadingMore'), JSON_THROW_ON_ERROR]) . ');';
                });

            $this->app->serverRequests
                ->add('-bearcms-lightbox-content', function ($data) {
                    $id = isset($data['id']) ? trim($data['id']) : '';
                    if (strlen($id) === 0 || preg_match('/^[0-9a-z\-]*$/', $id) !== 1) {
                        return 'error';
                    }
                    $style = '';
                    $style .= '.bearcms-lightbox-content{min-width:300px}';
                    $content = '<html><head><style>' . $style . '</style></head><body><div class="bearcms-lightbox-content"><bearcms-elements id="bearcms-lightbox-' . $id . '" editable="true"/></div></body></html>';
                    $content = $this->app->components->process($content);
                    $content = $this->app->clientPackages->process($content);
                    $editorContent = Internal\ElementsHelper::getEditableElementsHTML();
                    if ($editorContent !== '') {
                        $domDocument = new HTML5DOMDocument();
                        $domDocument->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                        $domDocument->insertHTML($editorContent);
                        $content = $domDocument->saveHTML();
                    }
                    return $content;
                });

            $this->app->clientPackages
                ->add('-bearcms-element-events', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    //$package->addJSCode(file_get_contents(__DIR__ . '/../dev/elementEvents.js')); // dev mode
                    $package->addJSCode(include $this->context->dir . '/resources/elementEvents.js.min.php');
                    $package->get = 'bearCMS.elementEvents.initialize();';
                });
        }

        // Load the CMS managed addons
        if (Config::hasFeature('ADDONS')) {
            Internal\Data\Addons::addToApp();
        }

        $disabledCheck = [$this, 'disabledCheck'];

        // Register the system pages
        if ($hasServer) {
            if (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_DEFAULT')) {
                $this->app->routes
                    // ->add(Config::$adminPagesPathPrefix . 'loggedin/', function () {
                    //     return new App\Response\TemporaryRedirect($this->app->request->base . '/');
                    // })
                    ->add([Config::$adminPagesPathPrefix, Config::$adminPagesPathPrefix . '*/'], function () {
                        return Internal\Controller::handleAdminPage();
                    })
                    ->add([rtrim(Config::$adminPagesPathPrefix, '/'), Config::$adminPagesPathPrefix . '*'], function (App\Request $request) {
                        $request->path->set($request->path->get() . '/');
                        return new App\Response\PermanentRedirect($request->getURL());
                    });
            }
            if (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) {
                $this->app->routes
                    ->add('POST /-aj/', function () {
                        return Internal\Controller::handleAjax();
                    })
                    ->add('POST /-au/', function () {
                        return Internal\Controller::handleFileUpload();
                    });
            }
        }

        // Register the file handlers
        if (Config::hasFeature('FILES')) {
            $this->app->routes
                ->add('/files/preview/*', [
                    $disabledCheck,
                    function (App\Request $request) {
                        return Internal\Controller::handleFilePreview($request);
                    }
                ])
                ->add('/files/download/?', [
                    $disabledCheck,
                    function (App\Request $request) {
                        return Internal\Controller::handleFileDownload($request);
                    }
                ]);
        }

        // Register some other pages
        $this->app->routes
            ->add(['/rss.xml', '/rss.*.xml'], [
                $disabledCheck,
                function (App\Request $request) {
                    $settings = $this->data->settings->get();
                    if ($settings->enableRSS) {
                        $segmentParts = explode('.', $request->path->getSegment(0));
                        $language = sizeof($segmentParts) === 3 ? $segmentParts[1] : '';
                        return Internal\Controller::handleRSS($language);
                    }
                }
            ])
            ->add('/sitemap.xml', [
                $disabledCheck,
                function () {
                    return Internal\Controller::handleSitemap();
                }
            ])
            ->add('/robots.txt', [
                $disabledCheck,
                function () {
                    return Internal\Controller::handleRobots();
                }
            ])
            ->add('/-link-rel-icon-*', [
                $disabledCheck,
                function () {
                    $size = (int) str_replace('/-link-rel-icon-', '', (string) $this->app->request->path);
                    if ($size >= 16 && $size <= 512) {
                        return Internal\Controller::handleIcon($size);
                    }
                }
            ])
            ->add(['/-meta-og-image', '*/-meta-og-image'], [
                $disabledCheck,
                function (App\Request $request) {
                    $path = substr((string) $request->path, 0, -strlen('-meta-og-image'));
                    return Internal\Controller::handleMetaOGImage($path);
                }
            ]);

        if (Config::hasFeature('COMMENTS')) {
            $this->app->serverRequests
                ->add('bearcms-comments-load-more', function ($data) {
                    return Comments::handleLoadMoreServerRequest($data);
                });
            $this->app->clientPackages
                ->add('-bearcms-comments-element-form', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    $package->addJSCode(include $this->context->dir . '/components/bearcmsCommentsElement/commentsElementForm.min.js.php');
                    //$package->addJSCode(file_get_contents(__DIR__ . '/../dev/commentsElementForm.js'));
                    $package->embedPackage('lightbox');
                })
                ->add('-bearcms-comments-element-list', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    $package->addJSCode(include $this->context->dir . '/components/bearcmsCommentsElement/commentsElementList.min.js.php');
                    //$package->addJSCode(file_get_contents(__DIR__ . '/../dev/commentsElementList.js'));
                    $package->embedPackage('lightbox'); // for the preview
                });
            CommentsLocations::addSource(function () use ($hasPages, $hasBlog) {
                if ($hasPages) {
                    Pages::setCommentsLocations();
                }
                if ($hasBlog) {
                    Blog::setCommentsLocations();
                }
            });
            $checkCommentsLocationsElementsContainerID = function (string $containerID) use ($hasPages) {
                if ($hasPages && strpos($containerID, 'bearcms-page-') === 0) {
                    $pageID = str_replace('bearcms-page-', '', $containerID);
                    $this->app->addEventListener('sendResponse', function () use ($pageID) {
                        Pages::addUpdateCommentsLocationsTask($pageID);
                    });
                }
            };
            $this
                ->addEventListener('internalElementChange', function (\BearCMS\Internal\ElementChangeEventDetails $details) use ($checkCommentsLocationsElementsContainerID) {
                    if ($details->containerID !== null) {
                        $checkCommentsLocationsElementsContainerID($details->containerID);
                    }
                })
                ->addEventListener('internalElementsContainerChange', function (\BearCMS\Internal\ElementsContainerChangeEventDetails $details) use ($checkCommentsLocationsElementsContainerID) {
                    $checkCommentsLocationsElementsContainerID($details->containerID);
                });
        }

        if ($hasBlog) {
            $this->app->routes
                ->add([Config::$blogPagesPathPrefix . '?', Config::$blogPagesPathPrefix . '?/'], [
                    $disabledCheck,
                    function (App\Request $request) {
                        return Blog::handleBlogPostPageRequest($this, $request);
                    }
                ]);
            $this->app->serverRequests
                ->add('bearcms-blogposts-load-more', function ($data) {
                    return Blog::handleLoadMoreServerRequest($data);
                });

            if ($hasThemes) {
                Internal\Themes::$pagesOptions['blog'] = function (\BearCMS\Internal\ThemeOptionsGroupInterface $options, array $details = []) {
                    Blog::addThemesPageOptions($options, $details);
                };
            }
            $this->app->clientPackages
                ->add('-bearcms-blog-posts-element', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    $package->addJSCode(include $this->context->dir . '/components/bearcmsBlogPostsElement/blogPostsElement.min.js.php');
                    //$package->addJSCode(file_get_contents(__DIR__ . '/../dev/blogPostsElement.js'));
                });
        }

        // Register a home page and the dynamic pages handler
        if ($hasPages) {
            $this->app->routes
                ->add('*', [
                    $disabledCheck,
                    function (App\Request $request) {
                        return Pages::handlePageRequest($this, $request);
                    }
                ]);
        }

        // Register a redirects handler
        $this->app->routes
            ->add('*', [
                $disabledCheck,
                function (App\Request $request) {
                    return Internal\Settings::handleRedirectRequest($this->app, $this, $request);
                }
            ]);

        // Sitemap for pages and blog posts
        if ($hasPages || $hasBlog) {
            Sitemap::addSource(function (\BearCMS\Internal\Sitemap\Sitemap $sitemap) use ($hasPages, $hasBlog) {
                if ($hasPages) {
                    Pages::addSitemapItems($sitemap);
                }
                if ($hasBlog) {
                    Blog::addSitemapItems($sitemap);
                }
            });
            $checkSitemapElementsContainerID = function (string $containerID) use ($hasPages, $hasBlog) {
                if ($hasPages && strpos($containerID, 'bearcms-page-') === 0) {
                    $pageID = str_replace('bearcms-page-', '', $containerID);
                    $this->app->addEventListener('sendResponse', function () use ($pageID) {
                        $page = $this->data->pages->get($pageID);
                        if ($page !== null) {
                            Sitemap::addUpdateDateTask($page->path);
                        }
                    });
                }
                if ($hasBlog && strpos($containerID, 'bearcms-blogpost-') === 0) {
                    $blogPostID = str_replace('bearcms-blogpost-', '', $containerID);
                    $this->app->addEventListener('sendResponse', function () use ($blogPostID) {
                        $blogPost = $this->data->blogPosts->get($blogPostID);
                        if ($blogPost !== null) {
                            Sitemap::addUpdateDateTask($blogPost->getURLPath());
                        }
                    });
                }
            };
            $this
                ->addEventListener('internalElementChange', function (\BearCMS\Internal\ElementChangeEventDetails $details) use ($checkSitemapElementsContainerID) {
                    if ($details->containerID !== null) {
                        $checkSitemapElementsContainerID($details->containerID);
                    }
                })
                ->addEventListener('internalElementsContainerChange', function (\BearCMS\Internal\ElementsContainerChangeEventDetails $details) use ($checkSitemapElementsContainerID) {
                    $checkSitemapElementsContainerID($details->containerID);
                });
        }

        $this->app->assets
            ->addEventListener('beforePrepare', function (\BearFramework\App\Assets\BeforePrepareEventDetails $details) {
                $filename = $details->filename;
                // Theme media file
                $matchingDir = $this->context->dir . '/assets/tm/';
                if (strpos($filename, $matchingDir) === 0) {
                    $details->filename = '';
                    $pathParts = explode('/', substr($filename, strlen($matchingDir)), 2);
                    if (isset($pathParts[0], $pathParts[1])) {
                        $themeIDMD5 = $pathParts[0];
                        $mediaFilenameMD5 = $pathParts[1];
                        $themes = Internal\Themes::getIDs();
                        foreach ($themes as $id) {
                            if ($themeIDMD5 === md5($id)) {
                                $themeManifest = Internal\Themes::getManifest($id, false);
                                if (isset($themeManifest['media'])) {
                                    foreach ($themeManifest['media'] as $mediaItem) {
                                        if (isset($mediaItem['filename'])) {
                                            if ($mediaFilenameMD5 === md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION)) {
                                                $details->filename = $mediaItem['filename'];
                                                return;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            })
            ->addEventListener('beforePrepare', function (\BearFramework\App\Assets\BeforePrepareEventDetails $details) {
                $filename = $details->filename;
                $addonAssetsDir = $this->context->dir . '/assets/';
                if (strpos($filename, $addonAssetsDir) === 0) {

                    // Proxy (used in video element)
                    $matchingDir = $addonAssetsDir . 'p/';
                    if (strpos($filename, $matchingDir) === 0) {
                        $details->filename = '';
                        $pathParts = explode('/', substr($filename, strlen($matchingDir)), 3);
                        if (isset($pathParts[0], $pathParts[1], $pathParts[2])) {
                            $url = $pathParts[0] . '://' . $pathParts[1] . '/' . str_replace('\\', '/', $pathParts[2]);
                            $details->filename = Internal\Downloads::download($url, true);
                        }
                        return;
                    }

                    // Download a server file
                    $matchingDir = $addonAssetsDir . 's/';
                    if (strpos($filename, $matchingDir) === 0) {
                        $details->filename = '';
                        $path = str_replace('\\', '/', substr($filename, strlen($matchingDir)));
                        $details->filename = Internal\Server::download($path, true);
                    }
                }
            });

        $this->app
            ->addEventListener('beforeSendResponse', function (\BearFramework\App\BeforeSendResponseEventDetails $details) {
                if (strpos((string) $this->app->request->path, $this->app->assets->pathPrefix) !== 0) {
                    $response = $details->response;
                    if ($response instanceof App\Response\NotFound) {
                        $response->headers->set($response->headers->make('Content-Type', 'text/html'));
                        $this->apply($response);
                    } elseif ($response instanceof App\Response\TemporaryUnavailable) {
                        $response->headers->set($response->headers->make('Content-Type', 'text/html'));
                        $this->apply($response);
                    }
                }
            });

        if ($hasThemes && Config::$addDefaultThemes) {
            $this->themes->addDefault();
        }

        if ($hasServer && (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*'))) {
            if (Config::$useDefaultUserProfile) {
                $this->app->users
                    ->addProvider('bearcms', Internal\UserProvider::class);

                if ($this->currentUser->exists()) {
                    if (!$this->app->currentUser->exists()) {
                        $this->app->currentUser->login('bearcms', $this->currentUser->getID());
                    }
                } else {
                    if ($this->app->currentUser->exists()) {
                        if ($this->app->currentUser->provider === 'bearcms') {
                            $this->app->currentUser->logout();
                        }
                    }
                }
            }
            $this->app
                ->addEventListener('beforeSendResponse', function (\BearFramework\App\BeforeSendResponseEventDetails $details) {
                    Internal\Cookies::apply($details->response);
                });
        }


        if (Config::hasFeature('NOTIFICATIONS')) {
            $this->app->tasks
                ->define('bearcms-send-new-comment-notification', function ($data) {
                    Comments::sendNewCommentNotification($data);
                });
        }

        $this->app->tasks
            ->define('bearcms-sitemap-update-dates', function ($paths) {
                foreach ($paths as $path) {
                    Internal\Sitemap::addUpdateDateTask($path);
                }
            })
            ->define('bearcms-sitemap-update-date', function ($path) {
                Internal\Sitemap::updateDate($path);
            })
            ->define('bearcms-sitemap-notify-search-engines', function () {
                Internal\Sitemap::notifySearchEngines();
            })
            ->define('bearcms-page-comments-locations-update', function ($pageID) {
                Pages::setCommentsLocations($pageID);
            })
            ->define('bearcms-blog-comments-locations-update', function ($blogPostID) {
                Blog::setCommentsLocations($blogPostID);
            });

        // Initialize to add asset dirs
        $currentThemeID = Internal\CurrentTheme::getID();
        Internal\Themes::initialize($currentThemeID);

        Config::$initialized = true;
    }

    /**
     * Applies all Bear CMS modifications (the default HTML, theme and admin UI) to the response.
     * 
     * @param \BearFramework\App\Response $response The response to modify.
     * @param \BearCMS\ApplyContext|null $applyContext
     * @return void
     */
    public function apply(\BearFramework\App\Response $response, \BearCMS\ApplyContext $applyContext = null): void
    {
        $language = null;
        if ($applyContext !== null) {
            $language = $applyContext->language;
        }
        if ($language !== null) {
            $localization = $this->app->localization;
            $previousLocale = $localization->getLocale();
            $localization->setLocale($language);
        }
        $this->applyTheme($response, $applyContext);
        $this->process($response, $applyContext);
        $this->applyDefaults($response, $applyContext);
        $this->applyAdminUI($response, $applyContext);
        if ($language !== null) {
            $localization->setLocale($previousLocale);
        }
    }

    /**
     * Converts custom tags (if any) into valid HTML code.
     * 
     * @param \BearFramework\App\Response $response
     * @return void
     */
    public function process(\BearFramework\App\Response $response): void
    {
        $response->content = $this->app->components->process($response->content);
    }

    /**
     * Add the default Bear CMS HTML to the response.
     * 
     * @param \BearFramework\App\Response $response The response to modify.
     * @param \BearCMS\ApplyContext|null $applyContext
     * @return void
     */
    public function applyDefaults(\BearFramework\App\Response $response, \BearCMS\ApplyContext $applyContext = null): void
    {
        $currentUserExists = Config::hasServer() && (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        $settings = $this->data->settings->get();

        if ($currentUserExists) {
            $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));
        }

        $document = new HTML5DOMDocument();
        $document->loadHTML($response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

        $language = null;
        if ($applyContext !== null) {
            $language = $applyContext->language;
        }
        $language = (string)$language;
        if (strlen($language) === 0 && isset($settings->languages[0])) {
            $language = $settings->languages[0];
        }

        if (strlen($language) > 0) {
            $html = '<html lang="' . htmlentities($language) . '">';
        } else {
            $html = '<html>';
        }
        $html .= '<head>';

        $title = '';
        $titleElement = $document->querySelector('title');
        if ($titleElement !== null && strlen($titleElement->innerHTML) > 0) {
            $title = html_entity_decode($titleElement->innerHTML);
        } else {
            $h1Element = $document->querySelector('h1');
            if ($h1Element !== null) {
                $innerHTML = $h1Element->innerHTML;
                if (isset($innerHTML[0])) {
                    $title = $innerHTML;
                    $html .= '<title>' . $innerHTML . '</title>';
                }
            }
        }

        $metaElements = $document->querySelectorAll('meta');
        $generateDescriptionMetaTag = true;
        $generateKeywordsMetaTag = true;
        foreach ($metaElements as $metaElement) {
            $metaElementName = $metaElement->getAttribute('name');
            if ($metaElementName === 'description' && strlen($metaElement->getAttribute('content')) > 0) {
                $generateDescriptionMetaTag = false;
            } elseif ($metaElementName === 'keywords' && strlen($metaElement->getAttribute('content')) > 0) {
                $generateKeywordsMetaTag = false;
            }
        }
        if ($generateDescriptionMetaTag || $generateKeywordsMetaTag) {
            $elements = $document->querySelectorAll('h1.bearcms-heading-element,div.bearcms-text-element');
            if ($elements->length > 0) {
                if ($generateDescriptionMetaTag) {
                    $descriptionContent = '';
                }
                if ($generateKeywordsMetaTag) {
                    $keywordsContent = '';
                }
                foreach ($elements as $element) {
                    $class = $element->getAttribute('class');
                    $content = $element->innerHTML;
                    if ($generateDescriptionMetaTag) {
                        if (strpos($class, 'bearcms-text-element') !== false) {
                            $descriptionContent .= ' ' . $content;
                        }
                    }
                    if ($generateKeywordsMetaTag) {
                        $keywordsContent .= ' ' . $content;
                    }
                }
                if ($generateDescriptionMetaTag) {
                    $html .= '<meta name="description" content="' . htmlentities(TextUtilities::cropText(TextUtilities::htmlToText($descriptionContent), 200)) . '"/>';
                }
                if ($generateKeywordsMetaTag) {
                    $keywords = TextUtilities::getKeywords(TextUtilities::htmlToText($keywordsContent));
                    $html .= '<meta name="keywords" content="' . htmlentities(implode(', ', $keywords)) . '"/>';
                }
            }
        }

        if (!Config::$whitelabel) {
            $html .= '<meta name="generator" content="Bear CMS (powered by Bear Framework)"/>';
        }
        $icon = $settings->icon;
        if (isset($icon[0])) {
            $baseUrl = $this->app->urls->get();
            $html .= '<link rel="icon" href="' . htmlentities($baseUrl . '-link-rel-icon-32') . '">';
            $html .= '<link rel="apple-touch-icon" href="' . htmlentities($baseUrl . '-link-rel-icon-192') . '">';
        } else if ($currentUserExists) {
            $html .= '<link rel="icon" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="apple-touch-icon" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
        }
        if (empty($settings->allowSearchEngines)) {
            $html .= '<meta name="robots" content="noindex">';
        }
        $isFirstLanguage = true;
        foreach ($settings->languages as $otherLanguage) {
            if ($otherLanguage !== $language) {
                $otherLanguageURL = $this->app->urls->get($isFirstLanguage ? '/' : '/' . $otherLanguage . '/');
                $html .= '<link rel="alternate" hreflang="' . htmlentities($otherLanguage) . '" href="' . htmlentities($otherLanguageURL) . '">';
            }
            $isFirstLanguage = false;
        }
        $url = rtrim($this->app->request->getURL(), '/') . '/';
        $url = explode('?', $url)[0]; // remove the query string
        $html .= '<link rel="canonical" href="' . htmlentities($url) . '">';
        if ($settings->enableRSS) {
            $rssKeys = $settings->languages;
            if (empty($rssKeys)) {
                $rssKeys = [''];
            } else {
                $rssKeys[0] = '';
            }
            foreach ($rssKeys as $rssKey) {
                $rssTitle = (string)$settings->getTitle($rssKey);
                $rssURL = $this->app->urls->get('/rss' . ($rssKey === '' ? '' : '.' . $rssKey) . '.xml');
                $html .= '<link rel="alternate" type="application/rss+xml" title="' . htmlentities(trim($rssTitle)) . '" href="' . htmlentities($rssURL) . '">';
            }
        }
        $html .= '<meta property="og:image" content="' . htmlentities($url) . '-meta-og-image' . '?' . time() . '">';
        $html .= '<meta property="og:type" content="website">';
        $html .= '<meta property="og:url" content="' . htmlentities($url) . '">';
        if (!empty($settings->fonts)) {
            $fontFacesCSS = '';
            foreach ($settings->fonts as $fontData) {
                if (isset($fontData['name'], $fontData['filename'])) {
                    $fontFacesCSS .= '@font-face {font-family:\'' . str_replace(['"', "'"], '', trim($fontData['name'])) . '\';src:url(' . $this->app->assets->getURL($fontData['filename'], ['cacheMaxAge' => 999999999, 'version' => 1]) . ');}'; // format(\'' . pathinfo($fontData['filename'], PATHINFO_EXTENSION) . '\')
                }
            }
            if ($fontFacesCSS !== '') {
                $html .= '<style>' . $fontFacesCSS . '</style>';
            }
        }
        $html .= '</head><body>';

        if ($response instanceof App\Response\HTML) { // is not temporary disabled
            if ($settings->externalLinks) {
                // taken from dev/externalLinksNoUser.min.js
                $html .= '<script>(function(){var f=location.host,e=function(){for(var d=document.getElementsByTagName("a"),b=0;b<d.length;b++){var c=d[b],a=c.getAttribute("href");null!==a&&-1!==a.indexOf("//")&&-1===a.indexOf("//"+f)&&0!==a.indexOf("#")&&0!==a.indexOf("javascript:")&&null===c.getAttribute("target")&&c.setAttribute("target","_blank")}};e();window.setInterval(e,999)})();</script>';
            }
        }
        $html .= '</body></html>';
        $htmlToInsert[] = ['source' => $html];

        if ($response instanceof App\Response\HTML) {
            $allowRenderGlobalHTML = Config::getVariable('internalAllowRenderGlobalHTML');
            $allowRenderGlobalHTML = $allowRenderGlobalHTML !== null ? (int)$allowRenderGlobalHTML : true;
            if ($allowRenderGlobalHTML) {
                $globalHTML = $settings->globalHTML;
                if (isset($globalHTML[0]) && (!$currentUserExists || ($currentUserExists && !$this->app->request->query->exists('disable-global-html')))) {
                    $htmlToInsert[] = ['source' => $globalHTML];
                }
            }
        }
        $document->insertHTMLMulti($htmlToInsert);

        if (strlen($title) > 0) {
            $imageElements = $document->querySelectorAll('img');
            foreach ($imageElements as $imageElement) {
                if (strlen((string)$imageElement->getAttribute('alt')) === 0) {
                    $imageElement->setAttribute('alt', $title);
                }
            }
        }

        // Set target="_blank" to files preview links
        if (strpos($response->content, '/files/preview/') !== false) {
            $linkElements = $document->querySelectorAll('a');
            foreach ($linkElements as $linkElement) {
                if (strpos($linkElement->getAttribute('href'), '/files/preview/') !== false) {
                    $linkTarget = $linkElement->getAttribute('target');
                    if (strlen((string)$linkTarget) === 0) {
                        $linkElement->setAttribute('target', '_blank');
                    }
                }
            }
        }

        $response->content = $document->saveHTML();

        if ($this->app->currentUser->exists() && !(int)$this->currentUser->exists()) {
            $this->app->users->applyUI($response);
        }
    }

    /**
     * Add the Bear CMS admin UI to the response, if an administrator is logged in.
     * 
     * @param \BearFramework\App\Response $response The response to modify.
     * @param \BearCMS\ApplyContext|null $applyContext
     * @return void
     */
    public function applyAdminUI(\BearFramework\App\Response $response, \BearCMS\ApplyContext $applyContext = null): void
    {
        $currentUserExists = Config::hasServer() && (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        if (!$currentUserExists) {
            return;
        }
        if (Config::getVariable('disableAdminUI')) {
            return;
        }

        $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));

        $cacheKey = json_encode([
            'adminUI',
            $this->app->request->base,
            $this->currentUser->getSessionKey(),
            $this->currentUser->getPermissions(),
            get_class_vars('\BearCMS\Internal\Config')
        ], JSON_THROW_ON_ERROR);

        $adminUIData = Internal\Server::call('adminui', [], true, $cacheKey);
        if (is_array($adminUIData) && isset($adminUIData['result'])) {
            if ($adminUIData['result'] === 'noUser') { // The user does not exists on the server
                $this->currentUser->logout();
                return;
            }
            if (is_array($adminUIData['result']) && isset($adminUIData['result']['content']) && strlen($adminUIData['result']['content']) > 0) {
                $content = $adminUIData['result']['content'];
                $content = Internal\Server::updateAssetsUrls($content, false);
                $document = new HTML5DOMDocument();
                $htmlToInsert = [];
                if (strpos($content, '{body}')) {
                    $content = str_replace('{body}', (string) $document->createInsertTarget('body'), $content);
                    $htmlToInsert[] = ['source' => $response->content, 'target' => 'body'];
                } elseif (strpos($content, '{jsonEncodedBody}')) {
                    $content = str_replace('{jsonEncodedBody}', json_encode($this->app->clientPackages->process($this->app->components->process($response->content)), JSON_THROW_ON_ERROR), $content);
                }
                $document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $elementsHTML = Internal\ElementsHelper::getEditableElementsHTML();
                if (isset($elementsHTML[0])) {
                    $htmlToInsert[] = ['source' => $elementsHTML];
                }
                if (!empty(Internal\ElementsHelper::$editorData)) {
                    $context = $this->app->contexts->get(__DIR__);
                    $html = '';
                    //$html .= '<script>' . file_get_contents(__DIR__ . '/../dev/elementsEditor.js') . '</script>'; // dev mode
                    $html .= '<script src="' . $context->assets->getURL('assets/elementsEditor.min.js', ['cacheMaxAge' => 999999999, 'version' => 8]) . '" />';
                    $html .= '<link rel="client-packages-embed" name="cssToAttributes">'; // may be needed when customizing elements
                    $html .= '<link rel="client-packages-embed" name="responsiveAttributes">'; // may be needed when customizing elements
                    $html .= '<link rel="client-packages-embed" name="-bearcms-element-events">'; // may be needed when customizing elements
                    $htmlToInsert[] = ['source' => '<html><head>' . $html . '</head></html>'];
                }
                $htmlToInsert[] = ['source' => '<html><head><link rel="client-packages"></head></html>']; // used by ServerCommands to update content
                $document->insertHTMLMulti($htmlToInsert);
                $response->content = $document->saveHTML();
            }
        }
    }

    /**
     * Applies the currently selected Bear CMS theme to the response provided.
     * 
     * @param \BearFramework\App\Response $response The response to modify.
     * @param \BearCMS\ApplyContext|null $applyContext
     * @return void
     */
    public function applyTheme(\BearFramework\App\Response $response, \BearCMS\ApplyContext $applyContext = null): void
    {
        $currentUserExists = $this->currentUser->exists();
        $currentThemeID = Internal\CurrentTheme::getID();
        $currentCustomizations = Internal\Themes::getCustomizations($currentThemeID, $currentUserExists ? $this->currentUser->getID() : null);

        $settings = $this->data->settings->get();
        $languages = $settings->languages;
        $language = null;
        if ($applyContext !== null) {
            $language = $applyContext->language;
        }
        $language = (string)$language;
        if (strlen($language) === 0) {
            if (isset($languages[0])) {
                $language = $languages[0];
            }
        }

        if ($currentUserExists) {
            $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));
        }

        if ($response instanceof App\Response\HTML) {
            if (strpos($response->content, 'class="bearcms-blogpost-page-date-container"') !== false && ($currentCustomizations !== null && $currentCustomizations->getValue('blogPostPageDateVisibility') === '0')) {
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML($response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $element = $domDocument->querySelector('div.bearcms-blogpost-page-date-container');
                if ($element) {
                    $element->parentNode->removeChild($element);
                    $response->content = $domDocument->saveHTML();
                }
            }
        }

        if (isset(Internal\Themes::$registrations[$currentThemeID])) {
            $theme = Internal\Themes::get($currentThemeID);
            $callContext = [
                'language' => $language,
                'languages' => $languages
            ];
            if ($theme->get !== null) {
                if ($response instanceof App\Response\HTML) {
                    $templateContent = call_user_func($theme->get, $currentCustomizations, $callContext);
                    if ($currentCustomizations !== null) {
                        $templateContent = $currentCustomizations->apply($templateContent);
                    }
                    $template = new \BearFramework\HTMLTemplate($templateContent);
                    $template->insert($response->content, 'body');
                    $response->content = $template->get();
                }
            }
            if ($theme->apply !== null) {
                call_user_func($theme->apply, $response, $currentCustomizations, $callContext);
            }
        }

        if (!Config::$whitelabel && $response instanceof App\Response\HTML) {
            $domDocument = new HTML5DOMDocument();
            $domDocument->loadHTML($response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            $logoSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="75.93" height="45.65" viewBox="0 0 75.929546 45.649438"><path fill="#666" d="M62.2 0c1.04-.02 2.13.8 2.55 2.14.15.56.1 1.3.43 1.6 2.02 1.88 5.34 1.64 6.04 4.9.12.75 2 2.3 2.92 3.2.8.77 2 2.13 1.76 2.86-.5 1.66-1.16 3.65-3.65 3.6-3.64-.06-7.3-.04-10.94 0-4.66.04-7.44 2.82-7.5 7.53-.05 3.8.07 7.63-.03 11.46-.08 3 1.25 4.67 4.18 5.35.93.24 1.5 1.1.84 1.9-.8 1-4.3 1-4.4 1-2.8.33-6.5-.7-8.78-6.4-1.3 1.7-2.2 2.56-3.4 2.94-.7.22-4.17 1.1-4.3.3-.25-1.44 3.9-5.03 4.07-6.5.3-2.84-2.18-3.9-5.05-4.6-2.9-.74-6 .57-7.3 1.95-1.8 1.9-1.7 7.77-.76 8.26.5.26 1.46.8 1.5 1.6 0 .6-.76 1.5-1.2 1.5-2.5.17-5.03.26-7.48-.05-.65-.08-1.6-1.66-1.6-2.54.04-2.87-5.5-7.9-6.4-6.6-1.52 2.16-6.04 3.23-5.5 6.04.34 1.8 3.9.6 4.25 2 .76 3.2-6.8 2.1-9.87 1.7-2.58-.33-3.63-1.83-1.32-6.9 2.8-5.1 3.23-10.4 2.75-16.17C3.08 9.6 11.53.97 24.08 1.3c10.9.24 21.9-.2 32.7 1.3 6.1.82 2.72.1 3.77-1.6.42-.67 1.03-1 1.65-1z"/></svg>';
            $codeToInsert = '<div style="background-color:#000;padding:15px;text-align:center;"><a href="https://bearcms.com/" target="_blank" rel="nofollow noopener" title="This website is powered by Bear CMS" style="width:40px;height:40px;display:inline-block;background-size:80%;background-repeat:no-repeat;background-position:center center;background-image:url(data:image/svg+xml;base64,' . base64_encode($logoSvg) . ');"></a></div>';
            //$html = '<body><script>document.body.insertAdjacentHTML("beforeend",' . json_encode($codeToInsert) . ');</script></body>';
            $domDocument->insertHTML($codeToInsert);
            $response->content = $domDocument->saveHTML();
        }
    }

    /**
     * A middleware to be used in routes that returns a temporary unavailable response if an administrator has disabled the app.
     * 
     * @return \BearFramework\App\Response|null
     */
    public function disabledCheck(): ?\BearFramework\App\Response
    {
        $currentUserExists = Config::hasServer() && (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) ? $this->currentUser->exists() : false;
        $settings = $this->data->settings->get();
        $isDisabled = !$currentUserExists && $settings->disabled;
        if ($isDisabled) {
            return new App\Response\TemporaryUnavailable(htmlspecialchars($settings->disabledText));
        }
        return null;
    }

    /**
     * 
     * @return \BearCMS\ApplyContext
     */
    public function makeApplyContext(): \BearCMS\ApplyContext
    {
        return new ApplyContext();
    }
}
