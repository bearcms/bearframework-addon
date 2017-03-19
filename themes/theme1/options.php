<?php

$getElementsList = function($idPrefix, $parentClassNameSelector) {
    return [
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
                                ["rule", $parentClassNameSelector . " .bearcms-heading-element-large", "font-weight:normal;"],
                                ["selector", $parentClassNameSelector . " .bearcms-heading-element-large"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-heading-element-large"]
                            ],
                            "defaultValue" => [
                                "color" => "#1BB0CE",
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
                                ["rule", $parentClassNameSelector . " .bearcms-heading-element-medium", "font-weight:normal;"],
                                ["selector", $parentClassNameSelector . " .bearcms-heading-element-medium"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-heading-element-medium"]
                            ],
                            "defaultValue" => [
                                "color" => "#1BB0CE",
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
                                ["rule", $parentClassNameSelector . " .bearcms-heading-element-small", "font-weight:normal;"],
                                ["selector", $parentClassNameSelector . " .bearcms-heading-element-small"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-heading-element-small"]
                            ],
                            "defaultValue" => [
                                "color" => "#1BB0CE",
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
                        ["selector", $parentClassNameSelector . " .bearcms-text-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-text-element"]
                    ],
                    "defaultValue" => [
                        "color" => "#000000",
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
                                ["rule", $parentClassNameSelector . " .bearcms-text-element a", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-text-element a"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-text-element a"]
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
                        ["rule", $parentClassNameSelector . " .bearcms-link-element", "display:inline-block;text-decoration:none;"],
                        ["selector", $parentClassNameSelector . " .bearcms-link-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-link-element"]
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
                        ["rule", $parentClassNameSelector . " .bearcms-image-element", "overflow:hidden;"],
                        ["selector", $parentClassNameSelector . " .bearcms-image-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-image-element"]
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
                        ["selector", $parentClassNameSelector . " .bearcms-image-gallery-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-image-gallery-element"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                ["selector", $parentClassNameSelector . " .bearcms-image-gallery-element-image"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-image-gallery-element-image"]
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
                        ["rule", $parentClassNameSelector . " .bearcms-video-element", "overflow:hidden;"],
                        ["selector", $parentClassNameSelector . " .bearcms-video-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-video-element"]
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
                        ["selector", $parentClassNameSelector . " .bearcms-navigation-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-navigation-element"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-navigation-element-item a", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-navigation-element-item a"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-navigation-element-item a"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-contact-form-element-email-label", "display:block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-email-label"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-email-label"]
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
                    "name" => __("bearcms.themes.theme1.options.contactForm.Email input"),
                    "options" => [
                        [
                            "id" => $idPrefix . "ContactFormEmailInputCSS",
                            "type" => "css",
                            "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-email"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-email"]
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
                    "name" => __("bearcms.themes.theme1.options.contactForm.Message label"),
                    "options" => [
                        [
                            "id" => $idPrefix . "ContactFormMessageLabelCSS",
                            "type" => "css",
                            "cssOutput" => [
                                ["rule", $parentClassNameSelector . " .bearcms-contact-form-element-message-label", "display:block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-message-label"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-message-label"]
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
                    "name" => __("bearcms.themes.theme1.options.contactForm.Message input"),
                    "options" => [
                        [
                            "id" => $idPrefix . "ContactFormMessageInputCSS",
                            "type" => "css",
                            "cssTypes" => ["cssText", "cssTextShadow", "cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-message"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-message"]
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
                    "name" => __("bearcms.themes.theme1.options.contactForm.Send button"),
                    "options" => [
                        [
                            "id" => $idPrefix . "ContactFormSendButtonCSS",
                            "type" => "css",
                            "cssOutput" => [
                                ["rule", $parentClassNameSelector . " .bearcms-contact-form-element-send-button", "display:inline-block;text-decoration:none;"],
                                ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-send-button"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-send-button"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-contact-form-element-send-button-waiting", "display:inline-block;text-decoration:none;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-contact-form-element-send-button-waiting"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-contact-form-element-send-button-waiting"]
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
                                ["selector", $parentClassNameSelector . " .bearcms-comments-comment"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-comments-comment-author-name", "display:inline-block;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-comment-author-name"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment-author-name"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-comments-comment-author-image", "display:inline-block;float:left;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-comment-author-image"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment-author-image"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-comments-comment-date", "display:inline-block;float:right;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-comment-date"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment-date"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-comment-text"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment-text"]
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
                            "name" => __("bearcms.themes.theme1.options.comments.Text links"),
                            "options" => [
                                [
                                    "id" => $idPrefix . "CommentsTextLinksCSS",
                                    "type" => "css",
                                    "cssOutput" => [
                                        ["rule", $parentClassNameSelector . " .bearcms-comments-comment-text a", "display:inline-block;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-comment-text a"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-comment-text a"]
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
                                ["selector", $parentClassNameSelector . " .bearcms-comments-element-text"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-comments-element-text"]
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
                    "name" => __("bearcms.themes.theme1.options.comments.Send button"),
                    "options" => [
                        [
                            "id" => $idPrefix . "CommentsSendButtonCSS",
                            "type" => "css",
                            "cssOutput" => [
                                ["rule", $parentClassNameSelector . " .bearcms-comments-element-send-button", "display:inline-block;text-decoration:none;"],
                                ["selector", $parentClassNameSelector . " .bearcms-comments-element-send-button"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-comments-element-send-button"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-comments-element-send-button-waiting", "display:inline-block;text-decoration:none;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-element-send-button-waiting"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-element-send-button-waiting"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-comments-show-more-button", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-comments-show-more-button"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-comments-show-more-button"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-comments-show-more-button-container"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-comments-show-more-button-container"]
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
                                ["selector", $parentClassNameSelector . " .bearcms-forum-posts-post"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-post"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-forum-posts-post-title"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-post-title"]
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
                                        ["rule", $parentClassNameSelector . " .bearcms-forum-posts-post-replies-count", "display:inline-block;float:right;"],
                                        ["selector", $parentClassNameSelector . " .bearcms-forum-posts-post-replies-count"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-post-replies-count"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-forum-posts-show-more-button", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-forum-posts-show-more-button"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-show-more-button"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-forum-posts-show-more-button-container"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-show-more-button-container"]
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
                                ["rule", $parentClassNameSelector . " .bearcms-forum-posts-new-post-button", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-forum-posts-new-post-button"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-new-post-button"]
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
                                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                    "cssOutput" => [
                                        ["selector", $parentClassNameSelector . " .bearcms-forum-posts-new-post-button-container"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-forum-posts-new-post-button-container"]
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
                        ["selector", $parentClassNameSelector . " .bearcms-html-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-html-element"]
                    ],
                    "defaultValue" => [
                        "color" => "#000000",
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
                                ["rule", $parentClassNameSelector . " .bearcms-html-element a", "display:inline-block;"],
                                ["selector", $parentClassNameSelector . " .bearcms-html-element a"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-html-element a"]
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
                        ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element"]
                    ],
                    "onCustomize" => [
                        ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element"]
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
                                ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post"]
                            ],
                            "onCustomize" => [
                                ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post-title"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post-title"]
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
                                                ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post-title-container"]
                                            ],
                                            "onCustomize" => [
                                                ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post-title-container"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post-date"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post-date"]
                                    ],
                                    "defaultValue" => [
                                        "color" => "#777777",
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
                                                ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post-date-container"]
                                            ],
                                            "onCustomize" => [
                                                ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post-date-container"]
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
                                        ["selector", $parentClassNameSelector . " .bearcms-blog-posts-element-post-content"]
                                    ],
                                    "onCustomize" => [
                                        ["updateRule", $parentClassNameSelector . " .bearcms-blog-posts-element-post-content"]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
};

$options = [
    [
        "type" => "group",
        "name" => __("bearcms.themes.theme1.options.Header"),
        "options" => [
            [
                "id" => "headerCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                "cssOutput" => [
                    ["rule", ".template-header", "box-sizing:border-box;"],
                    ["selector", ".template-header"]
                ],
                "onCustomize" => [
                    ["updateRule", ".template-header"]
                ],
                "defaultValue" => [
                    "max-width" => "800px",
                    "margin-left" => "auto",
                    "margin-right" => "auto",
                    "padding-right" => "40px",
                    "padding-bottom" => "40px",
                    "padding-left" => "40px",
                    "padding-top" => "80px"
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Logo"),
                "options" => [
                    [
                        "id" => "headerLogoImage",
                        "type" => "image",
                        "name" => __("bearcms.themes.theme1.options.Image")
                    ],
                    [
                        "id" => "headerLogoImageCSS",
                        "type" => "css",
                        "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-header-logo", "box-sizing:border-box;display:inline-block;overflow:hidden;font-size:0;"],
                            ["selector", ".template-header-logo"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-header-logo"]
                        ],
                        "defaultValue" => [
                            "width" => "300px"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Container"),
                        "options" => [
                            [
                                "id" => "headerLogoImageContainerCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                                "cssOutput" => [
                                    ["rule", ".template-header-logo-container", "box-sizing:border-box;"],
                                    ["selector", ".template-header-logo-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-header-logo-container"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "40px",
                                    "text-align" => "center"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Title"),
                "options" => [
                    [
                        "id" => "headerTitleVisibility",
                        "type" => "list",
                        "name" => __("bearcms.themes.theme1.options.Visibility"),
                        "defaultValue" => "1",
                        "values" => [
                            [
                                "value" => "1",
                                "name" => __("bearcms.themes.theme1.options.Visible")
                            ],
                            [
                                "value" => "0",
                                "name" => __("bearcms.themes.theme1.options.Hidden")
                            ]
                        ]
                    ],
                    [
                        "id" => "headerTitleCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["rule", ".template-header-title", "display:inline-block;text-decoration:none;"],
                            ["selector", ".template-header-title"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-header-title"]
                        ],
                        "defaultValue" => [
                            "font-family" => "googlefonts:Open Sans",
                            "font-size" => "40px",
                            "font-weight" => "bold",
                            "color" => "#1BB0CE",
                            "color:hover" => "#1099B5",
                            "color:active" => "#0A7D94"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Container"),
                        "options" => [
                            [
                                "id" => "headerTitleContainerCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                                "cssOutput" => [
                                    ["rule", ".template-header-title-container", "box-sizing:border-box;"],
                                    ["selector", ".template-header-title-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-header-title-container"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "40px",
                                    "text-align" => "center"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Description"),
                "options" => [
                    [
                        "id" => "headerDescriptionVisibility",
                        "type" => "list",
                        "name" => __("bearcms.themes.theme1.options.Visibility"),
                        "defaultValue" => "1",
                        "values" => [
                            [
                                "value" => "1",
                                "name" => __("bearcms.themes.theme1.options.Visible")
                            ],
                            [
                                "value" => "0",
                                "name" => __("bearcms.themes.theme1.options.Hidden")
                            ]
                        ]
                    ],
                    [
                        "id" => "headerDescriptionCSS",
                        "type" => "css",
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBackground", "cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-header-description", "box-sizing:border-box;display:inline-block;"],
                            ["selector", ".template-header-description"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-header-description"]
                        ],
                        "defaultValue" => [
                            "color" => "#666666",
                            "font-family" => "googlefonts:Open Sans",
                            "font-size" => "15px"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Container"),
                        "options" => [
                            [
                                "id" => "headerDescriptionContainerCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                                "cssOutput" => [
                                    ["rule", ".template-header-description-container", "box-sizing:border-box;"],
                                    ["selector", ".template-header-description-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-header-description-container"]
                                ],
                                "defaultValue" => [
                                    "margin-bottom" => "40px",
                                    "text-align" => "center"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Container"),
                "options" => [
                    [
                        "id" => "headerContainerCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-header-container", "box-sizing:border-box;"],
                            ["selector", ".template-header-container"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-header-container"]
                        ],
                        "defaultValue" => [
                            "background-color" => "#ffffff"
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "type" => "group",
        "name" => __("bearcms.themes.theme1.options.Navigation"),
        "options" => [
            [
                "id" => "navigationPosition",
                "type" => "list",
                "name" => __("bearcms.themes.theme1.options.Position"),
                "defaultValue" => "2",
                "values" => [
                    [
                        "value" => "1",
                        "name" => __("bearcms.themes.theme1.options.Top")
                    ],
                    [
                        "value" => "2",
                        "name" => __("bearcms.themes.theme1.options.Bottom")
                    ]
                ]
            ],
            [
                "id" => "navigationCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                "cssOutput" => [
                    ["rule", ".template-navigation", "box-sizing:border-box;"],
                    ["selector", ".template-navigation"]
                ],
                "onCustomize" => [
                    ["updateRule", ".template-navigation"]
                ],
                "defaultValue" => [
                    "max-width" => "800px",
                    "margin-left" => "auto",
                    "margin-right" => "auto",
                    "padding-right" => "15px",
                    "padding-left" => "15px",
                    "text-align" => "center"
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Home button"),
                "options" => [
                    [
                        "id" => "navigationHomeButtonVisibility",
                        "type" => "list",
                        "name" => __("bearcms.themes.theme1.options.Visibility"),
                        "defaultValue" => "1",
                        "values" => [
                            [
                                "value" => "1",
                                "name" => __("bearcms.themes.theme1.options.Visible")
                            ],
                            [
                                "value" => "0",
                                "name" => __("bearcms.themes.theme1.options.Hidden")
                            ]
                        ]
                    ],
                    [
                        "id" => "navigationHomeButtonText",
                        "type" => "textbox",
                        "name" => __("bearcms.themes.theme1.options.Text"),
                        "defaultValue" => "Home"
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Item"),
                "options" => [
                    [
                        "id" => "navigationItemCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding"],
                        "cssOutput" => [
                            ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item", "display:inline-block;"],
                            ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a", "display:block;white-space:nowrap;text-overflow:ellipsis;"],
                            ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a", "font-family:inherit;color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;text-decoration:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;text-shadow:inherit;"],
                            ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a"],
                            ["selector", ".template-navigation #template-navigation-toggle-button + label"],
                            ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a"],
                            ["updateRule", ".template-navigation #template-navigation-toggle-button + label"],
                            ["updateRule", ".template-navigation #template-navigation-toggle-button:checked + label"]
                        ],
                        "defaultValue" => [
                            "line-height" => "44px",
                            "padding-left" => "16px",
                            "padding-right" => "16px",
                            "height" => "44px"
                        ]
                    ],
                    [
                        "id" => "navigationItemCSS2",
                        "type" => "css",
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item"],
                            ["selector", ".template-navigation #template-navigation-toggle-button + label"],
                            ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item"],
                            ["updateRule", ".template-navigation #template-navigation-toggle-button + label"],
                            ["updateRule", ".template-navigation #template-navigation-toggle-button:checked + label"]
                        ],
                        "defaultValue" => [
                            "color" => "#ffffff",
                            "color:hover" => "#ffffff",
                            "color:active" => "#ffffff",
                            "text-decoration" => "none",
                            "font-family" => "Arial",
                            "font-size" => "15px",
                            "line-height" => "100%",
                            "background-color:hover" => "#1099B5",
                            "background-color:active" => "#0A7D94",
                            "text-align" => "left"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Selected"),
                        "options" => [
                            [
                                "id" => "navigationSelectedItemCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding"],
                                "cssOutput" => [
                                    ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected > a"],
                                    ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected > a"],
                                    ["updateRule", ".template-navigation #template-navigation-toggle-button:checked + label"]
                                ]
                            ],
                            [
                                "id" => "navigationSelectedItemCSS2",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected"],
                                    ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected"],
                                    ["updateRule", ".template-navigation #template-navigation-toggle-button:checked + label"]
                                ],
                                "defaultValue" => [
                                    "background-color" => "#1BB0CE"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Subitem"),
                "options" => [
                    [
                        "id" => "navigationSubitemCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding"],
                        "cssOutput" => [
                            ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item", "display:block;"],
                            ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "display:block;white-space:nowrap;text-overflow:ellipsis;"],
                            ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "font-family:inherit;color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;text-decoration:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;text-shadow:inherit;"],
                            ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a"]
                        ],
                        "defaultValue" => [
                            "line-height" => "44px",
                            "padding-left" => "16px",
                            "padding-right" => "16px",
                            "height" => "44px"
                        ]
                    ],
                    [
                        "id" => "navigationSubitemCSS2",
                        "type" => "css",
                        "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item"]
                        ],
                        "defaultValue" => [
                            "color" => "#ffffff",
                            "color:hover" => "#ffffff",
                            "color:active" => "#ffffff",
                            "text-decoration" => "none",
                            "font-family" => "Arial",
                            "font-size" => "15px",
                            "line-height" => "100%",
                            "background-color" => "#1BB0CE",
                            "background-color:hover" => "#1099B5",
                            "background-color:active" => "#0A7D94",
                            "text-align" => "left"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Selected"),
                        "options" => [
                            [
                                "id" => "navigationSelectedSubitemCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding"],
                                "cssOutput" => [
                                    ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected > a"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected > a"]
                                ]
                            ],
                            [
                                "id" => "navigationSelectedSubitemCSS2",
                                "type" => "css",
                                "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected"]
                                ],
                                "defaultValue" => [
                                    "background-color" => "#1BB0CE"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Container"),
                "options" => [
                    [
                        "id" => "navigationContainerCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-navigation-container", "box-sizing:border-box;"],
                            ["selector", ".template-navigation-container"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-navigation-container"]
                        ],
                        "defaultValue" => [
                            "background-color" => "#333333"
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "type" => "group",
        "name" => __("bearcms.themes.theme1.options.Home page special block"),
        "description" => "This content block is placed above the navigation on the home page. It is useful for welcoming your visitors with images, videos or text.",
        "options" => [
            [
                "id" => "homePageSpecialContentBlockVisibility",
                "type" => "list",
                "name" => __("bearcms.themes.theme1.options.Visibility"),
                "defaultValue" => "1",
                "values" => [
                    [
                        "value" => "1",
                        "name" => __("bearcms.themes.theme1.options.Visible")
                    ],
                    [
                        "value" => "0",
                        "name" => __("bearcms.themes.theme1.options.Hidden")
                    ]
                ]
            ],
            [
                "id" => "homePageSpecialContentBlockCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                "cssOutput" => [
                    ["rule", ".template-homepage-special-content-block", "box-sizing:border-box;"],
                    ["selector", ".template-homepage-special-content-block"]
                ],
                "onCustomize" => [
                    ["updateRule", ".template-homepage-special-content-block"]
                ],
                "defaultValue" => [
                    "max-width" => "800px",
                    "margin-left" => "auto",
                    "margin-right" => "auto",
                    "padding-top" => "25px",
                    "padding-right" => "15px",
                    "padding-bottom" => "25px",
                    "padding-left" => "15px"
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Elements"),
                "options" => $getElementsList('homePageSpecialContentBlockElements', '.template-homepage-special-content-block')
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Container"),
                "options" => [
                    [
                        "id" => "homePageSpecialContentBlockContainerCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-homepage-special-content-block-container", "box-sizing:border-box;"],
                            ["selector", ".template-homepage-special-content-block-container"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-homepage-special-content-block-container"]
                        ],
                        "defaultValue" => [
                            "background-color" => "#111111"
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
                "id" => "contentCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                "cssOutput" => [
                    ["rule", ".template-content", "box-sizing:border-box;"],
                    ["selector", ".template-content"]
                ],
                "onCustomize" => [
                    ["updateRule", ".template-content"]
                ],
                "defaultValue" => [
                    "max-width" => "800px",
                    "margin-left" => "auto",
                    "margin-right" => "auto",
                    "padding-top" => "40px",
                    "padding-right" => "15px",
                    "padding-bottom" => "40px",
                    "padding-left" => "15px",
                    "min-height" => "300px"
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Elements"),
                "options" => $getElementsList('contentElements', '.template-content')
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Container"),
                "options" => [
                    [
                        "id" => "contentContainerCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-content-container", "box-sizing:border-box;"],
                            ["selector", ".template-content-container"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-content-container"]
                        ],
                        "defaultValue" => [
                            "background-color" => "#ffffff"
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "type" => "group",
        "name" => __("bearcms.themes.theme1.options.Footer"),
        "options" => [
            [
                "id" => "footerVisibility",
                "type" => "list",
                "name" => __("bearcms.themes.theme1.options.Visibility"),
                "defaultValue" => "1",
                "values" => [
                    [
                        "value" => "1",
                        "name" => __("bearcms.themes.theme1.options.Visible")
                    ],
                    [
                        "value" => "0",
                        "name" => __("bearcms.themes.theme1.options.Hidden")
                    ]
                ]
            ],
            [
                "id" => "footerCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                "cssOutput" => [
                    ["rule", ".template-footer", "box-sizing:border-box;"],
                    ["selector", ".template-footer"]
                ],
                "onCustomize" => [
                    ["updateRule", ".template-footer"]
                ],
                "defaultValue" => [
                    "max-width" => "800px",
                    "margin-left" => "auto",
                    "margin-right" => "auto",
                    "padding-top" => "40px",
                    "padding-right" => "15px",
                    "padding-bottom" => "40px",
                    "padding-left" => "15px"
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Elements"),
                "options" => $getElementsList('footerElements', '.template-footer')
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Powered by link"),
                "description" => "This link is located at the bottom of your website and shows your visitors you are proud of using Bear CMS",
                "options" => [
                    [
                        "id" => "poweredByLinkVisibility",
                        "type" => "list",
                        "name" => __("bearcms.themes.theme1.options.Visibility"),
                        "defaultValue" => "1",
                        "values" => [
                            [
                                "value" => "1",
                                "name" => __("bearcms.themes.theme1.options.Visible")
                            ],
                            [
                                "value" => "0",
                                "name" => __("bearcms.themes.theme1.options.Hidden")
                            ]
                        ]
                    ],
                    [
                        "id" => "poweredByLinkCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["rule", ".template-powered-by-link", "display:inline-block;"],
                            ["selector", ".template-powered-by-link"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-powered-by-link"]
                        ],
                        "defaultValue" => [
                            "color" => "#ffffff",
                            "text-decoration" => "none",
                            "text-decoration:hover" => "underline",
                            "font-family" => "Arial",
                            "font-size" => "14px",
                            "line-height" => "180%",
                            "margin-top" => "20px"
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Container"),
                        "options" => [
                            [
                                "id" => "poweredByLinkContainerCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                                "cssOutput" => [
                                    ["rule", ".template-powered-by-link-container", "box-sizing:border-box;"],
                                    ["selector", ".template-powered-by-link-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-powered-by-link-container"]
                                ],
                                "defaultValue" => [
                                    "text-align" => "center"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                "type" => "group",
                "name" => __("bearcms.themes.theme1.options.Container"),
                "options" => [
                    [
                        "id" => "footerContainerCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["rule", ".template-footer-container", "box-sizing:border-box;"],
                            ["selector", ".template-footer-container"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-footer-container"]
                        ],
                        "defaultValue" => [
                            "background-color" => "#111111"
                        ]
                    ]
                ]
            ]
        ]
    ],
    [
        "type" => "group",
        "name" => __("bearcms.themes.theme1.options.Window"),
        "options" => [
            [
                "id" => "bodyCSS",
                "type" => "css",
                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                "cssOutput" => [
                    ["selector", "body"]
                ],
                "onCustomize" => [
                    ["updateRule", "body"]
                ],
                "defaultValue" => [
                    "background-color" => "#111111"
                ]
            ]
        ]
    ],
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
                            ["rule", ".template-content .bearcms-blogpost-page-title", "font-weight:normal;"],
                            ["selector", ".template-content .bearcms-blogpost-page-title"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-content .bearcms-blogpost-page-title"]
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
                                    ["selector", ".template-content .bearcms-blogpost-page-title-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-blogpost-page-title-container"]
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
                        "id" => "blogPostPageDateCSS",
                        "type" => "css",
                        "cssOutput" => [
                            ["selector", ".template-content .bearcms-blogpost-page-date"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-content .bearcms-blogpost-page-date"]
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
                                    ["selector", ".template-content .bearcms-blogpost-page-date-container"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-blogpost-page-date-container"]
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
                            ["selector", ".template-content .bearcms-blogpost-page-content"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".template-content .bearcms-blogpost-page-content"]
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
                            ["rule", ".bearcms-new-forum-post-page-title-label", "display:block;"],
                            ["selector", ".bearcms-new-forum-post-page-title-label"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".bearcms-new-forum-post-page-title-label"]
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
                            ["selector", ".bearcms-new-forum-post-page-title"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".bearcms-new-forum-post-page-title"]
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
                        "onCustomize" => [
                            ["updateRule", ".bearcms-new-forum-post-page-text-label"]
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
                            ["selector", ".bearcms-new-forum-post-page-text"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".bearcms-new-forum-post-page-text"]
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
                        "onCustomize" => [
                            ["updateRule", ".bearcms-new-forum-post-page-send-button"]
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
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-new-forum-post-page-send-button-waiting"]
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
                "name" => __("bearcms.themes.theme1.options.forumPostPage.Reply"),
                "options" => [
                    [
                        "id" => "forumPostPageReplyCSS",
                        "type" => "css",
                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                        "cssOutput" => [
                            ["selector", ".bearcms-forum-post-page-reply"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".bearcms-forum-post-page-reply"]
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
                                "id" => "forumPostPageAuthorNameCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-author-name", "display:inline-block;"],
                                    ["selector", ".bearcms-forum-post-page-reply-author-name"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-reply-author-name"]
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
                                "id" => "forumPostPageAuthorImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-reply-author-image", "display:inline-block;float:left;"],
                                    ["selector", ".bearcms-forum-post-page-reply-author-image"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-reply-author-image"]
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
                                "id" => "forumPostPageDateCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-reply-date", "display:inline-block;float:right;"],
                                    ["selector", ".bearcms-forum-post-page-reply-date"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-reply-date"]
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
                                "id" => "forumPostPageTextCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".bearcms-forum-post-page-reply-text"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-reply-text"]
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
                                "id" => "forumPostPageTextLinksCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".bearcms-forum-post-page-reply-text a", "display:inline-block;"],
                                    ["selector", ".bearcms-forum-post-page-reply-text a"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-reply-text a"]
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
                            ["selector", ".bearcms-forum-post-page-text"]
                        ],
                        "onCustomize" => [
                            ["updateRule", ".bearcms-forum-post-page-text"]
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
                        "onCustomize" => [
                            ["updateRule", ".bearcms-forum-post-page-send-button"]
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
                                "onCustomize" => [
                                    ["updateRule", ".bearcms-forum-post-page-send-button-waiting"]
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
        "id" => "customCSS",
        "type" => "cssCode",
        "name" => __("bearcms.themes.theme1.options.Custom CSS")
    ]
];

$setDefaultValue = function($id, $defaultValue) use (&$options) {
    $updateOptions = function($options) use ($id, $defaultValue, &$updateOptions) {
        foreach ($options as $index => $option) {
            if (is_array($option)) {
                if (isset($option['id']) && $option['id'] === $id) {
                    $options[$index]['defaultValue'] = $defaultValue;
                    return $options;
                }
                if (isset($option['options'])) {
                    $options[$index]['options'] = $updateOptions($option['options']);
                }
            }
        }
        return $options;
    };
    $options = $updateOptions($options);
};

$setDefaultValue("footerElementsHeadingLargeCSS", [
    "color" => "#fff",
    "font-family" => "googlefonts:Open Sans",
    "font-size" => "28px",
    "text-align" => "center",
    "line-height" => "180%",
    "margin-top" => "0",
    "margin-right" => "0",
    "margin-bottom" => "0",
    "margin-left" => "0"
]);

$setDefaultValue("footerElementsTextCSS", [
    "color" => "#ffffff",
    "font-family" => "Arial",
    "font-size" => "14px",
    "line-height" => "180%"
]);


return $options;
