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
use BearCMS\Internal2;
use BearCMS\Internal\Data\UploadsSize;
use BearCMS\Internal\ElementsCombinations;

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

        $hasElements = Config::hasFeature('ELEMENTS');
        $hasThemes = Config::hasFeature('THEMES');

        // Enable elements
        if ($hasElements || Config::hasFeature('ELEMENTS_*')) {
            $this->app->components
                ->addAlias('bearcms-elements', 'file:' . $this->context->dir . '/components/bearcmsElements.php')
                ->addTag('bearcms-elements', 'file:' . $this->context->dir . '/components/bearcmsElements.php')
                ->addAlias('bearcms-missing-element', 'file:' . $this->context->dir . '/components/bearcmsElement.php')
                ->addEventListener('makeComponent', function ($details) {
                    // Updates the BearCMS components when created
                    $component = $details->component;
                    $name = strlen($component->src) > 0 ? $component->src : ($component->tagName !== 'component' ? $component->tagName : null);
                    if ($name !== null) {
                        if ($name === 'bearcms-elements') {
                            Internal\ElementsHelper::updateContainerComponent($component);
                        } elseif (isset(Internal\ElementsHelper::$elementsTypesFilenames[$name])) {
                            $component->setAttribute('bearcms-internal-attribute-type', Internal\ElementsHelper::$elementsTypesCodes[$name]);
                            $component->setAttribute('bearcms-internal-attribute-filename', Internal\ElementsHelper::$elementsTypesFilenames[$name]);
                            Internal\ElementsHelper::updateElementComponent($component);
                        } else if ($name === 'bearcms-missing-element') {
                            $component->setAttribute('bearcms-internal-attribute-type', 'missing');
                            Internal\ElementsHelper::updateElementComponent($component);
                        }
                    }
                });

            $this->app->serverRequests
                ->add('bearcms-elements-load-more', function ($data) {
                    if (isset($data['serverData'])) {
                        $serverData = Internal\TempClientData::get($data['serverData']);
                        if (is_array($serverData) && isset($serverData['componentHTML'])) {
                            $content = $this->app->components->process($serverData['componentHTML']);
                            $content = $this->app->clientPackages->process($content);
                            $editorContent = Internal\ElementsHelper::getEditableElementsHtml();
                            return json_encode([
                                'content' => $content,
                                'editorContent' => (isset($editorContent[0]) ? $editorContent : ''),
                                'nextLazyLoadData' => (string) Internal\ElementsHelper::$lastLoadMoreServerData
                            ]);
                        }
                    }
                });

            if ($hasElements || Config::hasFeature('ELEMENTS_HEADING')) {
                Internal\ElementsTypes::add('heading', [
                    'componentSrc' => 'bearcms-heading-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsHeadingElement.php',
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
                        ],
                        [
                            'id' => 'linkTargetID',
                            'type' => 'textbox'
                        ]
                    ],
                    'canStyle' => true
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['heading'] = function ($options, $idPrefix, $parentSelector, $context) {

                        if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                            $options->addOption($idPrefix . "HeadingCSS", "css", '', [
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-large", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["rule", $parentSelector . " .bearcms-heading-element-medium", "box-sizing:border-box;box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["rule", $parentSelector . " .bearcms-heading-element-small", "box-sizing:border-box;box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-large"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-medium"],
                                    ["selector", $parentSelector . " .bearcms-heading-element-small"]
                                ],
                                "elementType" => "heading"
                            ]);
                        } else {
                            $group = $options->addGroup(__("bearcms.themes.options.Heading"));
                            $customStyleSelector = ' .bearcms-elements-element-container:not([class*="bearcms-elements-element-style-"]) >';

                            $groupLarge = $group->addGroup(__("bearcms.themes.options.Large"));
                            $groupLarge->addOption($idPrefix . "HeadingLargeCSS", "css", '', [
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-large", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["selector", $parentSelector . $customStyleSelector . " .bearcms-heading-element-large"]
                                ],
                                "elementType" => "heading"
                            ]);

                            $groupMedium = $group->addGroup(__("bearcms.themes.options.Medium"));
                            $groupMedium->addOption($idPrefix . "HeadingMediumCSS", "css", '', [
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-medium", "box-sizing:border-box;box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["selector", $parentSelector . $customStyleSelector . " .bearcms-heading-element-medium"]
                                ],
                                "elementType" => "heading"
                            ]);

                            $groupSmall = $group->addGroup(__("bearcms.themes.options.Small"));
                            $groupSmall->addOption($idPrefix . "HeadingSmallCSS", "css", '', [
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-small", "box-sizing:border-box;box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                    ["selector", $parentSelector . $customStyleSelector . " .bearcms-heading-element-small"]
                                ],
                                "elementType" => "heading"
                            ]);
                        }
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_TEXT')) {
                Internal\ElementsTypes::add('text', [
                    'componentSrc' => 'bearcms-text-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsTextElement.php',
                    'fields' => [
                        [
                            'id' => 'text',
                            'type' => 'textbox'
                        ]
                    ],
                    'canStyle' => true
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['text'] = function ($options, $idPrefix, $parentSelector, $context) {
                        if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                            $optionsGroup = $options;
                            $customStyleSelector = '';
                        } else {
                            $optionsGroup = $options->addGroup(__("bearcms.themes.options.Text"));
                            $customStyleSelector = ' .bearcms-elements-element-container:not([class*="bearcms-elements-element-style-"]) >';
                        }
                        $optionsGroup->addOption($idPrefix . "TextCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-text-element", "box-sizing:border-box;"],
                                ["rule", $parentSelector . " .bearcms-text-element ul", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-text-element ol", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-text-element li", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-text-element p", "margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-text-element input", "margin:0;padding:0;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-text-element"]
                            ],
                            "elementType" => "text"
                        ]);

                        $groupLinks = $optionsGroup->addGroup(__("bearcms.themes.options.Links"));
                        $groupLinks->addOption($idPrefix . "TextLinkCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-text-element a", "text-decoration:none;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-text-element a"]
                            ],
                            "elementType" => "text"
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_LINK')) {
                Internal\ElementsTypes::add('link', [
                    'componentSrc' => 'bearcms-link-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsLinkElement.php',
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
                    ],
                    'canStyle' => true
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['link'] = function ($options, $idPrefix, $parentSelector, $context) {
                        if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                            $optionsGroup = $options;
                            $customStyleSelector = '';
                        } else {
                            $optionsGroup = $options->addGroup(__("bearcms.themes.options.Link"));
                            $customStyleSelector = ' .bearcms-elements-element-container:not([class*="bearcms-elements-element-style-"]) >';
                        }

                        $optionsGroup->addOption($idPrefix . "LinkCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-link-element a", "text-decoration:none;box-sizing:border-box;display:inline-block;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-link-element a"]
                            ],
                            "elementType" => "link"
                        ]);

                        $groupContainer = $optionsGroup->addGroup(__("bearcms.themes.options.Container"));
                        $groupContainer->addOption($idPrefix . "LinkContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-link-element", "box-sizing:border-box;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-link-element"]
                            ],
                            "elementType" => "link"
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_IMAGE')) {
                Internal\ElementsTypes::add('image', [
                    'componentSrc' => 'bearcms-image-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsImageElement.php',
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
                    ],
                    'onDuplicate' => function ($data) {
                        $filename = isset($data['filename']) ? $data['filename'] : '';
                        if (strlen($filename) > 0) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            $newFilename = Internal\Data::generateNewFilename($filename);
                            copy($filename, $newFilename);
                            UploadsSize::add(Internal\Data::filenameToDataKey($newFilename), filesize($newFilename));
                            $data['filename'] = $newFilename;
                        }
                        return $data;
                    },
                    'getUploadsSize' => function ($data) {
                        $filename = isset($data['filename']) ? $data['filename'] : '';
                        if (strlen($filename) > 0) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            return (int) UploadsSize::getItemSize(Internal\Data::filenameToDataKey($filename));
                        }
                        return 0;
                    },
                    'canStyle' => true
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['image'] = function ($options, $idPrefix, $parentSelector, $context) {
                        $isElementContext = $context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT;
                        if ($isElementContext) {
                            $optionsGroup = $options;
                            $customStyleSelector = '';
                        } else {
                            $optionsGroup = $options->addGroup(__("bearcms.themes.options.Image"));
                            $customStyleSelector = ' .bearcms-elements-element-container:not([class*="bearcms-elements-element-style-"]) >';
                        }

                        $optionsGroup->addOption($idPrefix . "ImageCSS", "css", '', [
                            "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-image-element", "overflow:hidden;"],
                                ["rule", $parentSelector . " .bearcms-image-element img", "border:0;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-image-element"]
                            ],
                            "elementType" => "image"
                        ]);
                        if ($isElementContext) {
                            $optionsGroup->addOption($idPrefix . "elementContainerCSS", "css", '', [
                                "cssTypes" => ["cssSize"],
                                "cssOutput" => [
                                    ["selector", $parentSelector . $customStyleSelector]
                                ],
                                "elementType" => "image"
                            ]);
                        }
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_IMAGE_GALLERY')) {
                Internal\ElementsTypes::add('imageGallery', [
                    'componentSrc' => 'bearcms-image-gallery-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsImageGalleryElement.php',
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
                    'updateComponentFromData' => function ($component, $data) {
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
                    'updateDataFromComponent' => function ($component, $data) {
                        $domDocument = new HTML5DOMDocument();
                        $domDocument->loadHTML($component->innerHTML, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                        $files = [];
                        $filesElements = $domDocument->querySelectorAll('file');
                        foreach ($filesElements as $fileElement) {
                            $files[] = ['filename' => $fileElement->getAttribute('filename')];
                        }
                        $data['files'] = $files;
                        return $data;
                    },
                    'onDuplicate' => function ($data) {
                        if (isset($data['files']) && is_array($data['files'])) {
                            foreach ($data['files'] as $index => $file) {
                                if (isset($file['filename'])) {
                                    $filename = $file['filename'];
                                    if (strlen($filename) > 0) {
                                        $filename = Internal2::$data2->fixFilename($filename);
                                        $newFilename = Internal\Data::generateNewFilename($filename);
                                        copy($filename, $newFilename);
                                        UploadsSize::add(Internal\Data::filenameToDataKey($newFilename), filesize($newFilename));
                                        $data['files'][$index]['filename'] = $newFilename;
                                    }
                                }
                            }
                        }
                        return $data;
                    },
                    'getUploadsSize' => function ($data) {
                        $size = 0;
                        if (isset($data['files']) && is_array($data['files'])) {
                            foreach ($data['files'] as $index => $file) {
                                if (isset($file['filename'])) {
                                    $filename = $file['filename'];
                                    if (strlen($filename) > 0) {
                                        $filename = Internal2::$data2->fixFilename($filename);
                                        $size += (int) UploadsSize::getItemSize(Internal\Data::filenameToDataKey($filename));
                                    }
                                }
                            }
                        }
                        return $size;
                    }
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['imageGallery'] = function ($options, $idPrefix, $parentSelector) {
                        $groupImageGallery = $options->addGroup(__("bearcms.themes.options.Image gallery"));
                        $groupImageGallery->addOption($idPrefix . "ImageGalleryCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-image-gallery-element"]
                            ]
                        ]);

                        $groupImage = $groupImageGallery->addGroup(__("bearcms.themes.options.Image"));
                        $groupImage->addOption($idPrefix . "ImageGalleryImageCSS", "css", '', [
                            "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                ["rule", $parentSelector . " .bearcms-image-gallery-element-image img", "border:0;"],
                                ["selector", $parentSelector . " .bearcms-image-gallery-element-image"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_VIDEO')) {
                Internal\ElementsTypes::add('video', [
                    'componentSrc' => 'bearcms-video-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsVideoElement.php',
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
                    ],
                    'onDuplicate' => function ($data) {
                        $filename = isset($data['filename']) ? $data['filename'] : '';
                        if (strlen($filename) > 0) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            $newFilename = Internal\Data::generateNewFilename($filename);
                            copy($filename, $newFilename);
                            UploadsSize::add(Internal\Data::filenameToDataKey($newFilename), filesize($newFilename));
                            $data['filename'] = $newFilename;
                        }
                        return $data;
                    },
                    'getUploadsSize' => function ($data) {
                        $filename = isset($data['filename']) ? $data['filename'] : '';
                        if (strlen($filename) > 0) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            return (int) UploadsSize::getItemSize(Internal\Data::filenameToDataKey($filename));
                        }
                        return 0;
                    }
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['video'] = function ($options, $idPrefix, $parentSelector) {
                        $group = $options->addGroup(__("bearcms.themes.options.Video"));
                        $group->addOption($idPrefix . "VideoCSS", "css", '', [
                            "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-video-element", "overflow:hidden;"],
                                ["selector", $parentSelector . " .bearcms-video-element"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_NAVIGATION')) {
                Internal\ElementsTypes::add('navigation', [
                    'componentSrc' => 'bearcms-navigation-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsNavigationElement.php',
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
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['navigation'] = function ($options, $idPrefix, $parentSelector) {
                        $groupNavigation = $options->addGroup(__("bearcms.themes.options.Navigation"));
                        $groupNavigation->addOption($idPrefix . "NavigationCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBorder", "cssBackground"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-navigation-element", "margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-navigation-element ul", "margin:0;padding:0;"],
                                ["selector", $parentSelector . " .bearcms-navigation-element"]
                            ]
                        ]);

                        $groupElements = $groupNavigation->addGroup(__("bearcms.themes.options.Elements"));
                        $groupElements->addOption($idPrefix . "NavigationItemLinkCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-navigation-element-item a", "text-decoration:none;"], // treat as text link // no max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                                ["selector", $parentSelector . " .bearcms-navigation-element-item a"]
                            ]
                        ]);

                        $groupElementsContainer = $groupElements->addGroup(__("bearcms.themes.options.Container"));
                        $groupElementsContainer->addOption($idPrefix . "NavigationItemLinkContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-navigation-element-item", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-navigation-element-item"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_HTML')) {
                Internal\ElementsTypes::add('html', [
                    'componentSrc' => 'bearcms-html-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsHtmlElement.php',
                    'fields' => [
                        [
                            'id' => 'code',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'originalCode',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'renderMode',
                            'type' => 'textbox'
                        ]
                    ]
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['html'] = function ($options, $idPrefix, $parentSelector) {
                        $groupHTMLCode = $options->addGroup(__("bearcms.themes.options.HTML code"));
                        $groupHTMLCode->addOption($idPrefix . "HtmlCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-html-element ul", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-html-element ol", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-html-element li", "list-style-position:inside;margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-html-element p", "margin:0;padding:0;"],
                                ["rule", $parentSelector . " .bearcms-html-element input", "margin:0;padding:0;"],
                                ["selector", $parentSelector . " .bearcms-html-element"]
                            ]
                        ]);

                        $groupLinks = $groupHTMLCode->addGroup(__("bearcms.themes.options.Links"));
                        $groupLinks->addOption($idPrefix . "HtmlLinkCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-html-element a", "text-decoration:none;"],
                                ["selector", $parentSelector . " .bearcms-html-element a"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_BLOG_POSTS')) {
                Internal\ElementsTypes::add('blogPosts', [
                    'componentSrc' => 'bearcms-blog-posts-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsBlogPostsElement.php',
                    'fields' => [
                        [
                            'id' => 'source',
                            'type' => 'textbox'
                        ],
                        [
                            'id' => 'sourceCategoriesIDs',
                            'type' => 'textbox'
                        ],
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
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['blogPosts'] = function ($options, $idPrefix, $parentSelector) {
                        $groupBlogPosts = $options->addGroup(__("bearcms.themes.options.Blog posts"));
                        $groupBlogPosts->addOption($idPrefix . "BlogPostsCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-blog-posts-element"]
                            ]
                        ]);

                        $groupPost = $groupBlogPosts->addGroup(__("bearcms.themes.options.Post"));
                        $groupPost->addOption($idPrefix . "BlogPostsPostCSS", "css", '', [
                            "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post"]
                            ]
                        ]);

                        $groupPostTitle = $groupPost->addGroup(__("bearcms.themes.options.Title"));
                        $groupPostTitle->addOption($idPrefix . "BlogPostsPostTitleCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-post-title", "text-decoration:none;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title"]
                            ]
                        ]);

                        $groupPostTitleContainer = $groupPostTitle->addGroup(__("bearcms.themes.options.Container"));
                        $groupPostTitleContainer->addOption($idPrefix . "BlogPostsPostTitleContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-post-title-container", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title-container"]
                            ]
                        ]);

                        $groupPostDate = $groupPost->addGroup(__("bearcms.themes.options.Date"));
                        $groupPostDate->addOption($idPrefix . "BlogPostsPostDateCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date"]
                            ]
                        ]);

                        $groupPostDateContainer = $groupPostDate->addGroup(__("bearcms.themes.options.Container"));
                        $groupPostDateContainer->addOption($idPrefix . "BlogPostsPostDateContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-post-date-container", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date-container"]
                            ]
                        ]);

                        $groupPostContent = $groupPost->addGroup(__("bearcms.themes.options.Content"));
                        $groupPostContent->addOption($idPrefix . "BlogPostsPostContentCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-post-content", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-post-content"]
                            ]
                        ]);

                        $groupShowMoreButton = $groupBlogPosts->addGroup(__('bearcms.themes.options.blogPosts.Show more button'));
                        $groupShowMoreButton->addOption($idPrefix . "BlogPostsShowMoreButtonCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button"]
                            ]
                        ]);

                        $groupShowMoreButtonContainer = $groupShowMoreButton->addGroup(__("bearcms.themes.options.Container"));
                        $groupShowMoreButtonContainer->addOption($idPrefix . "BlogPostsShowMoreButtonContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button-container", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button-container"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_COMMENTS')) {
                Internal\ElementsTypes::add('comments', [
                    'componentSrc' => 'bearcms-comments-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsCommentsElement.php',
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
                    'onDelete' => function ($data) {
                        if (isset($data['threadID'])) {
                            $this->app->data->delete('bearcms/comments/thread/' . md5($data['threadID']) . '.json');
                        }
                    },
                    'onDuplicate' => function ($data) {
                        if (isset($data['threadID'])) {
                            $newThreadID = Internal\Data\Comments::generateNewThreadID();
                            Internal\Data\Comments::copyThread($data['threadID'], $newThreadID);
                            $data['threadID'] = $newThreadID;
                        }
                        return $data;
                    }
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['comments'] = function ($options, $idPrefix, $parentSelector) {
                        $groupComments = $options->addGroup(__("bearcms.themes.options.Comments"));

                        $groupComment = $groupComments->addGroup(__("bearcms.themes.options.comments.Comment"));
                        $groupComment->addOption($idPrefix . "CommentsCommentCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-comment", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-comments-comment"]
                            ]
                        ]);

                        $groupCommentAuthorName = $groupComment->addGroup(__("bearcms.themes.options.comments.Author name"));
                        $groupCommentAuthorName->addOption($idPrefix . "CommentsAuthorNameCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-comments-comment-author-name"]
                            ]
                        ]);

                        $groupCommentAuthorImage = $groupComment->addGroup(__("bearcms.themes.options.comments.Author image"));
                        $groupCommentAuthorImage->addOption($idPrefix . "CommentsAuthorImageCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-comment-author-image", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-comments-comment-author-image"]
                            ]
                        ]);

                        $groupCommentDate = $groupComment->addGroup(__("bearcms.themes.options.comments.Date"));
                        $groupCommentDate->addOption($idPrefix . "CommentsDateCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-comments-comment-date"]
                            ]
                        ]);

                        $groupCommentText = $groupComment->addGroup(__("bearcms.themes.options.comments.Text"));
                        $groupCommentText->addOption($idPrefix . "CommentsTextCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-comments-comment-text"]
                            ]
                        ]);

                        $groupCommentTextLinks = $groupComment->addGroup(__("bearcms.themes.options.comments.Text links"));
                        $groupCommentTextLinks->addOption($idPrefix . "CommentsTextLinksCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-comments-comment-text a"]
                            ]
                        ]);

                        $groupTextInput = $groupComments->addGroup(__("bearcms.themes.options.comments.Text input"));
                        $groupTextInput->addOption($idPrefix . "CommentsTextInputCSS", "css", '', [
                            "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-element-text-input", "box-sizing:border-box;border:0;margin:0;padding:0;"],
                                ["selector", $parentSelector . " .bearcms-comments-element-text-input"]
                            ]
                        ]);

                        $groupSendButton = $groupComments->addGroup(__("bearcms.themes.options.comments.Send button"));
                        $groupSendButton->addOption($idPrefix . "CommentsSendButtonCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-element-send-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                ["selector", $parentSelector . " .bearcms-comments-element-send-button"]
                            ]
                        ]);

                        $groupSendButtonWaiting = $groupSendButton->addGroup(__("bearcms.themes.options.comments.Send button waiting"));
                        $groupSendButtonWaiting->addOption($idPrefix . "CommentsSendButtonWaitingCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-element-send-button-waiting", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                ["selector", $parentSelector . " .bearcms-comments-element-send-button-waiting"]
                            ]
                        ]);

                        $groupShowMoreButton = $groupComments->addGroup(__("bearcms.themes.options.comments.Show more button"));
                        $groupShowMoreButton->addOption($idPrefix . "CommentsShowMoreButtonCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-show-more-button", "box-sizing:border-box;display:inline-block;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"],
                                ["selector", $parentSelector . " .bearcms-comments-show-more-button"]
                            ]
                        ]);

                        $groupShowMoreButtonContainer = $groupShowMoreButton->addGroup(__("bearcms.themes.options.comments.Container"));
                        $groupShowMoreButtonContainer->addOption($idPrefix . "CommentsShowMoreButtonContainerCSS", "css", '', [
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-comments-show-more-button-container", "box-sizing:border-box;"],
                                ["selector", $parentSelector . " .bearcms-comments-show-more-button-container"]
                            ]
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_SEPARATOR')) {
                Internal\ElementsTypes::add('separator', [
                    'componentSrc' => 'bearcms-separator-element',
                    'componentFilename' => $this->context->dir . '/components/bearcmsSeparatorElement.php',
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
                        ]
                    ]
                ]);
                if ($hasThemes) {
                    Internal\Themes::$elementsOptions['separator'] = function ($options, $idPrefix, $parentSelector) {
                        $group = $options->addGroup(__("bearcms.themes.options.Separator"));

                        $groupLarge = $group->addGroup(__("bearcms.themes.options.Separator.Large"));
                        $groupLarge->addOption($idPrefix . "SeparatorLargeCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-separator-element-large"]
                            ],
                            "value" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"90%"}'
                        ]);

                        $groupMedium = $group->addGroup(__("bearcms.themes.options.Separator.Medium"));
                        $groupMedium->addOption($idPrefix . "SeparatorMediumCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-separator-element-medium"]
                            ],
                            "value" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"60%"}'
                        ]);

                        $groupSmall = $group->addGroup(__("bearcms.themes.options.Separator.Small"));
                        $groupSmall->addOption($idPrefix . "SeparatorSmallCSS", "css", '', [
                            "cssTypes" => ["cssBackground", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                            "cssOutput" => [
                                ["selector", $parentSelector . " .bearcms-separator-element-small"]
                            ],
                            "value" => '{"background-color":"#555","height":"1px","margin-left":"auto","margin-right":"auto","margin-top":"2rem","margin-bottom":"2rem","width":"30%"}'
                        ]);
                    };
                }
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_COLUMNS')) {
                Internal\Themes::$elementsOptions['columns'] = function ($options, $idPrefix, $parentSelector, $context) {
                    if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                        $optionsGroup = $options;
                    } else {
                        throw new \Exception('Not supported in theme context');
                    }
                    $optionsGroup->addOption($idPrefix . "widths", "columnsWidths", __('bearcms.themes.options.columns.ColumnsCount'), [
                        "defaultValue" => ",",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "autoVerticalWidth", "columnsAutoVerticalWidth",  __('bearcms.themes.options.columns.AutoVertical'), [
                        "defaultValue" => "500px",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "elementsSpacing", "columnsElementsSpacing",  __('bearcms.themes.options.columns.ElementsSpacing'), [
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                };
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_FLOATING_BOX')) {
                Internal\Themes::$elementsOptions['floatingBox'] = function ($options, $idPrefix, $parentSelector, $context) {
                    if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                        $optionsGroup = $options;
                    } else {
                        throw new \Exception('Not supported in theme context');
                    }
                    $optionsGroup->addOption($idPrefix . "position", "floatingBoxPosition", __('bearcms.themes.options.floatingBox.Position'), [
                        "defaultValue" => "left",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "width", "floatingBoxWidth", __('bearcms.themes.options.floatingBox.Width'), [
                        "defaultValue" => "50%",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "autoVerticalWidth", "floatingBoxAutoVerticalWidth",  __('bearcms.themes.options.floatingBox.AutoVertical'), [
                        "defaultValue" => "500px",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "elementsSpacing", "floatingBoxElementsSpacing",  __('bearcms.themes.options.floatingBox.ElementsSpacing'), [
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                };
            }
            if ($hasElements || Config::hasFeature('ELEMENTS_FLEXIBLE_BOX')) {
                Internal\Themes::$elementsOptions['flexibleBox'] = function ($options, $idPrefix, $parentSelector, $context) {
                    if ($context === Internal\Themes::OPTIONS_CONTEXT_ELEMENT) {
                        $optionsGroup = $options;
                    } else {
                        throw new \Exception('Not supported in theme context');
                    }
                    $optionsGroup->addOption($idPrefix . "direction", "flexibleBoxDirection", __('bearcms.themes.options.flexibleBox.Direction'), [
                        "defaultValue" => "column",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "rowAlignment", "flexibleBoxRowAlignment",  __('bearcms.themes.options.flexibleBox.RowAlignment'), [
                        "defaultValue" => "left",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "autoVerticalWidth", "flexibleBoxAutoVerticalWidth",  __('bearcms.themes.options.flexibleBox.AutoVertical'), [
                        "defaultValue" => "500px",
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "elementsSpacing", "flexibleBoxElementsSpacing",  __('bearcms.themes.options.flexibleBox.ElementsSpacing'), [
                        "onHighlight" => [['cssSelector', $parentSelector]]
                    ]);
                    $optionsGroup->addOption($idPrefix . "css", "css", '', [
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"], // cssMargin conflicts with the elements spacing margin-bottom
                        "cssOutput" => [
                            ["selector", $parentSelector]
                        ]
                    ]);
                };
            }

            $this->app->clientPackages
                ->add('-bearcms-elements-lazy-load', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    $package->addJSFile($this->context->assets->getURL('assets/elementsLazyLoad.min.js', ['cacheMaxAge' => 999999999, 'version' => 5]));
                    $data = [
                        __('bearcms.elements.LoadingMore')
                    ];
                    $package->get = 'bearCMS.elementsLazyLoad.initialize(' . json_encode($data) . ');';
                });
        }

        // Load the CMS managed addons
        if (Config::hasFeature('ADDONS')) {
            Internal\Data\Addons::addToApp();
        }

        // Register the system pages
        if ($hasServer) {
            if (Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_DEFAULT')) {
                $this->app->routes
                    ->add(Config::$adminPagesPathPrefix . 'loggedin/', function () {
                        return new App\Response\TemporaryRedirect($this->app->request->base . '/');
                    })
                    ->add([Config::$adminPagesPathPrefix, Config::$adminPagesPathPrefix . '*/'], function () {
                        return Internal\Controller::handleAdminPage();
                    })
                    ->add([rtrim(Config::$adminPagesPathPrefix, '/'), Config::$adminPagesPathPrefix . '*'], function () {
                        return new App\Response\PermanentRedirect($this->app->request->base . $this->app->request->path . '/');
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
                ->add('/files/preview/*', function ($request) {
                    return Internal\Controller::handleFilePreview($request);
                })
                ->add('/files/download/?', function ($request) {
                    return Internal\Controller::handleFileDownload($request);
                });
        }

        // Register some other pages
        $this->app->routes
            ->add(['/rss.xml', '/rss.*.xml'], [
                [$this, 'disabledCheck'],
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
                [$this, 'disabledCheck'],
                function () {
                    return Internal\Controller::handleSitemap();
                }
            ])
            ->add('/robots.txt', [
                [$this, 'disabledCheck'],
                function () {
                    return Internal\Controller::handleRobots();
                }
            ])
            ->add('/-link-rel-icon-*', [
                [$this, 'disabledCheck'],
                function () {
                    $size = (int) str_replace('/-link-rel-icon-', '', (string) $this->app->request->path);
                    if ($size >= 16 && $size <= 512) {
                        $filename = \BearCMS\Internal\Data\Settings::getIconForSize($size);
                        if ($filename !== null) {
                            $content = $this->app->assets->getContent($filename, ['width' => $size, 'height' => $size]);
                            $response = new App\Response($content);
                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                            if ($extension !== '') {
                                $response->headers->set($response->headers->make('Content-Type', 'image/' . $extension));
                            }
                            $response->headers->set($response->headers->make('Cache-Control', 'public, max-age=3600'));
                            return $response;
                        }
                        return new App\Response\NotFound();
                    }
                }
            ])
            ->add(['/-meta-og-image', '*/-meta-og-image'], [
                [$this, 'disabledCheck'],
                function (App\Request $request) {
                    $path = (string) $request->path;
                    if (strpos($path, '-meta-og-image') !== false) {
                        $path = substr($path, 0, -strlen('-meta-og-image'));
                    }

                    $iconCache = null;
                    $getIconFilename = function () use (&$iconCache) {
                        if ($iconCache === null) {
                            $iconCache = [\BearCMS\Internal\Data\Settings::getIconForSize(2000)];
                        }
                        return $iconCache[0];
                    };

                    $filename = null;

                    $containerID = null;
                    if (strpos($path, Config::$blogPagesPathPrefix) === 0) {
                        $slug = rtrim(substr($path, strlen(Config::$blogPagesPathPrefix)), '/');
                        $blogPosts = $this->data->blogPosts->getList();
                        foreach ($blogPosts as $blogPost) {
                            if ($blogPost->status === 'published' && $blogPost->slug === $slug) {
                                if (strlen($blogPost->image) > 0) {
                                    $filename = $blogPost->image;
                                    break;
                                }
                                $containerID = 'bearcms-blogpost-' . $blogPost->id;
                                break;
                            }
                        }
                    } else {
                        $pages = $this->data->pages->getList();
                        foreach ($pages as $page) {
                            if ($page->status === 'public' && $page->path === $path) {
                                if (strlen($page->image) > 0) {
                                    $filename = $page->image;
                                    break;
                                }
                                $containerID = 'bearcms-page-' . $page->id;
                                break;
                            }
                        }
                        if ($path === '/' && $filename === null) {
                            $iconFilename = $getIconFilename();
                            if ($iconFilename !== null) {
                                $filename = $iconFilename;
                            } else {
                                $containerID = 'bearcms-page-home';
                            }
                        }
                    }

                    $imageUrl = null;

                    if ($filename !== null) {
                        $imageUrl = $this->app->assets->getURL($filename, ['cacheMaxAge' => 999999999]);
                    } else {
                        if ($containerID !== null) {
                            $content = $this->app->components->process('<component src="bearcms-elements" id="' . htmlentities($containerID) . '"/>');
                            if (strpos($content, '<img') !== false) {
                                $html5Document = new HTML5DOMDocument();
                                $html5Document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                                $imageElement = $html5Document->querySelector('img');
                                if ($imageElement !== null) {
                                    $imageUrl = $imageElement->getAttribute('src');
                                }
                            }
                        }
                        if ($imageUrl === null) { // use the website icon if no image found on page
                            $filename = $getIconFilename();
                            if ($filename !== null) {
                                $imageUrl = $this->app->assets->getURL($filename, ['cacheMaxAge' => 999999999]);
                            }
                        }
                    }

                    if ($imageUrl !== null) {
                        $response = new App\Response\TemporaryRedirect($imageUrl);
                        $response->headers->set($response->headers->make('Cache-Control', 'public, max-age=3600'));
                        return $response;
                    }
                }
            ]);

        if (Config::hasFeature('COMMENTS')) {
            $this->app->serverRequests
                ->add('bearcms-comments-load-more', function ($data) {
                    if (isset($data['serverData'], $data['count'])) {
                        $count = (int) $data['count'];
                        $serverData = Internal\TempClientData::get($data['serverData']);
                        if (is_array($serverData) && isset($serverData['threadID'])) {
                            $threadID = $serverData['threadID'];
                            $html = $this->app->components->process('<component src="file:' . $this->context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($count) . '" threadID="' . htmlentities($threadID) . '" />');
                            return json_encode([
                                'html' => $html
                            ]);
                        }
                    }
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
        }

        if (Config::hasFeature('BLOG')) {
            $this->app->routes
                ->add([Config::$blogPagesPathPrefix . '?', Config::$blogPagesPathPrefix . '?/'], [
                    [$this, 'disabledCheck'],
                    function () {
                        $slug = (string) $this->app->request->path->getSegment(1);
                        $slugsList = Internal\Data\BlogPosts::getSlugsList('published');
                        $blogPostID = array_search($slug, $slugsList);
                        if ($blogPostID === false && substr($slug, 0, 1) === '-') {
                            $blogPost = $this->data->blogPosts->get(substr($slug, 1));
                            if ($blogPost !== null) {
                                $status = $blogPost->status;
                                if ($status === 'published') {
                                    return new App\Response\PermanentRedirect($this->app->urls->get(Config::$blogPagesPathPrefix . $blogPost->slug . '/'));
                                } elseif ($status === 'draft') {
                                    // allow access
                                } else { // private
                                    if (!((Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) && $this->currentUser->exists())) {
                                        return;
                                    }
                                }
                                $blogPostID = $blogPost->id;
                            }
                        }
                        if ($blogPostID !== false) {
                            $blogPost = $this->data->blogPosts->get($blogPostID);
                            if ($blogPost !== null) {
                                $path = $this->app->request->path->get();
                                $hasSlash = substr($path, -1) === '/';
                                if (!$hasSlash) {
                                    return new App\Response\PermanentRedirect($this->app->request->base . $this->app->request->path . '/');
                                }
                                $content = '<html><head>';
                                $title = isset($blogPost->titleTagContent) ? trim($blogPost->titleTagContent) : '';
                                if (!isset($title[0])) {
                                    $title = isset($blogPost->title) ? trim($blogPost->title) : '';
                                }
                                $description = isset($blogPost->descriptionTagContent) ? trim($blogPost->descriptionTagContent) : '';
                                $keywords = isset($blogPost->keywordsTagContent) ? trim($blogPost->keywordsTagContent) : '';
                                if (isset($title[0])) {
                                    $content .= '<title>' . htmlspecialchars($title) . '</title>';
                                }
                                if (isset($description[0])) {
                                    $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                                }
                                if (isset($keywords[0])) {
                                    $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                                }
                                $content .= '<style>'
                                    . '.bearcms-blogpost-page-title-container{word-break:break-word;}'
                                    . '.bearcms-blogpost-page-content{word-break:break-word;}'
                                    . '</style>';
                                $content .= '</head><body>';
                                $content .= '<div class="bearcms-blogpost-page-title-container"><h1 class="bearcms-blogpost-page-title">' . htmlspecialchars($blogPost->title) . '</h1></div>';
                                $content .= '<div class="bearcms-blogpost-page-date-container"><div class="bearcms-blogpost-page-date">' . ($blogPost->status === 'published' ? $this->app->localization->formatDate($blogPost->publishedTime, ['date']) : ($blogPost->status === 'draft' ? __('bearcms.blogPost.draft') : __('bearcms.blogPost.private'))) . '</div></div>';
                                $content .= '<div class="bearcms-blogpost-page-content"><bearcms-elements id="bearcms-blogpost-' . $blogPostID . '"/></div>';
                                $settings = $this->data->settings->get();
                                if ($settings->allowCommentsInBlogPosts) {
                                    $content .= '<div class="bearcms-blogpost-page-comments-block-separator"><component src="bearcms-separator-element" size="large"/></div>';
                                    $content .= '<div class="bearcms-blogpost-page-comments-title-container"><component src="bearcms-heading-element" text="' . __('bearcms.pages.blogPost.Comments') . '" size="small"/></div>';
                                    $content .= '<div class="bearcms-blogpost-page-comments-container"><component src="bearcms-comments-element" threadID="bearcms-blogpost-' . $blogPost->id . '"/></div>';
                                }
                                $categoriesIDs = $blogPost->categoriesIDs;
                                if ($settings->showRelatedBlogPosts && !empty($categoriesIDs)) {
                                    $links = [];
                                    $relatedBlogPosts = $this->data->blogPosts->getList()
                                        ->filterBy('status', 'published')
                                        ->sortBy('publishedTime', 'desc');
                                    foreach ($relatedBlogPosts as $relatedBlogPost) {
                                        if ($blogPost->id === $relatedBlogPost->id || sizeof(array_intersect($categoriesIDs, $relatedBlogPost->categoriesIDs)) === 0) {
                                            continue;
                                        }
                                        $relatedBlogTitle = strlen($relatedBlogPost->title) > 0 ? $relatedBlogPost->title : 'Unknown';
                                        $relatedBlogURL = $this->app->urls->get(Config::$blogPagesPathPrefix . $relatedBlogPost->slug . '/');
                                        $links[] = '<a href="' . htmlentities($relatedBlogURL) . '" title="' . htmlentities($relatedBlogTitle) . '">' . htmlspecialchars($relatedBlogTitle) . '</a>';
                                        if (sizeof($links) >= 5) {
                                            break;
                                        }
                                    }
                                    if (!empty($links)) {
                                        $content .= '<div class="bearcms-blogpost-page-related-block-separator"><component src="bearcms-separator-element" size="large"/></div>';
                                        $content .= '<div class="bearcms-blogpost-page-related-title-container"><component src="bearcms-heading-element" text="' . __('bearcms.pages.blogPost.Continue reading') . '" size="small"/></div>';
                                        $content .= '<div class="bearcms-blogpost-page-related-container"><component src="bearcms-text-element" text="' . htmlentities(implode('<br>', $links)) . '"/></div>';
                                    }
                                }
                                $content .= '</body></html>';

                                $response = new App\Response\HTML($content);
                                if ($this->hasEventListeners('internalMakeBlogPostPageResponse')) {
                                    $eventDetails = new \BearCMS\Internal\MakeBlogPostPageResponseEventDetails($response, $blogPostID);
                                    $this->dispatchEvent('internalMakeBlogPostPageResponse', $eventDetails);
                                }
                                $applyContext = $this->makeApplyContext();
                                if (strlen($blogPost->language) > 0) {
                                    $applyContext->language = $blogPost->language;
                                }
                                $this->apply($response, $applyContext);
                                if ($blogPost->status !== 'published') {
                                    $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));
                                    $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'));
                                }
                                return $response;
                            }
                        }
                    }
                ]);
            \BearCMS\Internal\Sitemap::register(function (\BearCMS\Internal\Sitemap\Sitemap $sitemap) {
                $list = Internal\Data\BlogPosts::getSlugsList('published');
                foreach ($list as $blogPostID => $slug) {
                    $url = $this->app->urls->get(Config::$blogPagesPathPrefix . $slug . '/');
                    $sitemap->addURL($url, function () use ($blogPostID, $url) {
                        $details = Internal\Data\BlogPosts::getLastModifiedDetails($blogPostID);
                        Internal\Sitemap::addLastModifiedDetails($url, $details);
                        return Internal\Sitemap::getDateFromLastModifiedDetails($details);
                    });
                }
            });
            $this->app->serverRequests
                ->add('bearcms-blogposts-load-more', function ($data) {
                    if (isset($data['serverData'], $data['serverData'])) {
                        $serverData = Internal\TempClientData::get($data['serverData']);
                        if (is_array($serverData) && isset($serverData['componentHTML'])) {
                            $content = $this->app->components->process($serverData['componentHTML']);
                            return json_encode([
                                'content' => $content
                            ]);
                        }
                    }
                });

            if (Config::hasFeature('THEMES')) {
                Internal\Themes::$pagesOptions['blog'] = function ($options) {
                    $group = $options->addGroup(__("bearcms.themes.options.Blog post page"));

                    $groupTitle = $group->addGroup(__("bearcms.themes.options.Title"));
                    $groupTitle->addOption("blogPostPageTitleCSS", "css", '', [
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-title", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                            ["selector", ".bearcms-blogpost-page-title"]
                        ]
                    ]);

                    $groupTitleContainer = $groupTitle->addGroup(__("bearcms.themes.options.Container"));
                    $groupTitleContainer->addOption("blogPostPageTitleContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-title-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-title-container"]
                        ]
                    ]);

                    $groupDate = $group->addGroup(__("bearcms.themes.options.Date"));
                    $groupDate->addOption("blogPostPageDateVisibility", "list", __('bearcms.themes.options.Visibility'), [
                        "values" => [
                            [
                                "value" => "1",
                                "name" => __('bearcms.themes.options.Visible')
                            ],
                            [
                                "value" => "0",
                                "name" => __('bearcms.themes.options.Hidden')
                            ]
                        ],
                        "value" => "1"
                    ]);
                    $groupDate->addOption("blogPostPageDateCSS", "css", '', [
                        "cssTypes" => ["cssText", "cssTextShadow"],
                        "cssOutput" => [
                            ["selector", ".bearcms-blogpost-page-date"]
                        ]
                    ]);
                    $groupDateContainer = $groupDate->addGroup(__("bearcms.themes.options.Container"));
                    $groupDateContainer->addOption("blogPostPageDateContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-date-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-date-container"]
                        ]
                    ]);

                    $groupContent = $group->addGroup(__("bearcms.themes.options.Content"));
                    $groupContent->addOption("blogPostPageContentCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-content", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-content"]
                        ]
                    ]);

                    $groupComments = $group->addGroup(__("bearcms.themes.options.Comments"));
                    $groupCommentsSeparator = $groupComments->addGroup(__("bearcms.themes.options.Separator"));
                    $groupCommentsSeparator->addOption("blogPostPageCommentsBlockSeparatorCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-comments-block-separator", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-comments-block-separator"]
                        ]
                    ]);
                    $groupCommentsTitle = $groupComments->addGroup(__("bearcms.themes.options.Title"));
                    $groupCommentsTitle->addOption("blogPostPageCommentsTitleContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-comments-title-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-comments-title-container"]
                        ]
                    ]);
                    $groupCommentsContainer = $groupComments->addGroup(__("bearcms.themes.options.Comments"));
                    $groupCommentsContainer->addOption("blogPostPageCommentsContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-comments-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-comments-container"]
                        ]
                    ]);

                    $groupRelated = $group->addGroup(__("bearcms.themes.options.Related posts"));
                    $groupRelatedSeparator = $groupRelated->addGroup(__("bearcms.themes.options.Separator"));
                    $groupRelatedSeparator->addOption("blogPostPageRelatedBlockSeparatorCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-related-block-separator", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-related-block-separator"]
                        ]
                    ]);
                    $groupRelatedTitle = $groupRelated->addGroup(__("bearcms.themes.options.Title"));
                    $groupRelatedTitle->addOption("blogPostPageRelatedTitleContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-related-title-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-related-title-container"]
                        ]
                    ]);
                    $groupRelatedContainer = $groupRelated->addGroup(__("bearcms.themes.options.Related list"));
                    $groupRelatedContainer->addOption("blogPostPageRelatedContainerCSS", "css", '', [
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".bearcms-blogpost-page-related-container", "box-sizing:border-box;"],
                            ["selector", ".bearcms-blogpost-page-related-container"]
                        ]
                    ]);
                };
            }
            $this->app->clientPackages
                ->add('-bearcms-blog-posts-element', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) {
                    $package->addJSCode(include $this->context->dir . '/components/bearcmsBlogPostsElement/blogPostsElement.min.js.php');
                    //$package->addJSCode(file_get_contents(__DIR__ . '/../dev/blogPostsElement.js'));
                });
        }

        // Register a home page and the dynamic pages handler
        if (Config::hasFeature('PAGES')) {
            $this->app->routes
                ->add('*', [
                    [$this, 'disabledCheck'],
                    function () {
                        $path = $this->app->request->path->get();
                        if ($path === '/') {
                            if (Config::$autoCreateHomePage) {
                                $pageID = 'home';
                            } else {
                                $pageID = false;
                            }
                        } else {
                            $hasSlash = substr($path, -1) === '/';
                            $pathsList = Internal\Data\Pages::getPathsList((Config::hasFeature('USERS') || Config::hasFeature('USERS_LOGIN_*')) && $this->currentUser->exists() ? 'all' : 'publicOrSecret');
                            if ($hasSlash) {
                                $pageID = array_search($path, $pathsList);
                            } else {
                                $pageID = array_search($path . '/', $pathsList);
                                if ($pageID !== false) {
                                    return new App\Response\PermanentRedirect($this->app->request->base . $this->app->request->path . '/');
                                }
                            }
                        }
                        if ($pageID !== false) {
                            $response = $this->disabledCheck();
                            if ($response !== null) {
                                return $response;
                            }
                            $title = '';
                            $description = '';
                            $keywords = '';
                            $status = null;
                            $found = false;
                            $settings = $this->data->settings->get();
                            $page = $this->data->pages->get($pageID);
                            if ($page !== null) {
                                $title = isset($page->titleTagContent) ? trim($page->titleTagContent) : '';
                                if (!isset($title[0])) {
                                    $title = isset($page->name) ? trim($page->name) : '';
                                }
                                $description = isset($page->descriptionTagContent) ? trim($page->descriptionTagContent) : '';
                                $keywords = isset($page->keywordsTagContent) ? trim($page->keywordsTagContent) : '';
                                $found = true;
                                $status = $page->status;
                            }
                            if ($pageID === 'home') {
                                if (!isset($title[0])) {
                                    $title = trim($settings->title);
                                }
                                if (!isset($description[0])) {
                                    $description = trim($settings->description);
                                }
                                $found = true;
                                $status = 'public';
                            }
                            if ($found) {
                                $content = '<html><head>';
                                if (isset($title[0])) {
                                    $content .= '<title>' . htmlspecialchars($title) . '</title>';
                                }
                                if (isset($description[0])) {
                                    $content .= '<meta name="description" content="' . htmlentities($description) . '"/>';
                                }
                                if (isset($keywords[0])) {
                                    $content .= '<meta name="keywords" content="' . htmlentities($keywords) . '"/>';
                                }
                                $content .= '</head><body>';
                                $content .= '<bearcms-elements id="bearcms-page-' . $pageID . '" editable="true"/>';
                                $content .= '</body></html>';

                                $response = new App\Response\HTML($content);
                                if ($this->hasEventListeners('internalMakePageResponse')) {
                                    $eventDetails = new \BearCMS\Internal\MakePageResponseEventDetails($response, $pageID);
                                    $this->dispatchEvent('internalMakePageResponse', $eventDetails);
                                }

                                $applyContext = $this->makeApplyContext();
                                $potentialLanguage = $this->app->request->path->getSegment(0);
                                if (strlen($potentialLanguage) > 0 && array_search($potentialLanguage, $settings->languages) !== false) {
                                    $applyContext->language = $potentialLanguage;
                                }
                                $this->apply($response, $applyContext);
                                if ($status !== 'public') {
                                    $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));
                                    $response->headers->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'));
                                }
                                return $response;
                            }
                        }
                    }
                ]);
            \BearCMS\Internal\Sitemap::register(function (\BearCMS\Internal\Sitemap\Sitemap $sitemap) {
                $list = Internal\Data\Pages::getPathsList('public');
                if (Config::$autoCreateHomePage) {
                    $list['home'] = '/';
                }
                $appURLs = $this->app->urls;
                foreach ($list as $pageID => $path) {
                    $url = $appURLs->get($path);
                    $sitemap->addURL($url, function () use ($pageID, $url) {
                        $details = Internal\Data\Pages::getLastModifiedDetails($pageID);
                        Internal\Sitemap::addLastModifiedDetails($url, $details);
                        return Internal\Sitemap::getDateFromLastModifiedDetails($details);
                    });
                }
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
                                $themeStyles = Internal\Themes::getStyles($id, false);
                                foreach ($themeStyles as $themeStyle) {
                                    if (isset($themeStyle['media'])) {
                                        foreach ($themeStyle['media'] as $mediaItem) {
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
                }
                // Element combination media file
                $matchingDir = $this->context->dir . '/assets/ec/';
                if (strpos($filename, $matchingDir) === 0) {
                    $originalFilename = ElementsCombinations::getOriginalMediaFilename($filename);
                    $details->filename = $originalFilename !== null ? $originalFilename : '';
                }
            })
            ->addEventListener('prepare', function (\BearFramework\App\Assets\PrepareEventDetails $details) {
                $filename = $details->filename;
                $addonAssetsDir = $this->context->dir . '/assets/';
                if (strpos($filename, $addonAssetsDir) === 0) {

                    $downloadUrl = function ($url) {
                        $tempFileKey = '.temp/bearcms/urlassets/' . md5($url) . '.' . pathinfo($url, PATHINFO_EXTENSION);
                        $tempFilename = $this->app->data->getFilename($tempFileKey);
                        if ($this->app->data->exists($tempFileKey)) {
                            return $tempFilename;
                        } else {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            $response = curl_exec($ch);
                            $valid = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200 && strlen($response) > 0;
                            curl_close($ch);
                            if ($valid) {
                                $this->app->data->set($this->app->data->make($tempFileKey, $response));
                                return $tempFilename;
                            } else {
                                throw new \Exception('Cannot download file from URL (' . $url . ')');
                            }
                        }
                    };

                    // Proxy
                    $matchingDir = $addonAssetsDir . 'p/';
                    if (strpos($filename, $matchingDir) === 0) {
                        $details->returnValue = null;
                        $pathParts = explode('/', substr($filename, strlen($matchingDir)), 3);
                        if (isset($pathParts[0], $pathParts[1], $pathParts[2])) {
                            $url = $pathParts[0] . '://' . $pathParts[1] . '/' . str_replace('\\', '/', $pathParts[2]);
                            $details->returnValue = $downloadUrl($url);
                        }
                        return;
                    }

                    // Download the server files
                    $matchingDir = $addonAssetsDir . 's/';
                    if (strpos($filename, $matchingDir) === 0) {
                        $details->returnValue = null;
                        $url = Config::$serverUrl . str_replace('\\', '/', substr($filename, strlen($matchingDir)));
                        $details->returnValue = $downloadUrl($url);
                    }
                }
            });

        $cachedAssetsDetails = null;
        $getCachedAssetsDetails = function () use (&$cachedAssetsDetails): array {
            if ($cachedAssetsDetails === null) {
                $value = $this->app->cache->getValue('bearcms-assets-details');
                if ($value === null) {
                    $value = $this->app->data->getValue('.temp/bearcms/assets-details.json');
                    $this->app->cache->set($this->app->cache->make('bearcms-assets-details', $value === null ? '-1' : $value));
                } elseif ($value === '-1') { // in cache but empty
                    $value = null;
                }
                $cachedAssetsDetails = $value === null ? [] : json_decode($value, true);
                if (!is_array($cachedAssetsDetails)) {
                    $cachedAssetsDetails = [];
                }
            }
            return $cachedAssetsDetails;
        };
        $saveCachedAssetsDetails = function () use (&$cachedAssetsDetails) {
            $this->app->data->setValue('.temp/bearcms/assets-details.json', json_encode($cachedAssetsDetails));
            $this->app->cache->delete('bearcms-assets-details');
        };
        $this->app->assets
            ->addEventListener('beforeGetDetails', function (\BearFramework\App\Assets\BeforeGetDetailsEventDetails $details) use ($getCachedAssetsDetails) {
                $filename = $details->filename;
                if (strpos($filename, 'appdata://bearcms/') === 0) {
                    $filenameMD5 = md5($filename);
                    $list = $details->list;
                    $cache = $getCachedAssetsDetails();
                    $result = [];
                    if (isset($cache[$filenameMD5])) {
                        foreach ($list as $key) {
                            if (array_key_exists($key, $cache[$filenameMD5])) {
                                $result[$key] = $cache[$filenameMD5][$key];
                            } else {
                                return;
                            }
                        }
                        $details->returnValue = $result;
                    }
                }
            })
            ->addEventListener('getDetails', function (\BearFramework\App\Assets\GetDetailsEventDetails $details) use ($getCachedAssetsDetails, $saveCachedAssetsDetails, &$cachedAssetsDetails) {
                $filenameMD5 = md5($details->filename);
                $cache = $getCachedAssetsDetails();
                $hasChange = false;
                if (!isset($cache[$filenameMD5])) {
                    $cache[$filenameMD5] = [];
                    $hasChange = true;
                }
                foreach ($details->returnValue as $key => $value) {
                    if (!isset($cache[$filenameMD5][$key]) || $cache[$filenameMD5][$key] !== $value) {
                        $cache[$filenameMD5][$key] = $value;
                        $hasChange = true;
                    }
                }
                if ($hasChange) {
                    $cachedAssetsDetails = $cache;
                    $saveCachedAssetsDetails();
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

        if (Config::hasFeature('THEMES') && Config::$addDefaultThemes) {
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
                    $threadID = $data['threadID'];
                    $commentID = $data['commentID'];
                    $comments = Internal2::$data2->comments->getList()
                        ->filterBy('threadID', $threadID)
                        ->filterBy('id', $commentID);
                    if (isset($comments[0])) {
                        $comment = $comments[0];
                        $comments = Internal2::$data2->comments->getList()
                            ->filterBy('status', 'pendingApproval');
                        $pendingApprovalCount = $comments->count();
                        $profile = Internal\PublicProfile::getFromAuthor($comment->author);
                        Internal\Data::sendNotification('comments', $comment->status, $profile->name, $comment->text, $pendingApprovalCount);
                    }
                });
        }

        $this->app->tasks
            ->define('bearcms-sitemap-process-changes', function () {
                Internal\Sitemap::processChangedDataKeys();
            })
            ->define('bearcms-sitemap-update-cached-dates', function ($paths) {
                foreach ($paths as $path) {
                    Internal\Sitemap::addUpdateCachedDateTasks($path);
                }
            })
            ->define('bearcms-sitemap-update-cached-date', function ($path) {
                Internal\Sitemap::updateCachedDate($path);
            })
            ->define('bearcms-sitemap-check-for-changes', function () {
                Internal\Sitemap::checkSitemapForChanges();
            })
            ->define('bearcms-sitemap-notify-search-engines', function () {
                Internal\Sitemap::notifySearchEngines();
            });

        // Initialize to add asset dirs
        $currentThemeID = Internal\CurrentTheme::getID();
        Internal\Themes::initialize($currentThemeID);

        $theme = Internal\Themes::get($currentThemeID);
        if ($theme !== null) { // just in case it's registered later or other
            if ($theme->useDefaultElementsCombinations) {
                if ($hasElements || Config::hasFeature('ELEMENTS_*')) {
                    ElementsCombinations::addDefault();
                }
            }
        }

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
            $previousLocale = $this->app->localization->getLocale();
            $this->app->localization->setLocale($language);
        }
        $this->applyTheme($response, $applyContext);
        $this->process($response, $applyContext);
        $this->applyDefaults($response, $applyContext);
        $this->applyAdminUI($response, $applyContext);
        if ($language !== null) {
            $this->app->localization->setLocale($previousLocale);
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

        $strlen = function (string $string) {
            return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
        };

        $substr = function (string $string, int $start, int $length = null) {
            return function_exists('mb_substr') ? mb_substr($string, $start, $length) : substr($string, $start, $length);
        };

        $strtolower = function (string $string) {
            return function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
        };

        $metaElements = $document->querySelectorAll('meta');
        $generateDescriptionMetaTag = true;
        $generateKeywordsMetaTag = true;
        foreach ($metaElements as $metaElement) {
            $metaElementName = $metaElement->getAttribute('name');
            if ($metaElementName === 'description' && $strlen($metaElement->getAttribute('content')) > 0) {
                $generateDescriptionMetaTag = false;
            } elseif ($metaElementName === 'keywords' && $strlen($metaElement->getAttribute('content')) > 0) {
                $generateKeywordsMetaTag = false;
            }
        }

        if ($generateDescriptionMetaTag || $generateKeywordsMetaTag) {
            $elements = $document->querySelectorAll('h1.bearcms-heading-element-large,h2.bearcms-heading-element-medium,h3.bearcms-heading-element-small,div.bearcms-text-element');
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

                $prepare = function ($content) {
                    $content = preg_replace('/<script.*?<\/script>/s', '', $content);
                    $content = preg_replace('/<.*?>/', ' $0 ', $content);
                    $content = preg_replace('/\s/u', ' ', $content);
                    $content = strip_tags($content);
                    while (strpos($content, '  ') !== false) {
                        $content = str_replace('  ', ' ', $content);
                    }
                    $content = html_entity_decode(trim($content));
                    return trim($content);
                };

                if ($generateDescriptionMetaTag) {
                    $descriptionContent = $prepare($descriptionContent);
                    $html .= '<meta name="description" content="' . htmlentities($substr($descriptionContent, 0, 200) . (strlen($descriptionContent) > 200 ? ' ...' : '')) . '"/>';
                }
                if ($generateKeywordsMetaTag) {
                    $wordsText = preg_replace("/[^[:alnum:][:space:]]/u", '', $strtolower($prepare($keywordsContent)));
                    $words = explode(' ', $wordsText);
                    $wordsCount = array_count_values($words);
                    arsort($wordsCount);
                    $selectedWords = [];
                    foreach ($wordsCount as $word => $wordCount) {
                        $wordLength = $strlen($word);
                        if ($wordLength >= 3 && !is_numeric($word)) {
                            $selectedWords[] = $word;
                            if (sizeof($selectedWords) === 10) {
                                break;
                            }
                        }
                    }
                    $html .= '<meta name="keywords" content="' . htmlentities(implode(', ', $selectedWords)) . '"/>';
                }
            }
        }

        if (!Config::$whitelabel) {
            $html .= '<meta name="generator" content="Bear CMS (powered by Bear Framework)"/>';
        }
        $icon = $settings->icon;
        if (isset($icon[0])) {
            $baseUrl = $this->app->urls->get();
            $html .= '<link rel="apple-touch-icon-precomposed" sizes="180x180" href="' . htmlentities($baseUrl . '-link-rel-icon-180') . '">';
            $html .= '<link rel="apple-touch-icon-precomposed" sizes="64x64" href="' . htmlentities($baseUrl . '-link-rel-icon-64') . '">';
            $html .= '<link rel="apple-touch-icon-precomposed" sizes="32x32" href="' . htmlentities($baseUrl . '-link-rel-icon-32') . '">';
            $html .= '<link rel="icon" sizes="192x192" href="' . htmlentities($baseUrl . '-link-rel-icon-192') . '">';
            $html .= '<link rel="icon" sizes="64x64" href="' . htmlentities($baseUrl . '-link-rel-icon-64') . '">';
            $html .= '<link rel="icon" sizes="32x32" href="' . htmlentities($baseUrl . '-link-rel-icon-32') . '">';
        } else if ($currentUserExists) {
            $html .= '<link rel="apple-touch-icon-precomposed" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
            $html .= '<link rel="icon" href="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">';
        }
        if (empty($settings->allowSearchEngines)) {
            $html .= '<meta name="robots" content="noindex">';
        }
        $url = rtrim($this->app->request->getURL(), '/') . '/';
        $url = explode('?', $url)[0]; // remove the query string
        $html .= '<link rel="canonical" href="' . htmlentities($url) . '"/>';
        if ($settings->enableRSS) {
            $languages = $settings->languages;
            if (empty($languages)) {
                $languages = [''];
            } else {
                $languages[0] = '';
            }
            foreach ($languages as $language) {
                $rssTitle = $settings->getTitle($language);
                $rssURL = $this->app->urls->get('/rss' . ($language === '' ? '' : '.' . $language) . '.xml');
                $html .= '<link rel="alternate" type="application/rss+xml" title="' . htmlentities(trim($rssTitle)) . '" href="' . htmlentities($rssURL) . '" />';
            }
        }
        $html .= '<meta property="og:image" content="' . htmlentities($url) . '-meta-og-image' . '?' . time() . '">';
        $html .= '<meta property="og:type" content="website">';
        $html .= '<meta property="og:url" content="' . htmlentities($url) . '">';
        $html .= '</head><body>';

        if ($response instanceof App\Response\HTML) { // is not temporary disabled
            $externalLinksAreEnabled = $settings->externalLinks;
            if ($externalLinksAreEnabled || $currentUserExists) {
                if ($currentUserExists) {
                    $html .= '<script src="' . htmlentities($this->context->assets->getURL('assets/externalLinks.min.js', ['cacheMaxAge' => 999999999, 'version' => 6])) . '" async onload="bearCMS.externalLinks.initialize(' . ($externalLinksAreEnabled ? 1 : 0) . ',' . ($currentUserExists ? 1 : 0) . ');"></script>';
                } else {
                    // taken from dev/externalLinksNoUser.min.js
                    $html .= '<script>for(var links=document.getElementsByTagName("a"),host=location.host,i=0;i<links.length;i++){var link=links[i],href=link.getAttribute("href");null===href||-1===href.indexOf("//")||-1!==href.indexOf("//"+host)||0===href.indexOf("#")||0===href.indexOf("javascript:")||null!==link.target&&""!==link.target||(link.target="_blank")};</script>';
                }
            }
        }
        $html .= '</body></html>';
        $htmlToInsert[] = ['source' => $html];
        if (Config::$allowRenderGlobalHTML && $response instanceof App\Response\HTML) {
            $globalHTML = $settings->globalHTML;
            if (isset($globalHTML[0]) && (!$currentUserExists || ($currentUserExists && !$this->app->request->query->exists('disable-global-html')))) {
                $htmlToInsert[] = ['source' => $globalHTML];
            }
        }
        $document->insertHTMLMulti($htmlToInsert);

        if (strlen($title) > 0) {
            $imageElements = $document->querySelectorAll('img');
            foreach ($imageElements as $imageElement) {
                if (strlen($imageElement->getAttribute('alt')) === 0) {
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
                    if (strlen($linkTarget) === 0) {
                        $linkElement->setAttribute('target', '_blank');
                    }
                }
            }
        }

        $response->content = $document->saveHTML();

        if ($this->app->currentUser->exists()) {
            $addUserBadge = true;
            $serverCookies = Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER);
            if (!empty($serverCookies['tmcs']) || !empty($serverCookies['tmpr']) || !empty($serverCookies['wspr'])) {
                $addUserBadge = false;
            }
            if ($addUserBadge) {
                $this->app->users->applyUI($response);
            }
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

        $response->headers->set($response->headers->make('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0'));

        $settings = $this->data->settings->get();

        $serverCookies = Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER);

        if (!empty($serverCookies['tmcs']) || !empty($serverCookies['tmpr']) || !empty($serverCookies['wspr'])) {
            Internal\ElementsHelper::$editorData = [];
        }

        $requestArguments = [];
        $requestArguments['hasEditableElements'] = empty(Internal\ElementsHelper::$editorData) ? '0' : '1';
        $requestArguments['hasEditableContainers'] = '0';
        $requestArguments['isDisabled'] = $settings->disabled ? '1' : '0';
        foreach (Internal\ElementsHelper::$editorData as $itemData) {
            if ($itemData[0] === 'container') {
                $requestArguments['hasEditableContainers'] = '1';
            }
        }

        $cacheKey = json_encode([
            'adminUI',
            $this->app->request->base,
            $this->currentUser->getSessionKey(),
            $this->currentUser->getPermissions(),
            get_class_vars('\BearCMS\Internal\Config'),
            $serverCookies
        ]);

        $adminUIData = Internal\Server::call('adminui', $requestArguments, true, $cacheKey);
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
                    $content = str_replace('{jsonEncodedBody}', json_encode($this->app->clientPackages->process($this->app->components->process($response->content))), $content);
                }
                $document->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $elementsHtml = Internal\ElementsHelper::getEditableElementsHtml();
                if (isset($elementsHtml[0])) {
                    $htmlToInsert[] = ['source' => $elementsHtml];
                }
                if (!empty(Internal\ElementsHelper::$editorData)) {
                    $app = App::get();
                    $context = $app->contexts->get(__DIR__);
                    $htmlToInsert[] = ['source' => '<html><head><script src="' . $context->assets->getURL('assets/elementsEditor.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]) . '"></head></html>'];
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
                    $template = new \BearFramework\HTMLTemplate($templateContent);
                    if ($currentCustomizations !== null) {
                        $html = $currentCustomizations->getHTML();
                        if (isset($html[0])) {
                            $template->insert($html);
                        }
                    }
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
