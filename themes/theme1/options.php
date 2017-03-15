<?php

return [
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
                            "padding-top" => "15px",
                            "padding-right" => "15px",
                            "padding-bottom" => "15px",
                            "padding-left" => "15px"
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
                            "padding-top" => "15px",
                            "padding-right" => "15px",
                            "padding-bottom" => "15px",
                            "padding-left" => "15px"
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
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Heading"),
                        "options" => [
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Large"),
                                "options" => [
                                    [
                                        "id" => "homePageSpecialContentBlockElementsHeadingLargeCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-heading-element-large", "font-weight:normal;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-heading-element-large"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-heading-element-large"]
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
                                        "id" => "homePageSpecialContentBlockElementsHeadingMediumCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-heading-element-medium", "font-weight:normal;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-heading-element-medium"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-heading-element-medium"]
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
                                        "id" => "homePageSpecialContentBlockElementsHeadingSmallCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-heading-element-small", "font-weight:normal;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-heading-element-small"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-heading-element-small"]
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
                                "id" => "homePageSpecialContentBlockElementsTextCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-homepage-special-content-block .bearcms-text-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-text-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
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
                                        "id" => "homePageSpecialContentBlockElementsTextLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-text-element a", "display:inline-block;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-text-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-text-element a"]
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
                                "id" => "homePageSpecialContentBlockElementsLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".template-homepage-special-content-block .bearcms-link-element", "display:inline-block;text-decoration:none;"],
                                    ["selector", ".template-homepage-special-content-block .bearcms-link-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-link-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "padding-top" => "15px",
                                    "padding-right" => "15px",
                                    "padding-bottom" => "15px",
                                    "padding-left" => "15px",
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
                                "id" => "homePageSpecialContentBlockElementsImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-homepage-special-content-block .bearcms-image-element", "overflow:hidden;"],
                                    ["selector", ".template-homepage-special-content-block .bearcms-image-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-image-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Image gallery"),
                        "options" => [
                            [
                                "id" => "homePageSpecialContentBlockElementsImageGalleryCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-homepage-special-content-block .bearcms-image-gallery-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-image-gallery-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Image"),
                                "options" => [
                                    [
                                        "id" => "homePageSpecialContentBlockElementsImageGalleryImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-image-gallery-element-image"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-image-gallery-element-image"]
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
                                "id" => "homePageSpecialContentBlockElementsVideoCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-homepage-special-content-block .bearcms-video-element", "overflow:hidden;"],
                                    ["selector", ".template-homepage-special-content-block .bearcms-video-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-video-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Navigation"),
                        "options" => [
                            [
                                "id" => "homePageSpecialContentBlockElementsNavigationCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-homepage-special-content-block .bearcms-navigation-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-navigation-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Elements"),
                                "options" => [
                                    [
                                        "id" => "homePageSpecialContentBlockElementsNavigationItemLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-navigation-element-item a", "display:inline-block;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-navigation-element-item a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-navigation-element-item a"]
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
                        "name" => __("bearcms.themes.theme1.options.HTML code"),
                        "options" => [
                            [
                                "id" => "homePageSpecialContentBlockElementsHtmlCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-homepage-special-content-block .bearcms-html-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-html-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
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
                                        "id" => "homePageSpecialContentBlockElementsHtmlLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-homepage-special-content-block .bearcms-html-element a", "display:inline-block;"],
                                            ["selector", ".template-homepage-special-content-block .bearcms-html-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-html-element a"]
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
                                "id" => "homePageSpecialContentBlockElementsBlogPostsCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Post"),
                                "options" => [
                                    [
                                        "id" => "homePageSpecialContentBlockElementsBlogPostsPostCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                                        "cssOutput" => [
                                            ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post"]
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.theme1.options.Title"),
                                        "options" => [
                                            [
                                                "id" => "homePageSpecialContentBlockElementsBlogPostsPostTitleCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-title"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-title"]
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
                                                        "id" => "homePageSpecialContentBlockElementsBlogPostsPostTitleContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-title-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-title-container"]
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
                                                "id" => "homePageSpecialContentBlockElementsBlogPostsPostDateCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-date"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-date"]
                                                ],
                                                "defaultValue" => [
                                                    "color" => "#eeeeee",
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
                                                        "id" => "homePageSpecialContentBlockElementsBlogPostsPostDateContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-date-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-date-container"]
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
                                                "id" => "homePageSpecialContentBlockElementsBlogPostsPostContentCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-content"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-homepage-special-content-block .bearcms-blog-posts-element-post-content"]
                                                ]
                                            ]
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
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Heading"),
                        "options" => [
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Large"),
                                "options" => [
                                    [
                                        "id" => "elementsHeadingLargeCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-heading-element-large", "font-weight:normal;"],
                                            ["selector", ".template-content .bearcms-heading-element-large"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-heading-element-large"]
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
                                        "id" => "elementsHeadingMediumCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-heading-element-medium", "font-weight:normal;"],
                                            ["selector", ".template-content .bearcms-heading-element-medium"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-heading-element-medium"]
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
                                        "id" => "elementsHeadingSmallCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-heading-element-small", "font-weight:normal;"],
                                            ["selector", ".template-content .bearcms-heading-element-small"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-heading-element-small"]
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
                                "id" => "elementsTextCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-content .bearcms-text-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-text-element"]
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
                                        "id" => "elementsTextLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-text-element a", "display:inline-block;"],
                                            ["selector", ".template-content .bearcms-text-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-text-element a"]
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
                                "id" => "elementsLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".template-content .bearcms-link-element", "display:inline-block;text-decoration:none;"],
                                    ["selector", ".template-content .bearcms-link-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-link-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "padding-top" => "15px",
                                    "padding-right" => "15px",
                                    "padding-bottom" => "15px",
                                    "padding-left" => "15px",
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
                                "id" => "elementsImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-content .bearcms-image-element", "overflow:hidden;"],
                                    ["selector", ".template-content .bearcms-image-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-image-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Image gallery"),
                        "options" => [
                            [
                                "id" => "elementsImageGalleryCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-content .bearcms-image-gallery-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-image-gallery-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Image"),
                                "options" => [
                                    [
                                        "id" => "elementsImageGalleryImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                            ["selector", ".template-content .bearcms-image-gallery-element-image"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-image-gallery-element-image"]
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
                                "id" => "elementsVideoCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-content .bearcms-video-element", "overflow:hidden;"],
                                    ["selector", ".template-content .bearcms-video-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-video-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Navigation"),
                        "options" => [
                            [
                                "id" => "elementsNavigationCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-content .bearcms-navigation-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-navigation-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Elements"),
                                "options" => [
                                    [
                                        "id" => "elementsNavigationItemLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-navigation-element-item a", "display:inline-block;"],
                                            ["selector", ".template-content .bearcms-navigation-element-item a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-navigation-element-item a"]
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
                        "name" => __("bearcms.themes.theme1.options.HTML code"),
                        "options" => [
                            [
                                "id" => "elementsHtmlCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-content .bearcms-html-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-html-element"]
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
                                        "id" => "elementsHtmlLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-content .bearcms-html-element a", "display:inline-block;"],
                                            ["selector", ".template-content .bearcms-html-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-html-element a"]
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
                                "id" => "elementsBlogPostsCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-content .bearcms-blog-posts-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-content .bearcms-blog-posts-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Post"),
                                "options" => [
                                    [
                                        "id" => "elementsBlogPostsPostCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                                        "cssOutput" => [
                                            ["selector", ".template-content .bearcms-blog-posts-element-post"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-content .bearcms-blog-posts-element-post"]
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.theme1.options.Title"),
                                        "options" => [
                                            [
                                                "id" => "elementsBlogPostsPostTitleCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-content .bearcms-blog-posts-element-post-title"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-content .bearcms-blog-posts-element-post-title"]
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
                                                        "id" => "elementsBlogPostsPostTitleContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-content .bearcms-blog-posts-element-post-title-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-content .bearcms-blog-posts-element-post-title-container"]
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
                                                "id" => "elementsBlogPostsPostDateCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-content .bearcms-blog-posts-element-post-date"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-content .bearcms-blog-posts-element-post-date"]
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
                                                        "id" => "elementsBlogPostsPostDateContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-content .bearcms-blog-posts-element-post-date-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-content .bearcms-blog-posts-element-post-date-container"]
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
                                                "id" => "elementsBlogPostsPostContentCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", ".template-content .bearcms-blog-posts-element-post-content"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-content .bearcms-blog-posts-element-post-content"]
                                                ]
                                            ]
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
                "options" => [
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Heading"),
                        "options" => [
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Large"),
                                "options" => [
                                    [
                                        "id" => "footerElementsHeadingLargeCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-heading-element-large", "font-weight:normal;"],
                                            ["selector", ".template-footer .bearcms-heading-element-large"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-heading-element-large"]
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
                                        "id" => "footerElementsHeadingMediumCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-heading-element-medium", "font-weight:normal;"],
                                            ["selector", ".template-footer .bearcms-heading-element-medium"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-heading-element-medium"]
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
                                        "id" => "footerElementsHeadingSmallCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-heading-element-small", "font-weight:normal;"],
                                            ["selector", ".template-footer .bearcms-heading-element-small"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-heading-element-small"]
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
                                "id" => "footerElementsTextCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-footer .bearcms-text-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-text-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
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
                                        "id" => "footerElementsTextLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-text-element a", "display:inline-block;"],
                                            ["selector", ".template-footer .bearcms-text-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-text-element a"]
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
                                "id" => "footerElementsLinkCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["rule", ".template-footer .bearcms-link-element", "display:inline-block;text-decoration:none;"],
                                    ["selector", ".template-footer .bearcms-link-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-link-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
                                    "font-family" => "Arial",
                                    "font-size" => "14px",
                                    "padding-top" => "15px",
                                    "padding-right" => "15px",
                                    "padding-bottom" => "15px",
                                    "padding-left" => "15px",
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
                                "id" => "footerElementsImageCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-footer .bearcms-image-element", "overflow:hidden;"],
                                    ["selector", ".template-footer .bearcms-image-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-image-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Image gallery"),
                        "options" => [
                            [
                                "id" => "footerElementsImageGalleryCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-footer .bearcms-image-gallery-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-image-gallery-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Image"),
                                "options" => [
                                    [
                                        "id" => "footerElementsImageGalleryImageCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-image-gallery-element-image", "overflow:hidden;"],
                                            ["selector", ".template-footer .bearcms-image-gallery-element-image"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-image-gallery-element-image"]
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
                                "id" => "footerElementsVideoCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssRadius", "cssShadow"],
                                "cssOutput" => [
                                    ["rule", ".template-footer .bearcms-video-element", "overflow:hidden;"],
                                    ["selector", ".template-footer .bearcms-video-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-video-element"]
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "group",
                        "name" => __("bearcms.themes.theme1.options.Navigation"),
                        "options" => [
                            [
                                "id" => "footerElementsNavigationCSS",
                                "type" => "css",
                                "cssTypes" => ["cssBorder", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-footer .bearcms-navigation-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-navigation-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Elements"),
                                "options" => [
                                    [
                                        "id" => "footerElementsNavigationItemLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-navigation-element-item a", "display:inline-block;"],
                                            ["selector", ".template-footer .bearcms-navigation-element-item a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-navigation-element-item a"]
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
                        "name" => __("bearcms.themes.theme1.options.HTML code"),
                        "options" => [
                            [
                                "id" => "footerElementsHtmlCSS",
                                "type" => "css",
                                "cssOutput" => [
                                    ["selector", ".template-footer .bearcms-html-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-html-element"]
                                ],
                                "defaultValue" => [
                                    "color" => "#ffffff",
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
                                        "id" => "footerElementsHtmlLinkCSS",
                                        "type" => "css",
                                        "cssOutput" => [
                                            ["rule", ".template-footer .bearcms-html-element a", "display:inline-block;"],
                                            ["selector", ".template-footer .bearcms-html-element a"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-html-element a"]
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
                                "id" => "footerElementsBlogPostsCSS",
                                "type" => "css",
                                "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
                                "cssOutput" => [
                                    ["selector", ".template-footer .bearcms-blog-posts-element"]
                                ],
                                "onCustomize" => [
                                    ["updateRule", ".template-footer .bearcms-blog-posts-element"]
                                ]
                            ],
                            [
                                "type" => "group",
                                "name" => __("bearcms.themes.theme1.options.Post"),
                                "options" => [
                                    [
                                        "id" => "footerElementsBlogPostsPostCSS",
                                        "type" => "css",
                                        "cssTypes" => ["cssBorder", "cssBackground", "cssShadow"],
                                        "cssOutput" => [
                                            ["selector", ".template-footer .bearcms-blog-posts-element-post"]
                                        ],
                                        "onCustomize" => [
                                            ["updateRule", ".template-footer .bearcms-blog-posts-element-post"]
                                        ]
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => __("bearcms.themes.theme1.options.Title"),
                                        "options" => [
                                            [
                                                "id" => "footerElementsBlogPostsPostTitleCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-footer .bearcms-blog-posts-element-post-title"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-footer .bearcms-blog-posts-element-post-title"]
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
                                                        "id" => "footerElementsBlogPostsPostTitleContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-footer .bearcms-blog-posts-element-post-title-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-footer .bearcms-blog-posts-element-post-title-container"]
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
                                                "id" => "footerElementsBlogPostsPostDateCSS",
                                                "type" => "css",
                                                "cssOutput" => [
                                                    ["selector", ".template-footer .bearcms-blog-posts-element-post-date"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-footer .bearcms-blog-posts-element-post-date"]
                                                ],
                                                "defaultValue" => [
                                                    "color" => "#eeeeee",
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
                                                        "id" => "footerElementsBlogPostsPostDateContainerCSS",
                                                        "type" => "css",
                                                        "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                        "cssOutput" => [
                                                            ["selector", ".template-footer .bearcms-blog-posts-element-post-date-container"]
                                                        ],
                                                        "onCustomize" => [
                                                            ["updateRule", ".template-footer .bearcms-blog-posts-element-post-date-container"]
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
                                                "id" => "footerElementsBlogPostsPostContentCSS",
                                                "type" => "css",
                                                "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                                                "cssOutput" => [
                                                    ["selector", ".template-footer .bearcms-blog-posts-element-post-content"]
                                                ],
                                                "onCustomize" => [
                                                    ["updateRule", ".template-footer .bearcms-blog-posts-element-post-content"]
                                                ]
                                            ]
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
                        "id" => "elementsBlogPostPageTitleCSS",
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
                                "id" => "elementsBlogPostPageTitleContainerCSS",
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
                        "id" => "elementsBlogPostPageDateCSS",
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
                                "id" => "elementsBlogPostPageDateContainerCSS",
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
                        "id" => "elementsBlogPostPageContentCSS",
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
        "id" => "customCSS",
        "type" => "cssCode",
        "name" => __("bearcms.themes.theme1.options.Custom CSS")
    ]
];
