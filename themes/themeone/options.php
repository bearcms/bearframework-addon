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
            ["cssSelector", "body .bearcms-template-logo"]
        ]
    ])
    ->addOption("logoImageWidth", "htmlUnit", __("bearcms.themes.themeone.options.Logo.Image.Width"), [
        "defaultValue" => "200px",
        "onHighlight" => [
            ["cssSelector", "body .bearcms-template-logo"]
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
        "defaultValue" => "0",
        "onHighlight" => [
            ["cssSelector", "body .bearcms-template-logo"]
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
        "defaultValue" => "1"
    ])
    ->addOption("logoTextCSS", "cssText", __("bearcms.themes.themeone.options.Logo.Text.Style"), [
        "cssOptions" => ["cssText/defaultState", "cssText/fontFamilyProperty", "cssText/colorProperty", "cssText/fontWeightProperty", "cssText/fontStyleProperty", "cssText/fontSizeProperty", "cssText/lineHeightProperty", "cssText/letterSpacingProperty"],
        "cssOutput" => [
            ["rule", "body .bearcms-template-logo-text", "text-decoration:none;"],
            ["selector", "body .bearcms-template-logo-text"],
            ["selector", "body .bearcms-template-inner-page-logo-text", "font-size:calc({cssPropertyValue(font-size)} * 6/8);"]
        ],
        "defaultValue" => '{"color":"#000000","font-size":"26px","font-family":"Arial"}'
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
        "defaultValue" => "1",
        "onHighlight" => [
            ["cssSelector", "body .bearcms-template-navigation"]
        ]
    ]);

$group3 = $group2->addGroup(__("bearcms.themes.themeone.options.SearchButton"));
$group3
    ->addOption("searchButtonVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
        "values" => [
            [
                "value" => "auto",
                "name" => __("bearcms.themes.themeone.options.Auto")
            ],
            [
                "value" => "1",
                "name" => __("bearcms.themes.themeone.options.Visible")
            ],
            [
                "value" => "0",
                "name" => __("bearcms.themes.themeone.options.Hidden")
            ]
        ],
        "defaultValue" => "auto",
        "onHighlight" => [
            ["cssSelector", "body .bearcms-template-navigation-custom-item-search"]
        ]
    ]);

$group3 = $group2->addGroup(__("bearcms.themes.themeone.options.StoreCartButton"));
$group3
    ->addOption("storeCartButtonVisibility", "list", __("bearcms.themes.themeone.options.Visibility"), [
        "values" => [
            [
                "value" => "auto",
                "name" => __("bearcms.themes.themeone.options.Auto")
            ],
            [
                "value" => "1",
                "name" => __("bearcms.themes.themeone.options.Visible")
            ],
            [
                "value" => "0",
                "name" => __("bearcms.themes.themeone.options.Hidden")
            ]
        ],
        "defaultValue" => "auto",
        "onHighlight" => [
            ["cssSelector", "body .bearcms-template-navigation-custom-item-store-cart"]
        ]
    ]);

$options
    ->addOption("textCSS", "cssText", __("bearcms.themes.themeone.options.DefaultText"), [
        "cssOptions" => ["cssText/defaultState", "cssText/fontFamilyProperty", "cssText/colorProperty", "cssText/fontWeightProperty", "cssText/fontStyleProperty", "cssText/fontSizeProperty", "cssText/lineHeightProperty", "cssText/letterSpacingProperty"],
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-text-font-family:{cssPropertyValue(font-family,Arial,fontName)};"],
            ["selector", ":root", "--bearcms-template-text-color:{cssPropertyValue(color)};"],
            ["selector", ":root", "--bearcms-template-text-font-weight:{cssPropertyValue(font-weight)};"],
            ["selector", ":root", "--bearcms-template-text-font-style:{cssPropertyValue(font-style)};"],
            ["selector", ":root", "--bearcms-template-text-font-size:{cssPropertyValue(font-size)};"],
            ["selector", ":root", "--bearcms-template-text-line-height:{cssPropertyValue(line-height)};"],
            ["selector", ":root", "--bearcms-template-text-letter-spacing:{cssPropertyValue(letter-spacing)};"],
            ["selector", "body .bearcms-template-navigation-menu-button-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M6 7h12M6 12h12M6 17h12"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-navigation-custom-item-search-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-navigation-custom-item-store-cart-icon", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="19" r="1"/><circle cx="18" cy="19" r="1"/><path d="M6 6H3"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-context .bearcms-search-box-element-button", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-context .allebg-poll-element-answer-checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M4 13l5 5L20 7"/></svg>') . '\');'], // Temp (remove in the future)
            ["selector", "body .bearcms-template-context .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type=\"radio-list\"] [data-form-element-component=\"radio-list-option-input\"]:checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="90.708664" height="90.708664" viewBox="0 0 24 24" fill="{cssPropertyValue(color)}"><circle cx="12" cy="12" r="4.276312"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-context .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type=\"checkbox-list\"] [data-form-element-component=\"checkbox-list-option-input\"]:checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{cssPropertyValue(color)}" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M4 13l5 5L20 7"/></svg>') . '\');'],
        ],
        "defaultValue" => '{"color":"#000000","font-size":"16px","font-family":"Arial","line-height":"180%"}'
    ]);

$options
    ->addOption("accentTextCSS", "cssText", __("bearcms.themes.themeone.options.AccentText"), [
        "cssOptions" => ["cssText/defaultState", "cssText/fontFamilyProperty", "cssText/colorProperty", "cssText/fontWeightProperty", "cssText/fontStyleProperty", "cssText/fontSizeProperty", "cssText/lineHeightProperty", "cssText/letterSpacingProperty"],
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-accent-text-font-family:{cssPropertyValue(font-family,Arial,fontName)};"],
            ["selector", ":root", "--bearcms-template-accent-text-color:{cssPropertyValue(color)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-weight:{cssPropertyValue(font-weight)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-style:{cssPropertyValue(font-style)};"],
            ["selector", ":root", "--bearcms-template-accent-text-font-size:{cssPropertyValue(font-size)};"],
            ["selector", ":root", "--bearcms-template-accent-text-line-height:{cssPropertyValue(line-height)};"],
            ["selector", ":root", "--bearcms-template-accent-text-letter-spacing:{cssPropertyValue(letter-spacing)};"],
        ],
        "defaultValue" => '{"color":"#000000","font-size":"16px","font-family":"Arial","line-height":"170%"}'
    ]);

$options
    ->addOption("backgroundCSS", "cssBackground", __("bearcms.themes.themeone.options.Background"), [
        "cssOptions" => ["cssBackground/defaultState"],
        "cssOutput" => [
            ["selector", "body .bearcms-template-container"]
        ],
        "defaultValue" => '{"background-color":"#ffffff"}'
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
        "defaultValue" => "2"
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
        "defaultValue" => "1"
    ])
    ->addOption("footerBackgroundCSS", "cssBackground", __("bearcms.themes.themeone.options.Background"), [
        "cssOptions" => ["cssBackground/defaultState"],
        "cssOutput" => [
            ["selector", "body .bearcms-template-footer"],
            ["selector", ":root", "--bearcms-template-footer-background-color:{cssPropertyValue(background-color)};"]
        ],
        "defaultValue" => '{"background-color":"#111111"}'
    ])
    ->addOption("footerTextColor", "color", __("bearcms.themes.themeone.options.Text color"), [
        "cssOutput" => [
            ["selector", ":root", "--bearcms-template-footer-text-color:{value};"],
            ["selector", "body .bearcms-template-footer .bearcms-search-box-element-button", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{value}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M14.412112 14.412112L20 20"/><circle cx="10" cy="10" r="6"/></svg>') . '\');'],
            ["selector", "body .bearcms-template-footer .allebg-poll-element-answer-checked", 'background-image:url(\'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke="{value}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M4 13l5 5L20 7"/></svg>') . '\');'] // Temp (remove in the future)
        ],
        "defaultValue" => '#ffffff'
    ]);
