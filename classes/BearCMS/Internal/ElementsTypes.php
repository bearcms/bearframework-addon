<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal2;
use BearCMS\Internal\Data\UploadsSize;
use IvoPetkov\HTML5DOMDocument;

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsTypes
{

    /**
     * 
     * @var string|null
     */
    static private $contextDir = null;

    /**
     * 
     * @param string $typeCode
     * @param array $options
     * @return void
     */
    public static function add(string $typeCode, array $options = []): void
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->contexts->get(__DIR__);
            self::$contextDir = $context->dir;
        }
        $name = $options['componentSrc'];
        $app->components
            ->addAlias($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php')
            ->addTag($name, 'file:' . self::$contextDir . '/components/bearcmsElement.php');
        Internal\ElementsHelper::$elementsTypesCodes[$name] = $typeCode;
        Internal\ElementsHelper::$elementsTypesFilenames[$name] = $options['componentFilename'];
        Internal\ElementsHelper::$elementsTypesOptions[$name] = $options;
    }

    /**
     * 
     * @return void
     */
    public static function addDefault(): void
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->contexts->get(__DIR__);
            self::$contextDir = $context->dir;
        }

        $hasElements = Config::hasFeature('ELEMENTS');
        $hasThemes = Config::hasFeature('THEMES');

        if ($hasElements || Config::hasFeature('ELEMENTS_HEADING')) {
            self::add('heading', [
                'componentSrc' => 'bearcms-heading-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsHeadingElement.php',
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
                                ["rule", $parentSelector . " .bearcms-heading-element-medium", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["rule", $parentSelector . " .bearcms-heading-element-small", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
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
                                ["rule", $parentSelector . " .bearcms-heading-element-medium", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-heading-element-medium"]
                            ],
                            "elementType" => "heading"
                        ]);

                        $groupSmall = $group->addGroup(__("bearcms.themes.options.Small"));
                        $groupSmall->addOption($idPrefix . "HeadingSmallCSS", "css", '', [
                            "cssOutput" => [
                                ["rule", $parentSelector . " .bearcms-heading-element-small", "box-sizing:border-box;font-weight:normal;padding:0;margin:0;"],
                                ["selector", $parentSelector . $customStyleSelector . " .bearcms-heading-element-small"]
                            ],
                            "elementType" => "heading"
                        ]);
                    }
                };
            }
        }
        if ($hasElements || Config::hasFeature('ELEMENTS_TEXT')) {
            self::add('text', [
                'componentSrc' => 'bearcms-text-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsTextElement.php',
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
            self::add('link', [
                'componentSrc' => 'bearcms-link-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsLinkElement.php',
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
            self::add('image', [
                'componentSrc' => 'bearcms-image-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsImageElement.php',
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
                        'id' => 'fileWidth',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'fileHeight',
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'width', // Deprecated on 14 August 2021
                        'type' => 'textbox'
                    ],
                    [
                        'id' => 'align', // Deprecated on 14 August 2021
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
                'onDelete' => function ($data) use ($app) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        $dataKey = Internal\Data::filenameToDataKey($filename);
                        $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                        UploadsSize::remove($dataKey);
                    }
                },
                'onDuplicate' => function ($data) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        $newFilename = Internal\Data::generateNewFilename($filename);
                        copy($filename, $newFilename);
                        UploadsSize::add(Internal\Data::filenameToDataKey($newFilename), filesize($newFilename));
                        $data['filename'] = $newFilename;
                    }
                    return $data;
                },
                'getUploadsSizeItems' => function ($data) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        return [Internal\Data::filenameToDataKey($filename)];
                    }
                    return [];
                },
                'optimizeData' => function ($data) {
                    $app = App::get();
                    $hasChange = false;
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        if (strpos($filename, 'appdata://') === 0) {
                            if ($filename !== $data['filename']) {
                                $data['filename'] = $filename;
                                $hasChange = true;
                            }
                            if (!isset($data['fileWidth']) || !isset($data['fileHeight'])) {
                                $details = $app->assets->getDetails($filename, ['width', 'height']);
                                $data['fileWidth'] = $details['width'] !== null ? $details['width'] : 0;
                                $data['fileHeight'] = $details['height'] !== null ? $details['height'] : 0;
                                $hasChange = true;
                            }
                        }
                    }
                    if ($hasChange) {
                        return $data;
                    }
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
            self::add('imageGallery', [
                'componentSrc' => 'bearcms-image-gallery-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsImageGalleryElement.php',
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
                                $innerHTML .= '<file filename="' . htmlentities($file['filename']) . '" fileWidth="' . (isset($file['width']) ? htmlentities($file['width']) : null) . '" fileHeight="' . (isset($file['height']) ? htmlentities($file['height']) : null) . '"/>';
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
                        $file = ['filename' => $fileElement->getAttribute('filename')];
                        $width = (string)$fileElement->getAttribute('filewidth');
                        if (isset($width[0])) {
                            $file['width'] = $width[0];
                        }
                        $height = (string)$fileElement->getAttribute('fileheight');
                        if (isset($height[0])) {
                            $file['height'] = $height[0];
                        }
                        $files[] = $file;
                    }
                    $data['files'] = $files;
                    return $data;
                },
                'onDelete' => function ($data) use ($app) {
                    if (isset($data['files']) && is_array($data['files'])) {
                        foreach ($data['files'] as $index => $file) {
                            if (isset($file['filename'])) {
                                $filename = (string)$file['filename'];
                                if (strlen($filename) > 0) {
                                    $filename = Internal2::$data2->fixFilename($filename);
                                    $dataKey = Internal\Data::filenameToDataKey($filename);
                                    $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                                    UploadsSize::remove($dataKey);
                                }
                            }
                        }
                    }
                },
                'onDuplicate' => function ($data) {
                    if (isset($data['files']) && is_array($data['files'])) {
                        foreach ($data['files'] as $index => $file) {
                            if (isset($file['filename'])) {
                                $filename = (string)$file['filename'];
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
                'getUploadsSizeItems' => function ($data) {
                    $result = [];
                    if (isset($data['files']) && is_array($data['files'])) {
                        foreach ($data['files'] as $index => $file) {
                            if (isset($file['filename'])) {
                                $filename = (string)$file['filename'];
                                if (strlen($filename) > 0) {
                                    $filename = Internal2::$data2->fixFilename($filename);
                                    $result[] = Internal\Data::filenameToDataKey($filename);
                                }
                            }
                        }
                    }
                    return $result;
                },
                'optimizeData' => function ($data) {
                    $app = App::get();
                    $hasChange = false;
                    if (isset($data['files']) && is_array($data['files'])) {
                        foreach ($data['files'] as $index => $file) {
                            if (isset($file['filename'])) {
                                $filename = (string)$file['filename'];
                                if (strlen($filename) > 0) {
                                    $filename = Internal2::$data2->fixFilename($filename);
                                    if (strpos($filename, 'appdata://') === 0) {
                                        if ($filename !== $file['filename']) {
                                            $file['filename'] = $filename;
                                            $hasChange = true;
                                        }
                                        if (!isset($file['width']) || !isset($file['height'])) {
                                            $details = $app->assets->getDetails($filename, ['width', 'height']);
                                            $file['width'] = $details['width'] !== null ? $details['width'] : 0;
                                            $file['height'] = $details['height'] !== null ? $details['height'] : 0;
                                            $hasChange = true;
                                        }
                                        $data['files'][$index] = $file;
                                    }
                                }
                            }
                        }
                    }
                    if ($hasChange) {
                        return $data;
                    }
                },
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
            self::add('video', [
                'componentSrc' => 'bearcms-video-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsVideoElement.php',
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
                'onDelete' => function ($data) use ($app) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        $dataKey = Internal\Data::filenameToDataKey($filename);
                        $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                        UploadsSize::remove($dataKey);
                    }
                },
                'onDuplicate' => function ($data) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        $newFilename = Internal\Data::generateNewFilename($filename);
                        copy($filename, $newFilename);
                        UploadsSize::add(Internal\Data::filenameToDataKey($newFilename), filesize($newFilename));
                        $data['filename'] = $newFilename;
                    }
                    return $data;
                },
                'getUploadsSizeItems' => function ($data) {
                    $filename = isset($data['filename']) ? (string)$data['filename'] : '';
                    if (strlen($filename) > 0) {
                        $filename = Internal2::$data2->fixFilename($filename);
                        return [Internal\Data::filenameToDataKey($filename)];
                    }
                    return [];
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
            self::add('navigation', [
                'componentSrc' => 'bearcms-navigation-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsNavigationElement.php',
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
                        'id' => 'showSearchButton',
                        'type' => 'checkbox'
                    ],
                    [
                        'id' => 'showStoreCartButton',
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
            self::add('html', [
                'componentSrc' => 'bearcms-html-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsHtmlElement.php',
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
            self::add('blogPosts', [
                'componentSrc' => 'bearcms-blog-posts-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsBlogPostsElement.php',
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

                    $groupBlogPosts->addOption($idPrefix . "BlogPostsSpacing", "htmlUnit", __("bearcms.themes.options.Posts spacing"), [
                        "defaultValue" => "0",
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post:not(:first-child)", "margin-top:{value};"]
                        ],
                        "onHighlight" => [['cssSelector', $parentSelector . " .bearcms-blog-posts-element-post"]]
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
            self::add('comments', [
                'componentSrc' => 'bearcms-comments-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsCommentsElement.php',
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
                    $app = App::get();
                    if (isset($data['threadID'])) {
                        $app->data->delete('bearcms/comments/thread/' . md5($data['threadID']) . '.json');
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
            self::add('separator', [
                'componentSrc' => 'bearcms-separator-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsSeparatorElement.php',
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
                    throw new \Exception('Not supported in other contexts!');
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
                    throw new \Exception('Not supported in other contexts!');
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
                    throw new \Exception('Not supported in other contexts!');
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
        if ($hasElements || Config::hasFeature('ELEMENTS_CANVAS')) {
            self::add('canvas', [
                'componentSrc' => 'bearcms-canvas-element',
                'componentFilename' => self::$contextDir . '/components/bearcmsCanvasElement.php',
                'fields' => [
                    [
                        'id' => 'value',
                        'type' => 'textbox'
                    ]
                ],
                'onDelete' => function ($data) use ($app) {
                    if (isset($data['value'])) {
                        $files = CanvasElementHelper::getFilesInValue((string)$data['value']);
                        foreach ($files as $filename) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            $dataKey = Internal\Data::filenameToDataKey($filename);
                            if ($app->data->exists($dataKey)) {
                                $app->data->rename($dataKey, '.recyclebin/' . $dataKey . '-' . str_replace('.', '-', microtime(true)));
                            }
                            UploadsSize::remove($dataKey);
                        }
                    }
                },
                'onDuplicate' => function ($data) {
                    if (isset($data['value'])) {
                        $value = (string)$data['value'];
                        $files = CanvasElementHelper::getFilesInValue($value);
                        $filesToUpdate = [];
                        foreach ($files as $filename) {
                            $oldFilename = Internal2::$data2->fixFilename($filename);
                            $newFilename = Internal\Data::generateNewFilename($oldFilename);
                            copy($oldFilename, $newFilename);
                            $newFilenameDataKey = Internal\Data::filenameToDataKey($newFilename);
                            UploadsSize::add($newFilenameDataKey, filesize($newFilename));
                            $filesToUpdate[$filename] = 'data:' . $newFilenameDataKey;
                        }
                        if (!empty($filesToUpdate)) {
                            $data['value'] = CanvasElementHelper::updateFilesInValue($value, $filesToUpdate);
                        }
                    }
                    return $data;
                },
                'getUploadsSizeItems' => function ($data) {
                    $result = [];
                    if (isset($data['value'])) {
                        $files = CanvasElementHelper::getFilesInValue((string)$data['value']);
                        foreach ($files as $filename) {
                            $filename = Internal2::$data2->fixFilename($filename);
                            $result[] = Internal\Data::filenameToDataKey($filename);
                        }
                    }
                    return $result;
                }
            ]);
        }
    }
}
