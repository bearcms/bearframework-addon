<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\ElementsHelper;
use IvoPetkov\HTML5DOMDocument;

/**
 * Contains information about the available elements types
 */
class ElementsTypes
{

    static private $contextDir = null;

    public static function add(string $typeCode, array $options = [])
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->context->get(__FILE__);
            self::$contextDir = $context->dir;
        }
        $contextDir = self::$contextDir;
        $app->components->addAlias($options['componentSrc'], 'file:' . $contextDir . '/components/bearcmsElement.php');
        ElementsHelper::$elementsTypesCodes[$options['componentSrc']] = $typeCode;
        ElementsHelper::$elementsTypesFilenames[$options['componentSrc']] = $options['componentFilename'];
        ElementsHelper::$elementsTypesOptions[$options['componentSrc']] = $options;
    }

    public static function addDefault()
    {
        $app = App::get();
        if (self::$contextDir === null) {
            $context = $app->context->get(__FILE__);
            self::$contextDir = $context->dir;
        }
        $contextDir = self::$contextDir;
        self::add('heading', [
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
        ]);
        self::add('text', [
            'componentSrc' => 'bearcms-text-element',
            'componentFilename' => $contextDir . '/components/bearcmsTextElement.php',
            'fields' => [
                [
                    'id' => 'text',
                    'type' => 'textbox'
                ]
            ]
        ]);
        self::add('link', [
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
        ]);
        self::add('video', [
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
        ]);
        self::add('image', [
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
        ]);
        self::add('imageGallery', [
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
        ]);
        self::add('navigation', [
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
        ]);
        self::add('html', [
            'componentSrc' => 'bearcms-html-element',
            'componentFilename' => $contextDir . '/components/bearcmsHtmlElement.php',
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
        self::add('blogPosts', [
            'componentSrc' => 'bearcms-blog-posts-element',
            'componentFilename' => $contextDir . '/components/bearcmsBlogPostsElement.php',
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
        self::add('comments', [
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
        ]);
        self::add('contactForm', [
            'componentSrc' => 'bearcms-contact-form-element',
            'componentFilename' => $contextDir . '/components/bearcmsContactFormElement.php',
            'fields' => [
                [
                    'id' => 'email',
                    'type' => 'textbox'
                ]
            ]
        ]);
        self::add('forumPosts', [
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

}
