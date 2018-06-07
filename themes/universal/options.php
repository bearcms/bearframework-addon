<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

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
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Navigation"))
        ->add([
            "id" => "navigationVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Visibility"),
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
            "id" => "navigationPosition",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Position"),
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
            ]
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Item"))
                ->add([
                    "id" => "navigationItemCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item", "display:inline-block;"],
                        ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a", "display:block;white-space:nowrap;text-overflow:ellipsis;"],
                        ["rule", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a", "font-family:inherit;color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;text-decoration:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;text-shadow:inherit;"],
                        ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item > a"],
                        ["selector", ".template-navigation #template-navigation-toggle-button + label"],
                        ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                    ]
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Selected"))
                        ->add([
                            "id" => "navigationSelectedItemCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected > a"],
                                ["selector", ".template-navigation #template-navigation-toggle-button:checked + label"]
                            ]
                        ])
                )
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Subitem"))
                ->add([
                    "id" => "navigationSubitemCSS",
                    "type" => "css",
                    "cssTypes" => ["cssPadding", "cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                    "cssOutput" => [
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item", "display:block;"],
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "display:block;white-space:nowrap;text-overflow:ellipsis;"],
                        ["rule", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a", "font-family:inherit;color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;text-decoration:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;text-shadow:inherit;"],
                        ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item > a"]
                    ]
                ])
                ->add(
                        $options->makeGroup(__("bearcms.themes.universal.options.Selected"))
                        ->add([
                            "id" => "navigationSelectedSubitemCSS",
                            "type" => "css",
                            "cssTypes" => ["cssPadding", "cssText", "cssTextShadow", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize"],
                            "cssOutput" => [
                                ["selector", ".template-navigation .template-navigation-content .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected > a"]
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
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Home page special block"), "This content block is placed above the navigation on the home page. It is useful for welcoming your visitors with images, videos or text.")
        ->add([
            "id" => "homePageSpecialContentBlockVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Visibility"),
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
                    ]
                ])
);

$options->addGroup(__("bearcms.themes.universal.options.Footer"))
        ->add([
            "id" => "footerVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.universal.options.Visibility"),
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
            ]
        ])
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Elements"))
                ->addElements('footerElements', '.template-footer')
        )
        ->add(
                $options->makeGroup(__("bearcms.themes.universal.options.Powered by link"), "This link is located at the bottom of your website and shows your visitors you are proud of using BearCMS")
                ->add([
                    "id" => "poweredByLinkVisibility",
                    "type" => "list",
                    "name" => __("bearcms.themes.universal.options.Visibility"),
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
