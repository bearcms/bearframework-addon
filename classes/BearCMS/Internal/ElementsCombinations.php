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

/**
 * @internal
 * @codeCoverageIgnore
 */
class ElementsCombinations
{

    /**
     * 
     * @var array
     */
    static private $data = [];

    /**
     * 
     * @param string $id
     * @param callable $callback
     * @return void
     */
    public static function register(string $id, callable $callback): void
    {
        self::$data[$id] = $callback;
    }

    /**
     * 
     * @return array
     */
    public static function getList(): array
    {
        $result = [];
        foreach (self::$data as $id => $callback) {
            $result[] = self::getData($id, true);
        }
        return $result;
    }

    /**
     * 
     * @param string $id
     * @return array|null
     */
    public static function get(string $id): ?array
    {
        if (isset(self::$data[$id])) {
            return self::getData($id, true);
        }
        return null;
    }

    /**
     * 
     * @param string $id
     * @param boolean $updateMediaFilenames
     * @return array
     */
    private static function getData(string $id, bool $updateMediaFilenames): array
    {
        $app = App::get();
        $context = $app->contexts->get(__DIR__);
        $result = call_user_func(self::$data[$id]);
        $result['id'] = $id;
        if ($updateMediaFilenames && isset($result['media'])) {
            if (isset($result['media']) && is_array($result['media'])) {
                foreach ($result['media'] as $i => $mediaItem) {
                    if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                        $result['media'][$i]['filename'] = $context->dir . '/assets/ec/' . md5($id) . '/' . md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $filename
     * @return string|null
     */
    public static function getOriginalMediaFilename(string $filename): ?string
    {
        $app = App::get();
        $context = $app->contexts->get(__DIR__);
        $matchingDir = $context->dir . '/assets/ec/';
        $pathParts = explode('/', substr($filename, strlen($matchingDir)), 2);
        if (isset($pathParts[0], $pathParts[1])) {
            $combinationIDMD5 = $pathParts[0];
            $mediaFilenameMD5 = $pathParts[1];
            foreach (self::$data as $id => $callback) {
                if ($combinationIDMD5 === md5($id)) {
                    $combinationData = self::getData($id, false);
                    if (isset($combinationData['media']) && is_array($combinationData['media'])) {
                        foreach ($combinationData['media'] as $mediaItem) {
                            if (is_array($mediaItem) && isset($mediaItem['filename']) && is_string($mediaItem['filename'])) {
                                if ($mediaFilenameMD5 === md5($mediaItem['filename']) . '.' . pathinfo($mediaItem['filename'], PATHINFO_EXTENSION)) {
                                    return $mediaItem['filename'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return void
     */
    public static function addDefault(): void
    {
        self::register('bearcms-social-buttons-1', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'socialButtons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-social-buttons-1a.png',
                        'width' => 2000,
                        'height' => 800,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Facebook',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#355B9C","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#325795","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#3C63A5","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-facebook.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Instagram',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#E50572","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#D40269","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#F00075","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-instagram.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'YouTube',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#ed0000","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#d90000","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#ff0000","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-youtube.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Twitter',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#02A8D3","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#01A0C9","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#00B3E1","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-twitter.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'LinkedIn',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#0071AC","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#016AA1","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#0077B5","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-linkedin.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Email',
                            ],
                            'style' => [
                                'LinkCSS' => '{"border-top-left-radius":"3px","border-top-right-radius":"3px","border-bottom-left-radius":"3px","border-bottom-right-radius":"3px","font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#292828","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#252525","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#333333","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-email.svg)","background-position":"center center","background-repeat":"repeat","background-attachment":"scroll","background-size":"contain","min-width":"42px","min-height":"42px"}',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'row',
                        'rowAlignment' => 'center',
                        'autoVerticalWidth' => '300px',
                        'elementsSpacing' => '5px',
                    ],
                ]
            ];
        });
        self::register('bearcms-social-buttons-2', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'socialButtons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-social-buttons-2a.png',
                        'width' => 2000,
                        'height' => 800,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Facebook',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#355B9C","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#325795","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#3C63A5","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-facebook.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Instagram',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#E50572","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#D40269","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#F00075","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-instagram.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'YouTube',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#ed0000","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#d90000","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#ff0000","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-youtube.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Twitter',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#02A8D3","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#01A0C9","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#00B3E1","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-twitter.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'LinkedIn',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#0071AC","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#016AA1","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#0077B5","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-linkedin.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Email',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","background-color:hover":"#292828","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#252525","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-color":"#333333","background-image":"url(' . $context->dir . '/assets/elements/icon-ffffff-email.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'row',
                        'rowAlignment' => 'center',
                        'autoVerticalWidth' => '300px',
                        'elementsSpacing' => '5px',
                    ],
                ]
            ];
        });
        self::register('bearcms-social-buttons-3', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'socialButtons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-social-buttons-3a.png',
                        'width' => 2000,
                        'height' => 800,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Facebook',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-facebook.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Instagram',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-instagram.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'YouTube',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-youtube.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Twitter',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-twitter.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'LinkedIn',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-linkedin.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Email',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-color":"#EEEEEE","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-email.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px"}',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'row',
                        'rowAlignment' => 'center',
                        'autoVerticalWidth' => '300px',
                        'elementsSpacing' => '0px',
                    ],
                ]
            ];
        });
        self::register('bearcms-social-buttons-4', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'socialButtons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-social-buttons-4a.png',
                        'width' => 2000,
                        'height' => 800,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Facebook',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-facebook.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#3C63A5","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-facebook.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#355B9C","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-facebook.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Instagram',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-instagram.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#F00075","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-instagram.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#E50572","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-instagram.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'YouTube',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-youtube.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#ff0000","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-youtube.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#ed0000","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-youtube.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Twitter',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-twitter.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#00B3E1","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-twitter.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#02A8D3","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-twitter.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'LinkedIn',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-linkedin.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#0077B5","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-linkedin.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#0071AC","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-linkedin.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'data' => [
                                'url' => '#',
                                'text' => '',
                                'title' => 'Email',
                            ],
                            'style' => [
                                'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","border-top-left-radius":"21px","border-top-right-radius":"21px","border-bottom-left-radius":"21px","border-bottom-right-radius":"21px","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-email.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"28px 28px","background-color:hover":"#333333","background-image:hover":"url(' . $context->dir . '/assets/elements/icon-ffffff-email.svg)","background-position:hover":"center center","background-repeat:hover":"no-repeat","background-attachment:hover":"scroll","background-size:hover":"28px 28px","background-color:active":"#292828","background-image:active":"url(' . $context->dir . '/assets/elements/icon-ffffff-email.svg)","background-position:active":"center center","background-repeat:active":"no-repeat","background-attachment:active":"scroll","background-size:active":"28px 28px"}',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'row',
                        'rowAlignment' => 'center',
                        'autoVerticalWidth' => '300px',
                        'elementsSpacing' => '5px',
                    ],
                ]
            ];
        });
        self::register('bearcms-text-1', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-1.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'columns',
                    'elements' => [
                        0 => [
                            [
                                'type' => 'image',
                                'data' => [
                                    'filename' => $context->dir . '/assets/elements/placeholder-1400x1400.png',
                                    'onClick' => 'fullscreen'
                                ],
                                'style' => [
                                    'ImageCSS' => '{"border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%"}',
                                    'elementContainerCSS' => '{"max-width":"150px"}',
                                ],
                            ],
                        ],
                        1 => [
                            [
                                'type' => 'flexibleBox',
                                'elements' => [
                                    [
                                        'type' => 'heading',
                                        'data' => [
                                            'text' => 'Duis viverra tempor sollicitudin',
                                            'size' => 'large',
                                        ],
                                    ],
                                    [
                                        'type' => 'text',
                                        'data' => [
                                            'text' => 'Nullam at odio erat. Sed ultrices aliquam neque vitae vulputate. Vivamus fringilla euismod sem, id tristique sem vulputate eu. Ut pellentesque, nisi a efficitur rhoncus, ipsum est scelerisque quam, interdum fringilla libero nulla eu nulla. Proin rhoncus, enim ut venenatis pulvinar, turpis odio lacinia leo, ut rhoncus sem velit ut orci.',
                                        ],
                                    ],
                                ],
                                'style' => [
                                    'direction' => 'column',
                                    'rowAlignment' => 'left',
                                    'autoVerticalWidth' => '500px',
                                    'elementsSpacing' => '15px',
                                ],
                            ],
                        ],
                    ],
                    'style' => [
                        'widths' => '150px,',
                        'autoVerticalWidth' => '500px',
                        'elementsSpacing' => '35px',
                    ],
                ]
            ];
        });
        self::register('bearcms-text-2', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-2.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'filename' => $context->dir . '/assets/elements/placeholder-1400x1400.png',
                                        'onClick' => 'fullscreen',
                                    ],
                                    'style' => [
                                        'ImageCSS' => '{"border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%"}',
                                        'elementContainerCSS' => '{"width":"150px"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'data' => [
                                'text' => '<div align="center"><b>Duis molestie dignissim justo quis dignissim.</b></div><div align="center">Ut molestie dui purus. Cras non nisi magna. Sed eget malesuada ex. Curabitur condimentum aliquet arcu, fermentum sollicitudin lectus gravida nec. Suspendisse tempus mauris ultrices augue egestas, non sodales turpis vestibulum.</div>',
                            ],
                        ],
                    ],
                    'style' => [],
                ],
            ];
        });
        self::register('bearcms-text-3', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-3.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'filename' => $context->dir . '/assets/elements/placeholder-1400x1400.png',
                                        'onClick' => 'fullscreen',
                                    ],
                                    'style' => [
                                        'ImageCSS' => '{"border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%"}',
                                        'elementContainerCSS' => '{"width":"150px"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                0 => [
                                    'type' => 'heading',
                                    'data' => [
                                        'text' => 'Anna Quisque',
                                        'size' => 'medium',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'data' => [
                                'text' => '<div align="center">Cras sit amet nibh consectetur, iaculis ligula sed, iaculis massa. Nam sagittis metus eu iaculis suscipit. Integer mollis condimentum mauris vitae molestie.</div>',
                            ],
                        ],
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'Facebook',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-facebook.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'Instagram',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-instagram.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'YouTube',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-youtube.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'Twitter',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-twitter.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'LinkedIn',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-linkedin.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => '',
                                        'title' => 'Email',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"font-family":"Arial","color":"#111111","text-decoration":"underline","min-width":"42px","min-height":"42px","background-color:hover":"#E5E5E5","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","background-image":"url(' . $context->dir . '/assets/elements/icon-000000-email.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"24px 24px"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => '300px',
                                'elementsSpacing' => '0px',
                            ],
                        ],
                    ]
                ],
            ];
        });
        self::register('bearcms-text-4', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-4.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'text' => 'Maecenas rhoncus ante ac metus feugiat',
                                        'size' => 'large',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'data' => [
                                'text' => '<div align="center">Donec eget venenatis erat, nec auctor massa. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia cura</div>',
                            ],
                        ],
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                0 => [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => 'Button'
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"padding-top":"8px","padding-right":"15px","padding-bottom":"8px","padding-left":"15px","font-family":"Arial","color":"#111111","font-size":"16px","line-height":"180%","border-top-left-radius":"4px","border-top-right-radius":"4px","border-bottom-left-radius":"4px","border-bottom-right-radius":"4px","border-top":"2px solid #555555","border-right":"2px solid #555555","border-bottom":"2px solid #555555","border-left":"2px solid #555555","background-color:hover":"#555555","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#333333","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","border-top:active":"2px solid #333333","border-right:active":"2px solid #333333","border-bottom:active":"2px solid #333333","border-left:active":"2px solid #333333","color:hover":"#FFFFFF","color:active":"#FFFFFF"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                    ]
                ]
            ];
        });
        self::register('bearcms-text-5', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-5.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'text' => 'Etiam semper pharetra metus',
                                        'size' => 'large',
                                    ],
                                    'style' => [
                                        'HeadingCSS' => '{"font-family":"googlefonts:Pacifico","color":"#FFFFFF","font-size":"35px","line-height":"150%"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'data' => [
                                'text' => '<div align="center">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Praesent scelerisque, nunc a tempor mattis, ipsum dui volutpat orci, dignissim dapibus odio felis luctus nulla. Vivamus venenatis diam a quam tincidunt lacinia. Nam in hendrerit ex, eget suscipit sapien. Nulla vulputate lectus volutpat ipsum ornare laoreet.</div>',
                            ],
                            'style' => [
                                'TextCSS' => '{"font-family":"Arial","color":"#FFFFFF","font-size":"16px","line-height":"180%"}',
                                'TextLinkCSS' => '{"color":"#FFFFFF","font-family":"Arial","font-size":"16px","line-height":"180%","text-decoration":"underline"}',
                            ],
                        ],
                        2 => [
                            'type' => 'flexibleBox',
                            'elements' => [
                                [
                                    'type' => 'link',
                                    'data' => [
                                        'url' => '#',
                                        'text' => 'Button',
                                    ],
                                    'style' => [
                                        'LinkCSS' => '{"border-top-left-radius":"5px","border-top-right-radius":"5px","border-bottom-left-radius":"5px","border-bottom-right-radius":"5px","padding-top":"8px","padding-right":"15px","padding-bottom":"8px","padding-left":"15px","margin-top":"10px","border-top":"2px solid rgba(255,255,255,0.8)","border-right":"2px solid rgba(255,255,255,0.8)","border-bottom":"2px solid rgba(255,255,255,0.8)","border-left":"2px solid rgba(255,255,255,0.8)","background-color:hover":"rgba(255,255,255,0.2)","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"rgba(255,255,255,0.4)","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","font-family":"Arial","color":"#FFFFFF","font-size":"16px","line-height":"180%"}',
                                    ],
                                ],
                            ],
                            'style' => [
                                'direction' => 'row',
                                'rowAlignment' => 'center',
                                'autoVerticalWidth' => 'none',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'column',
                        'rowAlignment' => 'left',
                        'autoVerticalWidth' => '500px',
                        'css' => '{"padding-top":"40px","padding-right":"20px","padding-bottom":"40px","padding-left":"20px","border-top-left-radius":"12px","border-top-right-radius":"12px","border-bottom-left-radius":"12px","border-bottom-right-radius":"12px","background-color":"#76B813","background-image":"","background-position":"","background-repeat":"","background-attachment":"","background-size":""}',
                    ],
                ]
            ];
        });
        self::register('bearcms-text-6', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-6.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'text',
                            'data' => [
                                'text' => 'Integer quis justo lectus. Donec et ex a erat luctus interdum. Nunc et porta mi, quis maximus nisi. Vivamus vestibulum ultrices diam, a auctor erat condimentum at. Quisque quis fermentum turpis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere.',
                            ],
                        ],
                        [
                            'type' => 'columns',
                            'elements' => [
                                0 => [
                                    [
                                        'type' => 'image',
                                        'data' => [
                                            'filename' => $context->dir . '/assets/elements/placeholder-1400x1400.png',
                                            'onClick' => 'fullscreen'
                                        ],
                                        'style' => [
                                            'ImageCSS' => '{"border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%"}',
                                            'elementContainerCSS' => '{"max-width":"90px"}',
                                        ],
                                    ],
                                ],
                                1 => [
                                    [
                                        'type' => 'flexibleBox',
                                        'elements' => [
                                            [
                                                'type' => 'text',
                                                'data' => [
                                                    'text' => '<div><b>Fusce viverra</b></div><div>Sed ultrices aliquam neque vitae vulputate</div>',
                                                ],
                                            ],
                                        ],
                                        'style' => [
                                            'direction' => 'column',
                                            'rowAlignment' => 'left',
                                            'autoVerticalWidth' => '500px',
                                            'css' => '{"padding-top":"15px"}',
                                        ],
                                    ],
                                ],
                            ],
                            'style' => [
                                'widths' => '90px,',
                                'autoVerticalWidth' => '500px',
                                'elementsSpacing' => '25px',
                            ],
                        ],
                    ]
                ]
            ];
        });
        self::register('bearcms-text-7', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-7.jpg',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'columns',
                    'elements' => [
                        0 => [
                            [
                                'type' => 'image',
                                'data' => [
                                    'filename' => $context->dir . '/assets/elements/placeholder-1600x2000.png',
                                ],
                            ],
                        ],
                        1 => [
                            [
                                'type' => 'heading',
                                'data' => [
                                    'text' => 'Phasellus vitae viverra elit',
                                    'size' => 'large',
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'text' => '<div>Donec lobortis, lectus tristique fermentum porttitor, purus magna interdum nisl, a semper lorem magna id velit. Quisque scelerisque ut neque ut interdum. Vestibulum non libero ac purus eleifend scelerisque.</div>',
                                ],
                            ]
                        ],
                    ]
                ]
            ];
        });
        self::register('bearcms-text-8', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-8.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'flexibleBox',
                    'elements' => [
                        [
                            'type' => 'columns',
                            'elements' => [
                                0 => [
                                    [
                                        'type' => 'image',
                                        'data' => [
                                            'filename' => $context->dir . '/assets/elements/placeholder-1600x2000.png',
                                        ],
                                    ],
                                ],
                                1 => [
                                    [
                                        'type' => 'heading',
                                        'data' => [
                                            'text' => 'Etiam fermentum ipsum sed',
                                            'size' => 'medium',
                                        ],
                                    ],
                                    [
                                        'type' => 'text',
                                        'data' => [
                                            'text' => 'Aliquam consectetur interdum risus. Nullam dignissim volutpat felis, vel elementum lectus facilisis, at faucibus lacus vehicula.',
                                        ],
                                    ],
                                    [
                                        'type' => 'link',
                                        'data' => [
                                            'url' => '#',
                                            'text' => 'Button'
                                        ],
                                        'style' => [
                                            'LinkCSS' => '{"padding-top":"8px","padding-right":"18px","padding-bottom":"8px","padding-left":"18px","border-top-left-radius":"5px","border-top-right-radius":"5px","border-bottom-left-radius":"5px","border-bottom-right-radius":"5px","border-top":"2px solid #777777","border-right":"2px solid #777777","border-bottom":"2px solid #777777","border-left":"2px solid #777777","background-color:hover":"rgba(255,255,255,0.5)","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"rgba(255,255,255,0.2)","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","font-family":"Arial","color":"#111111","font-size":"16px","line-height":"180%"}',
                                        ],
                                    ],
                                ],
                            ],
                            'style' => [
                                'widths' => '200px,',
                                'autoVerticalWidth' => '500px',
                                'elementsSpacing' => '20px',
                            ],
                        ],
                    ],
                    'style' => [
                        'direction' => 'column',
                        'rowAlignment' => 'left',
                        'autoVerticalWidth' => '500px',
                        'css' => '{"background-color":"#EEEEEE","background-image":"","background-position":"","background-repeat":"","background-attachment":"","background-size":"","border-top-left-radius":"10px","border-top-right-radius":"10px","border-bottom-left-radius":"10px","border-bottom-right-radius":"10px","padding-top":"20px","padding-right":"20px","padding-bottom":"20px","padding-left":"20px"}',
                    ],
                ]
            ];
        });
        self::register('bearcms-text-9', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'text',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-text-9.png',
                        'width' => 2000,
                        'height' => 1100,
                    ]
                ],
                'element' => [
                    'type' => 'floatingBox',
                    'elements' => [
                        'inside' => [
                            [
                                'type' => 'image',
                                'data' => [
                                    'filename' => $context->dir . '/assets/elements/placeholder-1400x1400.png',
                                    'onClick' => 'fullscreen'
                                ],
                            ],
                        ],
                        'outside' => [
                            [
                                'type' => 'text',
                                'data' => [
                                    'text' => 'Duis feugiat ex a semper dictum. Nullam quis dolor dignissim, blandit mauris ac, ultrices velit. Ut quis efficitur mauris, consectetur ornare tortor. Sed bibendum faucibus facilisis. Duis turpis erat, varius non cursus sed, sodales sit amet libero. Pellentesque cursus et ex pulvinar commodo. Praesent vehicula vel justo accumsan eleifend. Quisque cursus lacus in dui mattis fringilla. Mauris dictum orci scelerisque consequat ullamcorper. Donec eget venenatis erat, nec auctor massa. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.',
                                ],
                            ],
                        ],
                    ],
                    'style' => [
                        'position' => 'left',
                        'width' => '150px',
                        'autoVerticalWidth' => '500px',
                    ],
                ],
            ];
        });
        self::register('bearcms-button-1', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'buttons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-button-1.png',
                        'width' => 2000,
                        'height' => 650,
                    ]
                ],
                'element' => [
                    'type' => 'link',
                    'data' => [
                        'url' => '#',
                        'text' => 'Click me'
                    ],
                    'style' => [
                        'LinkCSS' => '{"font-family":"Arial","color":"#FFFFFF","font-size":"16px","line-height":"180%","border-top-left-radius":"5px","border-top-right-radius":"5px","border-bottom-left-radius":"5px","border-bottom-right-radius":"5px","background-color:hover":"#05AFC4","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#02A3B7","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","padding-top":"12px","padding-right":"44px","padding-bottom":"12px","padding-left":"18px","background-color":"#08BBD1","background-image":"url(' . $context->dir . '/assets/elements/arrow-ffffff-right.svg)","background-position":"right 8px center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"30px 30px"}',
                    ],
                ]
            ];
        });
        self::register('bearcms-button-2', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'buttons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-button-2.png',
                        'width' => 2000,
                        'height' => 650,
                    ]
                ],
                'element' => [
                    'type' => 'link',
                    'data' => [
                        'url' => '#',
                        'text' => 'Click me'
                    ],
                    'style' => [
                        'LinkCSS' => '{"border-top":"2px solid #680695","border-right":"2px solid #680695","border-bottom":"2px solid #680695","border-left":"2px solid #680695","font-family":"Georgia","color":"#500473","font-size":"16px","font-weight":"bold","line-height":"180%","padding-top":"10px","padding-right":"18px","padding-bottom":"10px","padding-left":"18px","background-color:hover":"#EEEEEE","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#DDDDDD","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","border-top-left-radius":"6px","border-top-right-radius":"6px","border-bottom-left-radius":"6px","border-bottom-right-radius":"6px"}',
                    ],
                ]
            ];
        });
        self::register('bearcms-button-3', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'buttons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-button-3.png',
                        'width' => 2000,
                        'height' => 650,
                    ]
                ],
                'element' => [
                    'type' => 'link',
                    'data' => [
                        'url' => '#',
                        'text' => 'Click me'
                    ],
                    'style' => [
                        'LinkCSS' => '{"font-family":"Arial","color":"#FFFFFF","font-size":"16px","line-height":"180%","padding-top":"12px","padding-right":"44px","padding-bottom":"12px","padding-left":"18px","border-top-left-radius":"2px","border-top-right-radius":"2px","border-bottom-left-radius":"2px","border-bottom-right-radius":"2px","background-color":"#B50D11","background-image":"url(' . $context->dir . '/assets/elements/arrow-ffffff-down2.svg)","background-position":"right 8px center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"30px 30px","background-color:hover":"#A70B0F","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#9B080C","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","box-shadow":"3px 3px 0 0 rgba(0,0,0,0.9)"}',
                    ],
                ]
            ];
        });
        self::register('bearcms-button-4', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'buttons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-button-4.png',
                        'width' => 2000,
                        'height' => 650,
                    ]
                ],
                'element' => [
                    'type' => 'link',
                    'data' => [
                        'url' => '#',
                        'text' => 'Click me'
                    ],
                    'style' => [
                        'LinkCSS' => '{"background-color:hover":"#0E910E","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#0C880C","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":"","border-top-left-radius":"40px","border-top-right-radius":"40px","border-bottom-left-radius":"40px","border-bottom-right-radius":"40px","background-color":"#119D11","padding-top":"15px","padding-right":"27px","padding-bottom":"15px","padding-left":"27px","font-family":"Arial","color":"#FFFFFF","font-size":"18px","line-height":"180%"}',
                    ],
                ]
            ];
        });
        self::register('bearcms-button-5', function () {
            $app = App::get();
            $context = $app->contexts->get(__DIR__);
            return [
                'group' => 'buttons',
                'media' => [
                    [
                        'filename' => $context->dir . '/assets/elements/image-bearcms-button-5.png',
                        'width' => 2000,
                        'height' => 650,
                    ]
                ],
                'element' => [
                    'type' => 'link',
                    'data' => [
                        'url' => '#',
                        'text' => ''
                    ],
                    'style' => [
                        'LinkCSS' => '{"padding-top":"10px","padding-right":"15px","padding-bottom":"10px","padding-left":"15px","font-family":"Arial","color":"#FFFFFF","font-size":"16px","line-height":"180%","min-width":"50px","min-height":"50px","border-top-left-radius":"25px","border-top-right-radius":"25px","border-bottom-left-radius":"25px","border-bottom-right-radius":"25px","background-color":"#DD5911","background-image":"url(' . $context->dir . '/assets/elements/arrow-ffffff-down.svg)","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"35px 35px","background-color:hover":"#CE500B","background-image:hover":"","background-position:hover":"","background-repeat:hover":"","background-attachment:hover":"","background-size:hover":"","background-color:active":"#C14D0E","background-image:active":"","background-position:active":"","background-repeat:active":"","background-attachment:active":"","background-size:active":""}',
                    ],
                ]
            ];
        });
    }

    /**
     * 
     * @param array $combinationData
     * @return array
     */
    // public static function getFiles(array $combinationData): array
    // {
    //     $files = [];
    //     $walkStructure = function ($element) use (&$files, &$walkStructure) {
    //         if ($element['type'] === 'floatingBox' || $element['type'] === 'columns') {
    //             if (isset($element['elements'])) {
    //                 foreach ($element['elements'] as $locationElements) {
    //                     foreach ($locationElements as $locationElement) {
    //                         $walkStructure($locationElement);
    //                     }
    //                 }
    //             }
    //         } elseif ($element['type'] === 'flexibleBox') {
    //             if (isset($element['elements'])) {
    //                 foreach ($element['elements'] as $locationElement) {
    //                     $walkStructure($locationElement);
    //                 }
    //             }
    //         } else {
    //             if ($element['type'] === 'image') {
    //                 if (isset($element['data'], $element['data']['filename']) && strlen($element['data']['filename']) > 0) {
    //                     $files[] = $element['data']['filename'];
    //                 }
    //             }
    //         }
    //     };
    //     $walkStructure($combinationData['element']);
    //     return $files;
    // }
}
