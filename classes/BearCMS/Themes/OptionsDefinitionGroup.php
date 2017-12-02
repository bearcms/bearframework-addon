<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Themes;

class OptionsDefinitionGroup
{

    protected $name = '';
    protected $description = '';
    protected $options = [];

    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function add($option)
    {
        $this->options[] = $option;
        return $this;
    }

    public function addGroup($name, $description = '')
    {
        $group = new \BearCMS\Themes\OptionsDefinitionGroup($name, $description);
        $this->add($group);
        return $group;
    }

    public function makeGroup($name, $description = '')
    {
        return new \BearCMS\Themes\OptionsDefinitionGroup($name, $description);
    }

    public function addElements($idPrefix, $parentSelector)
    {
        $this->options = array_merge($this->options, [
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Heading"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Large"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingLargeCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-large", "font-weight:normal;"], // ?????
                                    ["selector", $parentSelector . " .bearcms-heading-element-large"]
                                ],
                                "defaultValue" => [
                                    "color" => $idPrefix === 'contentElements' ? "#1BB0CE" : "#ffffff",
                                    "font-family" => "googlefonts:Open Sans",
                                    "font-size" => "28px",
                                    "text-align" => "center",
                                    "line-height" => "180%",
                                    "margin-top" => "0",
                                    "margin-right" => "0",
                                    "margin-bottom" => "0",
                                    "margin-left" => "0"
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Medium"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingMediumCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-medium", "font-weight:normal;"], // ?????
                                    ["selector", $parentSelector . " .bearcms-heading-element-medium"]
                                ],
                                "defaultValue" => [
                                    "color" => $idPrefix === 'contentElements' ? "#1BB0CE" : "#ffffff",
                                    "font-family" => "googlefonts:Open Sans",
                                    "font-size" => "22px",
                                    "text-align" => "center",
                                    "line-height" => "180%",
                                    "margin-top" => "0",
                                    "margin-right" => "0",
                                    "margin-bottom" => "0",
                                    "margin-left" => "0"
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Small"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HeadingSmallCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-heading-element-small", "font-weight:normal;"], // ?????
                                    ["selector", $parentSelector . " .bearcms-heading-element-small"]
                                ],
                                "defaultValue" => [
                                    "color" => $idPrefix === 'contentElements' ? "#1BB0CE" : "#ffffff",
                                    "font-family" => "googlefonts:Open Sans",
                                    "font-size" => "18px",
                                    "text-align" => "center",
                                    "line-height" => "180%",
                                    "margin-top" => "0",
                                    "margin-right" => "0",
                                    "margin-bottom" => "0",
                                    "margin-left" => "0"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Text"),
                "options" => [
                    [
                        "id" => $idPrefix . "TextCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-text-element"]
                        ],
                        "defaultValue" => [
                            "color" => $idPrefix === 'contentElements' ? "#000000" : "#ffffff",
                            "font-family" => "Arial",
                            "font-size" => "14px",
                            "line-height" => "180%"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Links"),
                        "options" => [
                            [
                                "id" => $idPrefix . "TextLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-text-element a", "display:inline-block;"], // ?????
                                    ["selector", $parentSelector . " .bearcms-text-element a"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Link"),
                "options" => [
                    [
                        "id" => $idPrefix . "LinkCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-link-element", "display:inline-block;text-decoration:none;"], // ??????
                            ["selector", $parentSelector . " .bearcms-link-element"]
                        ],
                        "defaultValue" => [
                            "color" => "#ffffff",
                            "font-family" => "Arial",
                            "font-size" => "14px",
                            "line-height" => "42px",
                            "padding-left" => "15px",
                            "padding-right" => "15px",
                            "height" => "42px",
                            "background-color" => "#1BB0CE",
                            "background-color:hover" => "#1099B5",
                            "background-color:active" => "#0A7D94"
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Image"),
                "options" => [
                    [
                        "id" => $idPrefix . "ImageCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-image-element", "overflow:hidden;"], // ??????
                            ["selector", $parentSelector . " .bearcms-image-element"]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Image gallery"),
                "options" => [
                    [
                        "id" => $idPrefix . "ImageGalleryCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-image-gallery-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Image"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ImageGalleryImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-image-gallery-element-image", "overflow:hidden;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-image-gallery-element-image"]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Video"),
                "options" => [
                    [
                        "id" => $idPrefix . "VideoCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                        "cssOutput" => [
                            ["rule", $parentSelector . " .bearcms-video-element", "overflow:hidden;"], // ??????
                            ["selector", $parentSelector . " .bearcms-video-element"]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Navigation"),
                "options" => [
                    [
                        "id" => $idPrefix . "NavigationCSS",
                        "type" => "css",
                        "cssTypes" => ["cssBorder", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-navigation-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Elements"),
                        "options" => [
                            [
                                "id" => $idPrefix . "NavigationItemLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-navigation-element-item a", "display:inline-block;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-navigation-element-item a"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Contact Form"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.contactForm.Email label"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ContactFormEmailLabelCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-contact-form-element-email-label", "display:block;"], // ?????? inline-block
                                    ["selector", $parentSelector . " .bearcms-contact-form-element-email-label"]
                                ],
                                "defaultValue" => [
                                    "color" => $idPrefix === 'contentElements' ? "#000000" : "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.contactForm.Email input"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ContactFormEmailInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-contact-form-element-email", "box-sizing:border-box;border:0;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-contact-form-element-email"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "width" => "100%",
                                    "max-width" => "400px",
                                    "height" => "42px",
                                    "border-top" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-bottom" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-left" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-right" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-top:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-bottom:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-left:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-right:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-top:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-bottom:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-left:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-right:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.contactForm.Message label"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ContactFormMessageLabelCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-contact-form-element-message-label", "display:block;"], // ?????? inline-block
                                    ["selector", $parentSelector . " .bearcms-contact-form-element-message-label"]
                                ],
                                "defaultValue" => [
                                    "margin-top" => "10px",
                                    "color" => $idPrefix === 'contentElements' ? "#000000" : "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.contactForm.Message input"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ContactFormMessageInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-contact-form-element-message", "box-sizing:border-box;border:0;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-contact-form-element-message"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "padding-top" => "10px",
                                    "padding-bottom" => "10px",
                                    "width" => "100%",
                                    "max-width" => "400px",
                                    "height" => "200px",
                                    "border-top" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-bottom" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-left" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-right" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-top:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-bottom:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-left:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-right:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-top:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-bottom:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-left:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-right:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.contactForm.Send button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ContactFormSendButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-contact-form-element-send-button", "display:inline-block;text-decoration:none;"], // ?????? 
                                    ["selector", $parentSelector . " .bearcms-contact-form-element-send-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "height" => "42px",
                                    "background-color" => "#1BB0CE",
                                    "background-color:hover" => "#1099B5",
                                    "background-color:active" => "#0A7D94",
                                    "margin-top" => "10px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.contactForm.Send button waiting"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "ContactFormSendButtonWaitingCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-contact-form-element-send-button-waiting", "display:inline-block;text-decoration:none;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-contact-form-element-send-button-waiting"]
                                        ],
                                        "defaultValue" => [
                                            "background-color" => "#aaaaaa"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Comments"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.comments.Comment"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsCommentCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-comments-comment", "overflow:hidden;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-comments-comment"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "10px",
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Author name"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsAuthorNameCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-author-name", "display:inline-block;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-comments-comment-author-name"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "color:hover" => "#1099B5",
                                            "color:active" => "#0A7D94",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "text-decoration" => "underline",
                                            "margin-bottom" => "4px"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Author image"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsAuthorImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-author-image", "display:inline-block;float:left;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-comments-comment-author-image"]
                                        ],
                                        "defaultValue" => [
                                            "width" => "50px",
                                            "height" => "50px",
                                            "margin-right" => "8px",
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Date"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsDateCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-date", "display:inline-block;float:right;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-comments-comment-date"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#aaa",
                                            "font-family" => "Arial",
                                            "font-size" => "12px",
                                            "line-height" => "180%",
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Text"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsTextCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-comments-comment-text"]
                                        ],
                                        "defaultValue" => [
                                            "color" => $idPrefix === 'contentElements' ? "#000000" : "#ffffff",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Text links"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsTextLinksCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-comment-text a", "display:inline-block;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-comments-comment-text a"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "color:hover" => "#1099B5",
                                            "color:active" => "#0A7D94",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%",
                                            "text-decoration" => "underline"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.comments.Text input"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsTextInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-element-text", "box-sizing:border-box;border:0;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-comments-element-text"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "padding-top" => "10px",
                                    "padding-bottom" => "10px",
                                    "width" => "100%",
                                    "height" => "80px",
                                    "border-top" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-bottom" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-left" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-right" => $idPrefix === 'contentElements' ? "1px solid #cccccc" : "",
                                    "border-top:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-bottom:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-left:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-right:hover" => $idPrefix === 'contentElements' ? "1px solid #aaaaaa" : "",
                                    "border-top:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-bottom:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-left:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                    "border-right:active" => $idPrefix === 'contentElements' ? "1px solid #888888" : "",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.comments.Send button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsSendButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-element-send-button", "display:inline-block;text-decoration:none;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-comments-element-send-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "height" => "42px",
                                    "background-color" => "#1BB0CE",
                                    "background-color:hover" => "#1099B5",
                                    "background-color:active" => "#0A7D94",
                                    "margin-top" => "10px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Send button waiting"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsSendButtonWaitingCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-comments-element-send-button-waiting", "display:inline-block;text-decoration:none;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-comments-element-send-button-waiting"]
                                        ],
                                        "defaultValue" => [
                                            "background-color" => "#aaaaaa"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.comments.Show more button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "CommentsShowMoreButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-comments-show-more-button", "display:inline-block;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-comments-show-more-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline",
                                    "margin-bottom" => "5px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.comments.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "CommentsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-comments-show-more-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Forum posts"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPosts.Post"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ForumPostsPostCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", $parentSelector . " .bearcms-forum-posts-post"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "5px",
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPosts.Title"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "ForumPostsTitleCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-forum-posts-post-title"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "color:hover" => "#1099B5",
                                            "color:active" => "#0A7D94",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%",
                                            "text-decoration" => "underline"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPosts.Replies count"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "ForumPostsRepliesCountCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", $parentSelector . " .bearcms-forum-posts-post-replies-count", "display:inline-block;float:right;"], // ??????
                                            ["selector", $parentSelector . " .bearcms-forum-posts-post-replies-count"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#aaa",
                                            "font-family" => "Arial",
                                            "font-size" => "12px",
                                            "line-height" => "180%",
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPosts.Show more button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ForumPostsShowMoreButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-forum-posts-show-more-button", "display:inline-block;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-forum-posts-show-more-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline",
                                    "margin-bottom" => "5px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPosts.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "ForumPostsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-forum-posts-show-more-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPosts.New post bitton"),
                        "options" => [
                            [
                                "id" => $idPrefix . "ForumPostsNewPostButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-forum-posts-new-post-button", "display:inline-block;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-forum-posts-new-post-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPosts.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "ForumPostsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"], // ??????
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-forum-posts-new-post-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.HTML code"),
                "options" => [
                    [
                        "id" => $idPrefix . "HtmlCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-html-element"]
                        ],
                        "defaultValue" => [
                            "color" => $idPrefix === 'contentElements' ? "#000000" : "#ffffff",
                            "font-family" => "Arial",
                            "font-size" => "14px",
                            "line-height" => "180%"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Links"),
                        "options" => [
                            [
                                "id" => $idPrefix . "HtmlLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-html-element a", "display:inline-block;"], // ??????
                                    ["selector", $parentSelector . " .bearcms-html-element a"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Blog posts"),
                "options" => [
                    [
                        "id" => $idPrefix . "BlogPostsCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                        "cssOutput" => [
                            ["selector", $parentSelector . " .bearcms-blog-posts-element"]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Post"),
                        "options" => [
                            [
                                "id" => $idPrefix . "BlogPostsPostCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                                "cssOutput" => [
                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Title"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostTitleCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "font-family" => "googlefonts:Open Sans",
                                            "font-size" => "22px",
                                            "text-align" => "left",
                                            "line-height" => "180%"
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.theme1.options.Container"),
                                        "options" => [
                                            [
                                                "id" => $idPrefix . "BlogPostsPostTitleContainerCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post-title-container"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Date"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostDateCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date"]
                                        ],
                                        "defaultValue" => [
                                            "color" => $idPrefix === 'contentElements' ? "#777777" : "#eeeeee",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%"
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.theme1.options.Container"),
                                        "options" => [
                                            [
                                                "id" => $idPrefix . "BlogPostsPostDateContainerCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-post-date-container"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Content"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsPostContentCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-post-content"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.blogPosts.Show more button"),
                        "options" => [
                            [
                                "id" => $idPrefix . "BlogPostsShowMoreButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", $parentSelector . " .bearcms-blog-posts-element-show-more-button", "display:inline-block;"], // ????
                                    ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "color:hover" => "#1099B5",
                                    "color:active" => "#0A7D94",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "text-decoration" => "underline",
                                    "margin-bottom" => "5px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.blogPosts.Container"),
                                "options" => [
                                    [
                                        "id" => $idPrefix . "BlogPostsShowMoreButtonContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", $parentSelector . " .bearcms-blog-posts-element-show-more-button-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        return $this;
    }

    public function addPages()
    {
        $this->options = array_merge($this->options, [
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Blog post page"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Title"),
                        "options" => [
                            [
                                "id" => "blogPostPageTitleCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-blogpost-page-title", "font-weight:normal;"], // ????
                                    ["selector", ".bearcms-blogpost-page-title"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "font-family" => "Arial",
                                    "font-size" => "28px",
                                    "text-align" => "center",
                                    "line-height" => "180%",
                                    "margin-top" => "0",
                                    "margin-right" => "0",
                                    "margin-bottom" => "0",
                                    "margin-left" => "0"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Container"),
                                "options" => [
                                    [
                                        "id" => "blogPostPageTitleContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-blogpost-page-title-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Date"),
                        "options" => [
                            [
                                "id" => "blogPostPageDateVisibility",
                                "type" => "list",
                                "name" => "Видимост", //TODO
                                "defaultValue" => "1",
                                "values" => [
                                    [
                                        "value" => "1",
                                        "name" => "Видима"
                                    ],
                                    [
                                        "value" => "0",
                                        "name" => "Скрита"
                                    ]
                                ]
                            ],
                            [
                                "id" => "blogPostPageDateCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".bearcms-blogpost-page-date"] // ????? + inline-block
                                ],
                                "defaultValue" => [
                                    "color" => "#777777",
                                    "font-family" => "Arial",
                                    "text-align" => "center",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Container"),
                                "options" => [
                                    [
                                        "id" => "blogPostPageDateContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-blogpost-page-date-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Content"),
                        "options" => [
                            [
                                "id" => "blogPostPageContentCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", ".bearcms-blogpost-page-content"]
                                ],
                                "defaultValue" => [
                                    "padding-top" => "15px"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.New forum post page"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.newForumPostPage.Title label"),
                        "options" => [
                            [
                                "id" => "newForumPostPageTitleLabelCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-new-forum-post-page-title-label", "display:block;"], // ?????
                                    ["selector", ".bearcms-new-forum-post-page-title-label"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.newForumPostPage.Title input"),
                        "options" => [
                            [
                                "id" => "newForumPostPageTitleInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-new-forum-post-page-title", "box-sizing:border-box;border:0;"],
                                    ["selector", ".bearcms-new-forum-post-page-title"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "width" => "100%",
                                    "height" => "42px",
                                    "border-top" => "1px solid #cccccc",
                                    "border-bottom" => "1px solid #cccccc",
                                    "border-left" => "1px solid #cccccc",
                                    "border-right" => "1px solid #cccccc",
                                    "border-top:hover" => "1px solid #aaaaaa",
                                    "border-bottom:hover" => "1px solid #aaaaaa",
                                    "border-left:hover" => "1px solid #aaaaaa",
                                    "border-right:hover" => "1px solid #aaaaaa",
                                    "border-top:active" => "1px solid #888888",
                                    "border-bottom:active" => "1px solid #888888",
                                    "border-left:active" => "1px solid #888888",
                                    "border-right:active" => "1px solid #888888",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.newForumPostPage.Text label"),
                        "options" => [
                            [
                                "id" => "newForumPostPageTextLabelCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-new-forum-post-page-text-label", "display:block;"],
                                    ["selector", ".bearcms-new-forum-post-page-text-label"]
                                ],
                                "defaultValue" => [
                                    "margin-top" => "10px",
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.newForumPostPage.Text input"),
                        "options" => [
                            [
                                "id" => "newForumPostPageTextInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-new-forum-post-page-text", "box-sizing:border-box;border:0;"],
                                    ["selector", ".bearcms-new-forum-post-page-text"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "padding-top" => "10px",
                                    "padding-bottom" => "10px",
                                    "width" => "100%",
                                    "height" => "200px",
                                    "border-top" => "1px solid #cccccc",
                                    "border-bottom" => "1px solid #cccccc",
                                    "border-left" => "1px solid #cccccc",
                                    "border-right" => "1px solid #cccccc",
                                    "border-top:hover" => "1px solid #aaaaaa",
                                    "border-bottom:hover" => "1px solid #aaaaaa",
                                    "border-left:hover" => "1px solid #aaaaaa",
                                    "border-right:hover" => "1px solid #aaaaaa",
                                    "border-top:active" => "1px solid #888888",
                                    "border-bottom:active" => "1px solid #888888",
                                    "border-left:active" => "1px solid #888888",
                                    "border-right:active" => "1px solid #888888",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.newForumPostPage.Send button"),
                        "options" => [
                            [
                                "id" => "newForumPostPageSendButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-new-forum-post-page-send-button", "display:inline-block;text-decoration:none;"],
                                    ["selector", ".bearcms-new-forum-post-page-send-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "height" => "42px",
                                    "background-color" => "#1BB0CE",
                                    "background-color:hover" => "#1099B5",
                                    "background-color:active" => "#0A7D94",
                                    "margin-top" => "10px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.newForumPostPage.Send button waiting"),
                                "options" => [
                                    [
                                        "id" => "newForumPostPageSendButtonWaitingCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".bearcms-new-forum-post-page-send-button-waiting", "display:inline-block;text-decoration:none;"],
                                            ["selector", ".bearcms-new-forum-post-page-send-button-waiting"]
                                        ],
                                        "defaultValue" => [
                                            "background-color" => "#aaaaaa"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.Forum post page"),
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Title"),
                        "options" => [
                            [
                                "id" => "forumPostPageTitleCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-title", "font-weight:normal;"],
                                    ["selector", ".bearcms-forum-post-page-title"]
                                ],
                                "defaultValue" => [
                                    "color" => "#1BB0CE",
                                    "font-family" => "Arial",
                                    "font-size" => "28px",
                                    "text-align" => "center",
                                    "line-height" => "180%",
                                    "margin-top" => "0",
                                    "margin-right" => "0",
                                    "margin-bottom" => "0",
                                    "margin-left" => "0"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Container"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageTitleContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-forum-post-page-title-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Date"),
                        "options" => [
                            [
                                "id" => "forumPostPageDateCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".bearcms-forum-post-page-date"]
                                ],
                                "defaultValue" => [
                                    "color" => "#777777",
                                    "font-family" => "Arial",
                                    "text-align" => "center",
                                    "font-size" => "14px",
                                    "line-height" => "180%"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Container"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageDateContainerCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["selector", ".bearcms-blogpost-page-date-container"]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Content"),
                        "options" => [
                            [
                                "id" => "forumPostPageContentCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", ".bearcms-forum-post-page-content"]
                                ],
                                "defaultValue" => [
                                    "padding-top" => "15px"
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Reply"),
                        "options" => [
                            [
                                "id" => "forumPostPageReplyCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-reply", "overflow:hidden;"],
                                    ["selector", ".bearcms-forum-post-page-reply"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "10px",
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Author name"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageReplyAuthorNameCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".bearcms-forum-post-page-reply-author-name", "display:inline-block;"],
                                            ["selector", ".bearcms-forum-post-page-reply-author-name"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "color:hover" => "#1099B5",
                                            "color:active" => "#0A7D94",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "text-decoration" => "underline",
                                            "margin-bottom" => "4px"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Author image"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageReplyAuthorImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                        "cssOutput" => [
                                            ["rule", ".bearcms-forum-post-page-reply-author-image", "display:inline-block;float:left;"],
                                            ["selector", ".bearcms-forum-post-page-reply-author-image"]
                                        ],
                                        "defaultValue" => [
                                            "width" => "50px",
                                            "height" => "50px",
                                            "margin-right" => "8px",
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Date"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageReplyDateCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".bearcms-forum-post-page-reply-date", "display:inline-block;float:right;"],
                                            ["selector", ".bearcms-forum-post-page-reply-date"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#aaa",
                                            "font-family" => "Arial",
                                            "font-size" => "12px",
                                            "line-height" => "180%",
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Text"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageReplyTextCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["selector", ".bearcms-forum-post-page-reply-text"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#000000",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Text links"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageReplyTextLinksCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".bearcms-forum-post-page-reply-text a", "display:inline-block;"],
                                            ["selector", ".bearcms-forum-post-page-reply-text a"]
                                        ],
                                        "defaultValue" => [
                                            "color" => "#1BB0CE",
                                            "color:hover" => "#1099B5",
                                            "color:active" => "#0A7D94",
                                            "font-family" => "Arial",
                                            "font-size" => "14px",
                                            "line-height" => "180%",
                                            "text-decoration" => "underline"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Text input"),
                        "options" => [
                            [
                                "id" => "forumPostPageTextInputCSS",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-text", "box-sizing:border-box;border:0;"],
                                    ["selector", ".bearcms-forum-post-page-text"]
                                ],
                                "defaultValue" => [
                                    "color" => "#000000",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "180%",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "padding-top" => "10px",
                                    "padding-bottom" => "10px",
                                    "width" => "100%",
                                    "height" => "80px",
                                    "border-top" => "1px solid #cccccc",
                                    "border-bottom" => "1px solid #cccccc",
                                    "border-left" => "1px solid #cccccc",
                                    "border-right" => "1px solid #cccccc",
                                    "border-top:hover" => "1px solid #aaaaaa",
                                    "border-bottom:hover" => "1px solid #aaaaaa",
                                    "border-left:hover" => "1px solid #aaaaaa",
                                    "border-right:hover" => "1px solid #aaaaaa",
                                    "border-top:active" => "1px solid #888888",
                                    "border-bottom:active" => "1px solid #888888",
                                    "border-left:active" => "1px solid #888888",
                                    "border-right:active" => "1px solid #888888",
                                ]
                            ],
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.forumPostPage.Send button"),
                        "options" => [
                            [
                                "id" => "forumPostPageSendButtonCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-send-button", "display:inline-block;text-decoration:none;"],
                                    ["selector", ".bearcms-forum-post-page-send-button"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "line-height" => "42px",
                                    "padding-left" => "15px",
                                    "padding-right" => "15px",
                                    "height" => "42px",
                                    "background-color" => "#1BB0CE",
                                    "background-color:hover" => "#1099B5",
                                    "background-color:active" => "#0A7D94",
                                    "margin-top" => "10px"
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.forumPostPage.Send button waiting"),
                                "options" => [
                                    [
                                        "id" => "forumPostPageSendButtonWaitingCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".bearcms-forum-post-page-send-button-waiting", "display:inline-block;text-decoration:none;"],
                                            ["selector", ".bearcms-forum-post-page-send-button-waiting"]
                                        ],
                                        "defaultValue" => [
                                            "background-color" => "#aaaaaa"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]);
        return $this;
    }

    public function addCustomCSS()
    {
        $this->options[] = [
            "id" => "customCSS",
            "type" => "cssCode",
            "name" => __("bearcms.themes.theme1.options.Custom CSS")
        ];
        return $this;
    }

    public function toArray()
    {
        $result = [
            "type" => "group",
            "name" => $this->name
        ];
        if (strlen($this->description) > 0) {
            $result['description'] = $this->description;
        }
        $result['options'] = [];
        foreach ($this->options as $option) {
            $result['options'][] = is_object($option) && method_exists($option, 'toArray') ? $option->toArray() : (is_array($option) ? $option : (array) $option);
        }
        return $result;
    }

}
