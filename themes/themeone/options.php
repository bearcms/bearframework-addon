<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$schema = $app->bearCMS->themes->makeOptionsSchema();

$schema
        ->addOption("textColor", "color", __("bearcms.themes.themeone.options.Text color"), [
            "defaultValue" => '#000000'
        ])
        ->addOption("accentColor", "color", __("bearcms.themes.themeone.options.Accent color"), [
            "defaultValue" => '#058cc4'
        ])
        ->addOption("backgroundColor", "color", __("bearcms.themes.themeone.options.Background color"), [
            "defaultValue" => '#ffffff'
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
            "defaultValue" => "2"
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
            "defaultValue" => "2"
        ]);

$group = $schema->addGroup(__("bearcms.themes.themeone.options.Header"));

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
            "defaultValue" => "1"
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
            "defaultValue" => "1"
        ]);

$schema->addGroup(__("bearcms.themes.themeone.options.Footer"))
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
            "defaultValue" => "1"
        ]);

return $schema;
