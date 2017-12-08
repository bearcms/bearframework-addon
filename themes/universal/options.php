<?php

use BearFramework\App;

$app = App::get();

$options = $app->bearCMS->themes->makeOptionsDefinition();

$options->addGroup(__("bearcms.themes.universal.options.Header"))
        ->add([
            "id" => "headerCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOutput" => [
                ["rule", ".template-header", "box-sizing:border-box;"],
                ["selector", ".template-header"]
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
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Logo"))
                ->add([
                    "id" => "headerLogoImage",
                    "type" => "image",
                    "name" => __("bearcms.themes.universal.options.Image")
                ])
                ->add([
                    "id" => "headerLogoImageCSS",
                    "type" => "css",
                    "cssTypes" => ["cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-header-logo", "box-sizing:border-box;display:inline-block;overflow:hidden;font-size:0;"],
                        ["selector", ".template-header-logo"]
                    ],
                    "defaultValue" => [
                        "width" => "300px"
                    ]
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                        ->add([
                            "id" => "headerLogoImageContainerCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                            "cssOutput" => [
                                ["rule", ".template-header-logo-container", "box-sizing:border-box;"],
                                ["selector", ".template-header-logo-container"]
                            ],
                            "defaultValue" => [
                                "margin-bottom" => "40px",
                                "text-align" => "center"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Title"))
                ->add([
                    "id" => "headerTitleVisibility",
                    "type" => "list",
                    "name" => __("bearcms.themes.universal.options.Visibility"),
                    "defaultValue" => "1",
                    "values" => [
                        [
                            "value" => "1",
                            "name" => __("bearcms.themes.universal.options.Visible")
                        ],
                        [
                            "value" => "0",
                            "name" => __("bearcms.themes.universal.options.Hidden")
                        ]
                    ]
                ])
                ->add([
                    "id" => "headerTitleCSS",
                    "type" => "css",
                    "cssOutput" => [
                        ["rule", ".template-header-title", "display:inline-block;text-decoration:none;"],
                        ["selector", ".template-header-title"]
                    ],
                    "defaultValue" => [
                        "font-family" => "googlefonts:Open Sans",
                        "font-size" => "40px",
                        "font-weight" => "bold",
                        "color" => "#1BB0CE",
                        "color:hover" => "#1099B5",
                        "color:active" => "#0A7D94"
                    ]
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                        ->add([
                            "id" => "headerTitleContainerCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                            "cssOutput" => [
                                ["rule", ".template-header-title-container", "box-sizing:border-box;"],
                                ["selector", ".template-header-title-container"]
                            ],
                            "defaultValue" => [
                                "margin-bottom" => "40px",
                                "text-align" => "center"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Description"))
                ->add([
                    "id" => "headerDescriptionVisibility",
                    "type" => "list",
                    "name" => __("bearcms.themes.universal.options.Visibility"),
                    "defaultValue" => "1",
                    "values" => [
                        [
                            "value" => "1",
                            "name" => __("bearcms.themes.universal.options.Visible")
                        ],
                        [
                            "value" => "0",
                            "name" => __("bearcms.themes.universal.options.Hidden")
                        ]
                    ]
                ])
                ->add([
                    "id" => "headerDescriptionCSS",
                    "type" => "css",
                    "cssTypes" => ["cssText", "cssTextShadow", "cssBackground", "cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-header-description", "box-sizing:border-box;display:inline-block;"],
                        ["selector", ".template-header-description"]
                    ],
                    "defaultValue" => [
                        "color" => "#666666",
                        "font-family" => "googlefonts:Open Sans",
                        "font-size" => "15px"
                    ]
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                        ->add([
                            "id" => "headerDescriptionContainerCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                            "cssOutput" => [
                                ["rule", ".template-header-description-container", "box-sizing:border-box;"],
                                ["selector", ".template-header-description-container"]
                            ],
                            "defaultValue" => [
                                "margin-bottom" => "40px",
                                "text-align" => "center"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                ->add([
                    "id" => "headerContainerCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-header-container", "box-sizing:border-box;"],
                        ["selector", ".template-header-container"]
                    ],
                    "defaultValue" => [
                        "background-color" => "#ffffff"
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Navigation"))
        ->add([
            "id" => "navigationPosition",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Position"),
            "defaultValue" => "2",
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.universal.options.Top")
                ],
                [
                    "value" => "2",
                    "name" => __("bearcms.themes.universal.options.Bottom")
                ]
            ]
        ])
        ->add([
            "id" => "navigationCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
            "cssOutput" => [
                ["rule", ".template-navigation", "box-sizing:border-box;"],
                ["selector", ".template-navigation"]
            ],
            "defaultValue" => [
                "max-width" => "800px",
                "margin-left" => "auto",
                "margin-right" => "auto",
                "padding-right" => "15px",
                "padding-left" => "15px",
                "text-align" => "center"
            ]
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Item"))
                ->add([
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
                    "defaultValue" => [
                        "line-height" => "44px",
                        "padding-left" => "16px",
                        "padding-right" => "16px",
                        "height" => "44px"
                    ]
                ])
                ->add([
                    "id" => "navigationItemCSS2",
                    "type" => "css",
                    "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item"],
                        ["selector", ".template-navigation #template-navigation-toggle-button + label"],
                        ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
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
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Selected"))
                        ->add([
                            "id" => "navigationSelectedItemCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected > a"],
                                ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                            ]
                        ])
                        ->add([
                            "id" => "navigationSelectedItemCSS2",
                            "type" => "css",
                            "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected"],
                                ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                            ],
                            "defaultValue" => [
                                "background-color" => "#1BB0CE"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Subitem"))
                ->add([
                    "id" => "navigationSubitemCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding"],
                    "cssOutput" => [
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item", "display:block;"],
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "display:block;white-space:nowrap;text-overflow:ellipsis;"],
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "font-family:inherit;color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;text-decoration:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;text-shadow:inherit;"],
                        ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a"]
                    ],
                    "defaultValue" => [
                        "line-height" => "44px",
                        "padding-left" => "16px",
                        "padding-right" => "16px",
                        "height" => "44px"
                    ]
                ])
                ->add([
                    "id" => "navigationSubitemCSS2",
                    "type" => "css",
                    "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item"]
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
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Selected"))
                        ->add([
                            "id" => "navigationSelectedSubitemCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected > a"]
                            ]
                        ])
                        ->add([
                            "id" => "navigationSelectedSubitemCSS2",
                            "type" => "css",
                            "cssTypes" => ["cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected"]
                            ],
                            "defaultValue" => [
                                "background-color" => "#1BB0CE"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                ->add([
                    "id" => "navigationContainerCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-navigation-container", "box-sizing:border-box;"],
                        ["selector", ".template-navigation-container"]
                    ],
                    "defaultValue" => [
                        "background-color" => "#333333"
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Home page special block"), "This content block is placed above the navigation on the home page. It is useful for welcoming your visitors with images, videos or text.")
        ->add([
            "id" => "homePageSpecialContentBlockVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Visibility"),
            "defaultValue" => "1",
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.universal.options.Visible")
                ],
                [
                    "value" => "0",
                    "name" => __("bearcms.themes.universal.options.Hidden")
                ]
            ]
        ])
        ->add([
            "id" => "homePageSpecialContentBlockCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOutput" => [
                ["rule", ".template-homepage-special-content-block", "box-sizing:border-box;"],
                ["selector", ".template-homepage-special-content-block"]
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
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Elements"))
                ->addElements('homePageSpecialContentBlockElements', '.template-homepage-special-content-block')
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                ->add([
                    "id" => "homePageSpecialContentBlockContainerCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-homepage-special-content-block-container", "box-sizing:border-box;"],
                        ["selector", ".template-homepage-special-content-block-container"]
                    ],
                    "defaultValue" => [
                        "background-color" => "#111111"
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Content"))
        ->add([
            "id" => "contentCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOutput" => [
                ["rule", ".template-content", "box-sizing:border-box;"],
                ["selector", ".template-content"]
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
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Elements"))
                ->addElements('contentElements', '.template-content')
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                ->add([
                    "id" => "contentContainerCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-content-container", "box-sizing:border-box;"],
                        ["selector", ".template-content-container"]
                    ],
                    "defaultValue" => [
                        "background-color" => "#ffffff"
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Footer"))
        ->add([
            "id" => "footerVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Visibility"),
            "defaultValue" => "1",
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.universal.options.Visible")
                ],
                [
                    "value" => "0",
                    "name" => __("bearcms.themes.universal.options.Hidden")
                ]
            ]
        ])
        ->add([
            "id" => "footerCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
            "cssOutput" => [
                ["rule", ".template-footer", "box-sizing:border-box;"],
                ["selector", ".template-footer"]
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
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Elements"))
                ->addElements('footerElements', '.template-footer')
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Powered by link"), "This link is located at the bottom of your website and shows your visitors you are proud of using Bear CMS")
                ->add([
                    "id" => "poweredByLinkVisibility",
                    "type" => "list",
                    "name" => __("bearcms.themes.universal.options.Visibility"),
                    "defaultValue" => "1",
                    "values" => [
                        [
                            "value" => "1",
                            "name" => __("bearcms.themes.universal.options.Visible")
                        ],
                        [
                            "value" => "0",
                            "name" => __("bearcms.themes.universal.options.Hidden")
                        ]
                    ]
                ])
                ->add([
                    "id" => "poweredByLinkCSS",
                    "type" => "css",
                    "cssOutput" => [
                        ["rule", ".template-powered-by-link", "display:inline-block;"],
                        ["selector", ".template-powered-by-link"]
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
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                        ->add([
                            "id" => "poweredByLinkContainerCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"],
                            "cssOutput" => [
                                ["rule", ".template-powered-by-link-container", "box-sizing:border-box;"],
                                ["selector", ".template-powered-by-link-container"]
                            ],
                            "defaultValue" => [
                                "text-align" => "center"
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Container"))
                ->add([
                    "id" => "footerContainerCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-footer-container", "box-sizing:border-box;"],
                        ["selector", ".template-footer-container"]
                    ],
                    "defaultValue" => [
                        "background-color" => "#111111"
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Window"))
        ->add([
            "id" => "bodyCSS",
            "type" => "css",
            "cssTypes" => ["cssPadding", "cssBorder", "cssRadius", "cssShadow", "cssBackground"],
            "cssOutput" => [
                ["selector", "body"]
            ],
            "defaultValue" => [
                "background-color" => "#111111"
            ]
        ]);

$options->addGroup(__('bearcms.themes.universal.Pages'))
        ->addPages();

$options->addCustomCSS();

return $options;
//
//$options->addGroup('Elements')
//        ->addElements('Content', '.parent-css');
//
//$options->addPages();
//$options->addCustomCSS();
//
//$options->setDefaultValue('forumPostPageTextInputCSS', [
//    "color" => "#000000"
//]);
//$options->setDefaultValues(['customCSS' => 'custom!!!!']);
