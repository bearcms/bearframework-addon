<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$options
        ->addOption("textColor", "color", __("bearcms.themes.themeone.options.Text color"), [
            "value" => '#000000'
        ])
        ->addOption("accentColor", "color", __("bearcms.themes.themeone.options.Accent color"), [
            "value" => '#058cc4'
        ])
        ->addOption("backgroundColor", "color", __("bearcms.themes.themeone.options.Background color"), [
            "value" => '#ffffff'
        ])
        ->addOption("textSize", "list", __("bearcms.themes.themeone.options.Text size"), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.themeone.options.Text small")
                ],
                [
                    "value" => "2",
                    "name" => __("bearcms.themes.themeone.options.Text normal")
                ],
                [
                    "value" => "3",
                    "name" => __("bearcms.themes.themeone.options.Text large")
                ]
            ],
            "value" => "2"
        ])
        ->addOption("contentWidth", "list", __("bearcms.themes.themeone.options.Content width"), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.themeone.options.Content small")
                ],
                [
                    "value" => "2",
                    "name" => __("bearcms.themes.themeone.options.Content normal")
                ],
                [
                    "value" => "3",
                    "name" => __("bearcms.themes.themeone.options.Content large")
                ]
            ],
            "value" => "2"
        ]);

$group = $options->addGroup(__("bearcms.themes.themeone.options.Header"));

$group->addOption("headerLogoImage", "image", __("bearcms.themes.themeone.options.Logo"));

$group->addGroup(__("bearcms.themes.themeone.options.Title"))
        ->addOption("headerTitleVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.themeone.options.Visible")
                ],
                [
                    "value" => "0",
                    "name" => __("bearcms.themes.themeone.options.Hidden")
                ]
            ],
            "value" => "1"
        ]);

$group->addGroup(__("bearcms.themes.themeone.options.Navigation"))
        ->addOption("navigationVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.themeone.options.Visible")
                ],
                [
                    "value" => "0",
                    "name" => __("bearcms.themes.themeone.options.Hidden")
                ]
            ],
            "value" => "1"
        ]);

$options->addGroup(__("bearcms.themes.themeone.options.Footer"))
        ->addOption("footerVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
            "values" => [
                [
                    "value" => "1",
                    "name" => __("bearcms.themes.themeone.options.Visible")
                ],
                [
                    "value" => "0",
                    "name" => __("bearcms.themes.themeone.options.Hidden")
                ]
            ],
            "value" => "1"
        ]);
