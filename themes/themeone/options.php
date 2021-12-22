<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */


$group = $options->addGroup(__("bearcms.themes.themeone.options.Logo"));
$group2 = $group->addGroup(__("bearcms.themes.themeone.options.Logo.Image"));
$group2
    ->addOption("logoImage", "image", __("bearcms.themes.themeone.options.Logo.Image.File"), [
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-logo"]
        ]
    ])
    ->addOption("logoImageWidth", "htmlUnit", __("bearcms.themes.themeone.options.Logo.Image.Width"), [
        "value" => "200px",
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-logo"]
        ]
    ])
    ->addOption("logoImageEffect", "list", __("bearcms.themes.themeone.options.Logo.Image.Effect"), [
        "values" => [
            [
                "value" => "0",
                "name" => __("bearcms.themes.themeone.options.Logo.Image.Effect.None")
            ],
            [
                "value" => "1",
                "name" => __("bearcms.themes.themeone.options.Logo.Image.Effect.Circle")
            ]
        ],
        "value" => "0",
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-logo"]
        ]
    ]);
$group2 = $group->addGroup(__("bearcms.themes.themeone.options.Logo.Text"))
    ->addOption("logoTextVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
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
    ])
    ->addOption("logoTextCSS", "cssText", __("bearcms.themes.themeone.options.Logo.Text.Style"), [
        "cssOptions" => ["cssTextStateDefault", "cssTextFontFamily", "cssTextColor", "cssTextFontWeight", "cssTextFontStyle", "cssTextFontSize", "cssTextLineHeight", "cssTextLetterSpacing"],
        "cssOutput" => [
            ["rule", ".bearcms-template-logo-text", "text-decoration:none;"],
            ["selector", ".bearcms-template-logo-text"],
            ["selector", ".bearcms-template-inner-page-logo-text", "font-size:calc({cssPropertyValue(font-size)} * 6/8);"]
        ],
        "value" => '{"color":"#000000","font-size":"26px","font-family":"Arial"}'
    ]);

$group2 = $options->addGroup(__("bearcms.themes.themeone.options.Navigation"));
$group2
    ->addOption("navigationVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
        "values" => [
            [
                "value" => "1",
                "name" => __("bearcms.themes.themeone.options.Visible2")
            ],
            [
                "value" => "0",
                "name" => __("bearcms.themes.themeone.options.Hidden2")
            ]
        ],
        "value" => "1",
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-navigation"]
        ]
    ]);

$group3 = $group2->addGroup(__("bearcms.themes.themeone.options.SearchButton"));
$group3
    ->addOption("searchButtonVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
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
        "value" => "1",
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-navigation-custom-item-search"]
        ]
    ]);

$group3 = $group2->addGroup(__("bearcms.themes.themeone.options.StoreCartButton"));
$group3
    ->addOption("storeCartButtonVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
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
        "value" => "1",
        "onHighlight" => [
            ["cssSelector", ".bearcms-template-navigation-custom-item-store-cart"]
        ]
    ]);

$options
    ->addOption("textCSS", "cssText", __("bearcms.themes.themeone.options.DefaultText"), [
        "cssOptions" => ["cssTextStateDefault", "cssTextFontFamily", "cssTextColor", "cssTextFontWeight", "cssTextFontStyle", "cssTextFontSize", "cssTextLineHeight", "cssTextLetterSpacing"],
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-text-font-family:{cssPropertyValue(font-family)};"],
            ["selector", ":root", "--bearcms-template-text-color:{cssPropertyValue(color)};"],
            ["selector", ":root", "--bearcms-template-text-font-weight:{cssPropertyValue(font-weight)};"],
            ["selector", ":root", "--bearcms-template-text-font-style:{cssPropertyValue(font-style)};"],
            ["selector", ":root", "--bearcms-template-text-font-size:{cssPropertyValue(font-size)};"],
            ["selector", ":root", "--bearcms-template-text-line-height:{cssPropertyValue(line-height)};"],
            ["selector", ":root", "--bearcms-template-text-letter-spacing:{cssPropertyValue(letter-spacing)};"],
            ["selector", ".bearcms-template-navigation-menu-button-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M6 7h12M6 12h12M6 17h12"/></svg>') . '\');'],
            ["selector", ".bearcms-template-navigation-custom-item-search-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", ".bearcms-template-navigation-custom-item-store-cart-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="19" r="1"/><circle cx="18" cy="19" r="1"/><path d="M6 6H3"/></svg>') . '\');'],
            ["selector", ".bearcms-template-context .bearcms-search-box-element-button", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", ".bearcms-template-context .allebg-poll-element-answer-checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M4 13l5 5L20 7"/></svg>') . '\');'] // Temp (remove in the future)
        ],
        "value" => '{"color":"#000000","font-size":"16px","font-family":"Arial","line-height":"180%"}'
    ]);

$options
    ->addOption("accentTextCSS", "cssText", __("bearcms.themes.themeone.options.AccentText"), [
        "cssOptions" => ["cssTextStateDefault", "cssTextFontFamily", "cssTextColor", "cssTextFontWeight", "cssTextFontStyle", "cssTextFontSize", "cssTextLineHeight", "cssTextLetterSpacing"],
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-accent-text-font-family:{cssPropertyValue(font-family)};"],
            ["selector", ":root", "--bearcms-template-accent-text-color:{cssPropertyValue(color)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-weight:{cssPropertyValue(font-weight)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-style:{cssPropertyValue(font-style)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-size:{cssPropertyValue(font-size)};"],
            ["selector", ":root", "--bearcms-template-accent-text-line-height:{cssPropertyValue(line-height)};"],
            ["selector", ":root", "--bearcms-template-accent-text-letter-spacing:{cssPropertyValue(letter-spacing)};"],
        ],
        "value" => '{"color":"#000000","font-size":"16px","font-family":"Arial","line-height":"170%"}'
    ]);

$options
    ->addOption("backgroundCSS", "cssBackground", __("bearcms.themes.themeone.options.Background"), [
        "cssOptions" => ["cssBackgroundStateDefault"],
        "cssOutput" => [
            ["selector", ".bearcms-template-container"]
        ],
        "value" => '{"background-color":"#ffffff"}'
    ])
    ->addOption("contentWidth", "list", __("bearcms.themes.themeone.options.ContentWidth"), [
        "values" => [
            [
                "value" => "1",
                "name" => __("bearcms.themes.themeone.options.ContentWidth.Small")
            ],
            [
                "value" => "2",
                "name" => __("bearcms.themes.themeone.options.ContentWidth.Normal")
            ],
            [
                "value" => "3",
                "name" => __("bearcms.themes.themeone.options.ContentWidth.Large")
            ]
        ],
        "value" => "2"
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
    ])
    ->addOption("footerBackgroundCSS", "cssBackground", __("bearcms.themes.themeone.options.Background"), [
        "cssOptions" => ["cssBackgroundStateDefault"],
        "cssOutput" => [
            ["selector", ".bearcms-template-footer"],
            ["selector", ":root", "--bearcms-template-footer-background-color:{cssPropertyValue(background-color)};"]
        ],
        "value" => '{"background-color":"#111111"}'
    ])
    ->addOption("footerTextColor", "color", __("bearcms.themes.themeone.options.Text color"), [
        "value" => '#ffffff',
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-footer-text-color:{value};"],
            ["selector", ".bearcms-template-footer .bearcms-search-box-element-button", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{value}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", ".bearcms-template-footer .allebg-poll-element-answer-checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{value}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M4 13l5 5L20 7"/></svg>') . '\');'] // Temp (remove in the future)
        ]
    ]);
