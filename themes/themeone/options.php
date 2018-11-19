<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$options = $app->bearCMS->themes->makeOptionsDefinition();


$options->add([
    "id" => "textColor",
    "type" => "color",
    "name" => __("bearcms.themes.themeone.options.Text color"),
    "defaultValue" => '#000000'
]);
$options->add([
    "id" => "accentColor",
    "type" => "color",
    "name" => __("bearcms.themes.themeone.options.Accent color"),
    "defaultValue" => '#058cc4'
]);
$options->add([
    "id" => "backgroundColor",
    "type" => "color",
    "name" => __("bearcms.themes.themeone.options.Background color"),
    "defaultValue" => '#ffffff'
]);
$options->add([
    "id" => "textSize",
    "type" => "list",
    "name" => __("bearcms.themes.themeone.options.Text size"),
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
]);
$options->add([
    "id" => "contentWidth",
    "type" => "list",
    "name" => __("bearcms.themes.themeone.options.Content width"),
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

$group = $options->addGroup(__("bearcms.themes.themeone.options.Header"));

$group->add([
    "id" => "headerLogoImage",
    "type" => "image",
    "name" => __("bearcms.themes.themeone.options.Logo")
]);

$group->addGroup(__("bearcms.themes.themeone.options.Title"))
        ->add([
            "id" => "headerTitleVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.themeone.options.Visibility"),
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
        ->add([
            "id" => "navigationVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.themeone.options.Visibility"),
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

$options->addGroup(__("bearcms.themes.themeone.options.Footer"))
        ->add([
            "id" => "footerVisibility",
            "type" => "list",
            "name" => __("bearcms.themes.themeone.options.Visibility"),
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

return $options;
