<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */


$options = $app->bearCMS->currentTemplate->getOptions();

$addBrightness = function($color, $percent) {
    $a = $percent * 2.55;

    $a = max(-255, min(255, $a));
    if (substr($color, 0, 1) === '#') {
        $color = str_replace('#', '', $color);
        if (strlen($color) == 3) {
            $color = str_repeat(substr($color, 0, 1), 2) . str_repeat(substr($color, 1, 1), 2) . str_repeat(substr($color, 2, 1), 2);
        } $c = hexdec(substr($color, 0, 2));
        $d = hexdec(substr($color, 2, 2));
        $e = hexdec(substr($color, 4, 2));
        $t = 1;
    } elseif (substr($color, 0, 5) === 'rgba(') {
        $color = str_replace('rgba(', '', $color);
        $color = explode(',', $color);
        $c = trim($color[0]);
        $d = trim($color[1]);
        $e = trim($color[2]);
        $t = 2;
        $o = explode(')', $color[3]);
        $o = trim($o[0]);
    } elseif (substr($color, 0, 4) === 'rgb(') {
        $color = str_replace('rgb(', '', $color);
        $color = explode(',', $color);
        $c = trim($color[0]);
        $d = trim($color[1]);
        $color[2] = explode(')', $color[2]);
        $e = trim($color[2][0]);
        $t = 3;
    } else {
        return $color;
    }
    $c = (int) max(0, min(255, $c + $a));
    $d = (int) max(0, min(255, $d + $a));
    $e = (int) max(0, min(255, $e + $a));
    $f = str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
    $h = str_pad(dechex($d), 2, '0', STR_PAD_LEFT);
    $i = str_pad(dechex($e), 2, '0', STR_PAD_LEFT);
    if ($t === 1) {
        return '#' . $f . $h . $i;
    } elseif ($t === 2) {
        return 'grba(' . $c . ',' . $d . ',' . $e . ',' . $o . ')';
    } elseif ($t === 3) {
        return 'grb(' . $c . ',' . $d . ',' . $e . ')';
    }
};

$getResponsiveImageData = function ($desiredWidths, $maxWidth, $widthCompensation = 0, $contain = false) {
    $result = [];
    $desiredWidths[] = $maxWidth;
    $desiredWidths = array_unique($desiredWidths);
    sort($desiredWidths);
    foreach ($desiredWidths as $i => $desiredWidth) {
        if ($desiredWidth > $maxWidth) {
            unset($desiredWidths[$i]);
        }
    }
    $desiredWidths = array_values($desiredWidths);
    if (sizeof($desiredWidths) === 1) {
        $mediaQuery = '(min-width:0)';
        $result[] = ['mediaQuery' => $mediaQuery, 'imageWidth' => $desiredWidths[0]];
    } else {
        $previousDesiredWidth = 0;
        foreach ($desiredWidths as $desiredWidth) {
            if ($previousDesiredWidth === 0) {
                $mediaQuery = '(max-width:' . ($desiredWidth + $widthCompensation) . 'px)';
            } else {
                $mediaQuery = '(min-width:' . ($previousDesiredWidth + $widthCompensation + 0.0001) . 'px) and (max-width:' . ($desiredWidth + $widthCompensation) . 'px)';
            }
            $result[] = ['mediaQuery' => $mediaQuery, 'imageWidth' => $contain ? $previousDesiredWidth : $desiredWidth];
            $previousDesiredWidth = $desiredWidth;
        }
        if ($contain) {
            unset($result[0]);
            $result[1]['mediaQuery'] = '(max-width:' . ($desiredWidths[1] + $widthCompensation) . 'px)';
            $maxDesiredWidth = max($desiredWidths);
            $mediaQuery = '(min-width:' . ($maxDesiredWidth + $widthCompensation + 0.0001) . 'px)';
            $result[] = ['mediaQuery' => $mediaQuery, 'imageWidth' => $maxDesiredWidth];
        } else {
            $lastResultIndex = sizeof($result) - 1;
            $mediaQuery = '(min-width:' . ($desiredWidths[sizeof($desiredWidths) - 2] + $widthCompensation + 0.0001) . 'px)';
            $result[$lastResultIndex]['mediaQuery'] = $mediaQuery;
        }
    }
    return $result;
};

$getNewDimensions = function ($width, $height, $maxWidth, $maxHeight) use (&$getNewDimensions) {
    if ($width > $maxWidth) {
        return $getNewDimensions($maxWidth, round($height * $maxWidth / $width), $maxWidth, $maxHeight);
    }
    if ($height > $maxHeight) {
        return $getNewDimensions(round($width * $maxHeight / $height), $maxHeight, $maxWidth, $maxHeight);
    }
    return [$width, $height];
};

$addAlpha = function ($color, $alpha) {
    if (substr($color, 0, 1) === '#') {
        $color = str_replace('#', '', $color);
        if (strlen($color) == 3) {
            $color = str_repeat(substr($color, 0, 1), 2) . str_repeat(substr($color, 1, 1), 2) . str_repeat(substr($color, 2, 1), 2);
        }
        $c = hexdec(substr($color, 0, 2));
        $d = hexdec(substr($color, 2, 2));
        $e = hexdec(substr($color, 4, 2));
        $t = 1;
        $o = 1;
    } elseif (substr($color, 0, 5) === 'rgba(') {
        $color = str_replace('rgba(', '', $color);
        $color = explode(',', $color);
        $c = trim($color[0]);
        $d = trim($color[1]);
        $e = trim($color[2]);
        $t = 2;
        $o = explode(')', $color[3]);
        $o = (double) trim($o[0]);
    } elseif (substr($color, 0, 4) === 'rgb(') {
        $color = str_replace('rgb(', '', $color);
        $color = explode(',', $color);
        $c = trim($color[0]);
        $d = trim($color[1]);
        $color[2] = explode(')', $color[2]);
        $e = trim($color[2][0]);
        $t = 3;
        $o = 1;
    } else {
        return $color;
    }
    $v = $o + $alpha;
    if ($v < 0) {
        $v = 0;
    } elseif ($v > 1) {
        $v = 1;
    }
    return 'rgba(' . (int) $c . ',' . (int) $d . ',' . (int) $e . ',' . number_format($v, 2, '.', '') . ')';
};

$mode = $component->mode;
if ($mode === 'notFound' || $mode === 'temporaryUnavailable') {
    ?><html>
        <head>
            <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,minimal-ui">
            <style>
                html{
                    height:100%;
                }
                html, body{
                    padding:0;
                    margin:0;
                }
                body{
                    background-color:#000;
                    color:#fff;
                    font-family:Arial,Helvetica,sans-serif;
                    font-size:14px;
                    height:100%;
                    box-sizing:border-box;
                    padding:15px;
                }
            </style>
        </head>
        <body>
            <div style="display:table;width:100%;height:100%;"><div style="display:table-cell;text-align:center;vertical-align:middle;">{body}</div></div>
        </body></html><?php
    return;
}

// todo - in nav pages
$hasPages = sizeof($app->bearCMS->data->pages->getList());
$settings = $app->bearCMS->data->settings->get();
$isHomePage = (string) $app->request->path === '/';

$fontsHTML = '';
$headerLogoImage = $options['headerLogoImage'];
$headerTitleVisibility = $options['headerTitleVisibility'];
if ($headerTitleVisibility === '1') {
    $headerTitleColor = $options['headerTitleColor'];
    $headerTitleFont = $options['headerTitleFont'];
    $fontsHTML .= $app->bearCMS->currentTemplate->getFontsHTML($headerTitleFont, $this);
    $headerTitleBackgroundColor = $options['headerTitleBackgroundColor'];
    $headerTitleBackgroundColor1 = $addBrightness($headerTitleBackgroundColor, -7);
    $headerTitleBackgroundColor2 = $addBrightness($headerTitleBackgroundColor, -14);
}
$headerDescriptionVisibility = $options['headerDescriptionVisibility'];
if ($headerDescriptionVisibility === '1') {
    $headerDescriptionBackgroundColor = $options['headerDescriptionBackgroundColor'];
    $headerDescriptionColor = $options['headerDescriptionColor'];
}
$headerBackgroundColor = $options['headerBackgroundColor'];
$headerBackgroundImage = $options['headerBackgroundImage'];

if ($hasPages) {
    $navigationPosition = $options['navigationPosition'];
    $navigationTextColor = $options['navigationTextColor'];
    $navigationHighlightColor = $options['navigationHighlightColor'];
    $navigationHighlightColor1 = $addBrightness($navigationHighlightColor, -7);
    $navigationHighlightColor2 = $addBrightness($navigationHighlightColor, -14);
    $navigationBackgroundColor = $options['navigationBackgroundColor'];
}

$contentTextColor = $options['contentTextColor'];
$contentHighlightColor = $options['contentHighlightColor'];
$contentHighlightColor1 = $addBrightness($contentHighlightColor, -7);
$contentHighlightColor2 = $addBrightness($contentHighlightColor, -14);
$contentBackgroundColor = $options['contentBackgroundColor'];

if ($isHomePage) {
    $homePageSpecialBlockVisibility = $options['homePageSpecialBlockVisibility'];
    $homePageSpecialBlockTextColor = $options['homePageSpecialBlockTextColor'];
    $homePageSpecialBlockHighlightColor = $options['homePageSpecialBlockHighlightColor'];
    $homePageSpecialBlockHighlightColor1 = $addBrightness($homePageSpecialBlockHighlightColor, -7);
    $homePageSpecialBlockHighlightColor2 = $addBrightness($homePageSpecialBlockHighlightColor, -14);
    $homePageSpecialBlockBackgroundColor = $options['homePageSpecialBlockBackgroundColor'];
}

$footerVisibility = $options['footerVisibility'];
if ($footerVisibility === '1') {
    $footerTextColor = $options['footerTextColor'];
    $footerHighlightColor = $options['footerHighlightColor'];
    $footerHighlightColor1 = $addBrightness($footerHighlightColor, -7);
    $footerHighlightColor2 = $addBrightness($footerHighlightColor, -14);
    $footerBackgroundColor = $options['footerBackgroundColor'];

    $poweredByLinkVisibility = $options['poweredByLinkVisibility'];
    $poweredByLinkTextColor = $options['poweredByLinkTextColor'];
    $poweredByLinkTextColor1 = $addBrightness($poweredByLinkTextColor, -7);
    $poweredByLinkTextColor2 = $addBrightness($poweredByLinkTextColor, -14);
}

$contentMaxWidth = '800px';
$headerItemsSpacingInPx = 40;
?><html>
    <head>
        <style>
            html, body{
                padding:0;
                margin:0;
            }
            html{
                height:100%;
            }
            body{
                background-color:#000;
                color:#fff;
                font-family:Arial,Helvetica,sans-serif;
                font-size:14px;
                height:100%;
            }
            
            .bearcms-heading-element-large{
                font-size:28px;
                line-height:180%;
                color:<?= $contentHighlightColor ?>;
                font-weight:normal;
                font-family:Arial;
                text-align:center;
                margin:0;
            }
            .bearcms-heading-element-medium{
                font-size:22px;
                line-height:180%;
                color:<?= $contentHighlightColor ?>;
                font-weight:normal;
                font-family:Arial;
                text-align:center;
                margin:0;
            }
            .bearcms-heading-element-small{
                font-size:18px;
                line-height:180%;
                color:<?= $contentHighlightColor ?>;
                font-weight:normal;
                font-family:Arial;
                text-align:center;
                margin:0;
            }

            .bearcms-link-element{
                color:<?= $contentBackgroundColor ?>;
                display:inline-block;
                text-decoration:none;
                padding:15px;
                background-color:<?= $contentHighlightColor ?>;
                transition: background-color 200ms;
            }
            .bearcms-link-element:hover{
                background-color:<?= $contentHighlightColor1 ?>;
            }
            .bearcms-link-element:active{
                background-color:<?= $contentHighlightColor2 ?>;
            }

            .bearcms-navigation-element{
                list-style-type:none;
            }
            .bearcms-navigation-element-item a{
                font-size:14px;
                color:<?= $contentHighlightColor ?>;
                line-height:180%;
                display:inline-block;
                text-decoration:underline;
                transition: color 200ms;
            }
            .bearcms-navigation-element-item a:hover{
                color:<?= $contentHighlightColor1 ?>;
            }
            .bearcms-navigation-element-item a:active{
                color:<?= $contentHighlightColor2 ?>;
            }

            .bearcms-text-element, .bearcms-html-element {
                font-size:14px;
                color:<?= $contentTextColor ?>;
                line-height:180%;
            }
            .bearcms-text-element a, .bearcms-html-element a{
                font-size:14px;
                color:<?= $contentHighlightColor ?>;
                display:inline-block;
                text-decoration:underline;
                transition: color 200ms;
            }
            .bearcms-text-element a:hover, .bearcms-html-element a:hover{
                color:<?= $contentHighlightColor1 ?>;
            }
            .bearcms-text-element a:active, .bearcms-html-element a:active{
                color:<?= $contentHighlightColor2 ?>;
            }

            .bearcms-blog-posts-element-post{
                padding-bottom:15px;
            }
            .bearcms-blog-posts-element-post:last-child{
                padding-bottom:0;
            }

            .bearcms-blog-posts-element-post-title{
                font-size:25px;
                line-height:180%;
                color:<?= $contentHighlightColor ?>;
                text-align:center;
                font-weight:normal;
                font-family:Arial;
                text-decoration:none;
            }
            .bearcms-blog-posts-element-post-title-container{
                text-align:center;
            }
            .bearcms-blog-posts-element-post-title:hover{
                color:<?= $contentHighlightColor1 ?>;
            }
            .bearcms-blog-posts-element-post-title:active{
                color:<?= $contentHighlightColor2 ?>;
            }

            .bearcms-blog-posts-element-post-date{
                font-size:14px;
                color:<?= $addBrightness($contentTextColor, 50) ?>;
                line-height:180%;
            }

            .bearcms-blog-posts-element-post-date-container{
                padding-bottom:10px;
                text-align:center;
            }

            .bearcms-blogpost-page-title{
                font-size:28px;
                line-height:180%;
                color:<?= $contentHighlightColor ?>;
                font-weight:normal;
                font-family:Arial;
                text-align:center;
            }

            .bearcms-blogpost-page-date{
                font-size:14px;
                color:<?= $addBrightness($contentTextColor, 50) ?>;
                text-align:center;
                line-height:180%;
                padding-bottom:15px;
            }

            .template-header-outer-container{
                background-color:<?= $headerBackgroundColor ?>;
            }

            .template-header-inner-container{
                text-align:center;
                max-width:<?= $contentMaxWidth ?>;
                margin:0 auto;
                padding:<?= $headerItemsSpacingInPx ?>px;
                padding-top:0;
            }

            <?php if (!empty($headerBackgroundImage)) { ?>
                .template-header-outer-container{
                    background-position:center center;
                    background-size:cover;
                }
                <?php
                $headerLogoImageFilename = $app->bearCMS->data->getRealFilename($headerBackgroundImage);
                $headerBackgroundImageDimensions = $app->images->getSize($headerLogoImageFilename);
                $headerBackgroundResponsiveImageData = $getResponsiveImageData([320, 360, 480, 768, 1024, 1280, 1366, 1440, 1680, 1960, 2048, $headerBackgroundImageDimensions[0]], $headerBackgroundImageDimensions[0]);
                foreach ($headerBackgroundResponsiveImageData as $responsiveImageData) {
                    echo '@media' . $responsiveImageData['mediaQuery'] . '{
                            .template-header-outer-container{
                                background-image:url(' . $app->assets->getUrl($headerLogoImageFilename, ['width' => $responsiveImageData['imageWidth']]) . ');
                            }
                        }';
                }
                ?>
            <?php } ?>

            <?php if (!empty($headerLogoImage)) { ?>
                .template-header-logo-container{
                    padding-top:<?= $headerItemsSpacingInPx ?>px;
                }
                .template-header-logo{
                    background-repeat:no-repeat;
                    background-size:100% 100%;
                    background-position:center center;
                    display:block;
                    margin:0 auto;
                }

                <?php
                $headerLogoImageFilename = $app->bearCMS->data->getRealFilename($headerLogoImage);
                $headerLogoImageDimensions = $app->images->getSize($headerLogoImageFilename);
                $headerLogoImageMaxWidth = $isHomePage ? 400 : 200;
                if ($headerLogoImageDimensions[0] < $headerLogoImageMaxWidth) {
                    $headerLogoImageMaxWidth = $headerLogoImageDimensions[0];
                }
                $headerLogoResponsiveImageData = $getResponsiveImageData([100, 200, 300, $headerLogoImageMaxWidth], $headerLogoImageMaxWidth, $headerItemsSpacingInPx * 2, true);
                foreach ($headerLogoResponsiveImageData as $responsiveImageData) {
                    list($width, $height) = $getNewDimensions($headerLogoImageDimensions[0], $headerLogoImageDimensions[1], $responsiveImageData['imageWidth'], $responsiveImageData['imageWidth']);
                    echo '@media' . $responsiveImageData['mediaQuery'] . '{
                        .template-header-logo{
                            width:' . $width . 'px;
                            height:' . $height . 'px;
                            background-image:url(' . $app->assets->getUrl($headerLogoImageFilename, ['width' => $width, 'height' => $height]) . ');
                        }
                    }';
                }
                ?>

            <?php } ?>

            <?php if ($headerTitleVisibility === '1') { ?>
                .template-header-title-container{
                    padding-top:<?= $headerItemsSpacingInPx ?>px;
                }
                .template-header-title{
                    display:inline-block;
                    font-size:25px;
                    background-color:<?= $headerTitleBackgroundColor ?>;
                    color:<?= $headerTitleColor ?>;
                    font-family:<?= $app->bearCMS->currentTemplate->getFontFamily($headerTitleFont) ?>;
                    padding:20px;
                    transition: background-color 200ms;
                    text-decoration:none;
                }
                .template-header-title:hover{
                    background-color:<?= $headerTitleBackgroundColor1 ?>;
                }
                .template-header-title:active{
                    background-color:<?= $headerTitleBackgroundColor2 ?>;
                }
            <?php } ?>

            <?php if ($headerDescriptionVisibility === '1') { ?>
                .template-header-description{
                    padding-top:<?= $headerItemsSpacingInPx ?>px;
                    font-size:15px;
                    background-color:<?= $headerDescriptionBackgroundColor ?>;
                    color:<?= $headerDescriptionColor ?>;
                    font-family:Arial,Helvetica,sans-serif;
                }
            <?php } ?>

            <?php if ($hasPages) { ?>
                .template-navigation{
                    padding-left:15px;
                    padding-right:15px;
                }
                .template-navigation .bearcms-navigation-element-item{
                    display:inline-block;
                    cursor:pointer;
                    white-space:nowrap;
                    transition: background-color 200ms, color 200ms;
                }
                .template-navigation .bearcms-navigation-element-item > a{
                    display:block;
                    color:<?= $navigationTextColor ?>;
                    padding:15px;
                    font-size:15px;
                    font-family:Arial,Helvetica,sans-serif;
                    text-decoration:none;
                    line-height:normal;
                }
                .template-navigation .bearcms-navigation-element-item-selected, .template-navigation .bearcms-navigation-element-item-in-path, .template-navigation .bearcms-navigation-element-item:hover{
                    background-color:<?= $navigationHighlightColor ?>;
                }
                .template-navigation .bearcms-navigation-element-item:active{
                    background-color:<?= $navigationHighlightColor1 ?>;
                }

                .template-navigation .bearcms-navigation-element-item-children{
                    z-index:100;
                }

                .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item{
                    display:block;
                    text-align:left;
                }
                .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item{
                    background-color:<?= $navigationHighlightColor ?>;
                }
                .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-selected, .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item-in-path, .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item:hover{
                    background-color:<?= $navigationHighlightColor1 ?>;
                }
                .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item:active{
                    background-color:<?= $navigationHighlightColor2 ?>;
                }

                .template-navigation .bearcms-navigation-element-item-more > a:before{
                    content:"...";
                    color:<?= $navigationTextColor ?>;
                    font-size:15px;
                    font-family:Arial,Helvetica,sans-serif;
                }

                .template-navigation-outer-container{
                    text-align:center;
                    background-color:<?= $navigationBackgroundColor ?>;
                }

                .template-navigation-inner-container{
                    max-width:<?= $contentMaxWidth ?>;
                    margin:0 auto;
                }

                #template-navigation-toggle-button{
                    display:none;
                }
                #template-navigation-toggle-button + label{
                    display:none;
                }

                @media(max-width: 680px) {

                    .template-navigation{
                        padding-left:0;
                        padding-right:0;
                    }

                    .template-navigation .bearcms-navigation-element-item{
                        display:block;
                    }

                    .template-navigation > .bearcms-navigation-element-item{
                        margin-bottom:15px;
                    }
                    .template-navigation > .bearcms-navigation-element-item:last-child{
                        margin-bottom:0;
                    }

                    .template-navigation .bearcms-navigation-element-item, .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item{
                        text-align:center;
                        transition: background-color 200ms;
                        background-color:rgba(0, 0, 0, 0.1);
                    }
                    .template-navigation .bearcms-navigation-element-item:hover, .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item:hover{
                        background-color:rgba(0, 0, 0, 0.2);
                    }
                    .template-navigation .bearcms-navigation-element-item:active, .template-navigation .bearcms-navigation-element-item-children > .bearcms-navigation-element-item:active{
                        background-color:rgba(0, 0, 0, 0.30);
                    }

                    .template-navigation .bearcms-navigation-element-item > a:hover{
                        background-color:rgba(0, 0, 0, 0.2);
                    }
                    .template-navigation .bearcms-navigation-element-item > a:active{
                        background-color:rgba(0, 0, 0, 0.30);
                    }

                    #template-navigation-toggle-button{
                        display:none;
                    }
                    #template-navigation-toggle-button + label{
                        display:block;
                        width:100%;
                        height:48px;
                        margin:0 auto;
                        background-color:<?= $navigationHighlightColor ?>;
                        transition: background-color 200ms;
                        cursor:pointer;
                        background-image:url('data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" fill="' . $navigationTextColor . '"><path d="M512 192l-96-96-160 160L96 96 0 192l256 256z"/></svg>') ?>');
                        background-size:50% 50%;
                        background-position:center center;
                        background-repeat:no-repeat;
                    }
                    #template-navigation-toggle-button + label:hover{
                        background-color:<?= $navigationHighlightColor1 ?>;
                    }
                    #template-navigation-toggle-button + label:active{
                        background-color:<?= $navigationHighlightColor2 ?>;
                    }
                    #template-navigation-toggle-button:checked + label{
                        background-color:<?= $navigationHighlightColor2 ?>;
                        background-image:url('data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="' . $navigationTextColor . '"><path d="M63.42 51.42L44 32 63.4 12.58c.02 0 .02 0 .02 0 .2-.2.36-.45.45-.7.27-.72.12-1.55-.45-2.13L54.23.58c-.57-.57-1.4-.72-2.1-.46-.27.1-.52.25-.73.46.02 0 .02 0 0 0L32 20 12.58.58c-.2-.2-.45-.36-.7-.46-.72-.26-1.55-.1-2.13.46L.58 9.75c-.57.58-.72 1.4-.46 2.12.1.26.25.5.46.7L20 32 .58 51.4s0 0 0 .02c-.2.2-.36.45-.45.7-.27.72-.12 1.55.45 2.12l9.18 9.17c.57.6 1.4.74 2.1.47.27-.1.5-.25.72-.45L32 44 51.4 63.4c.02 0 .02 0 .02.02.2.2.45.35.7.45.72.27 1.55.12 2.12-.46l9.17-9.16c.6-.57.74-1.4.47-2.1-.1-.27-.25-.52-.45-.72z"/></svg>') ?>');
                    }
                    #template-navigation-toggle-button + label + div{
                        display:none;
                        padding:15px;
                    }
                    #template-navigation-toggle-button:checked + label + div{
                        display:block;
                        position:absolute;
                        width:100%;
                        background-color:<?= $navigationHighlightColor ?>;
                        z-index:100;
                        box-sizing:border-box;
                    }

                }
            <?php } ?>

            .template-content-outer-container{
                background-color:<?= $contentBackgroundColor ?>;
            }

            .template-content-inner-container{
                padding:15px;
                padding-top:<?= $headerItemsSpacingInPx ?>px;
                padding-bottom:<?= $headerItemsSpacingInPx ?>px;
                max-width:<?= $contentMaxWidth ?>;
                margin:0 auto;
                min-height:250px;
                box-sizing:border-box;
            }

            <?php if ($isHomePage && $homePageSpecialBlockVisibility === '1') { ?>

                .template-homepage-special-block-outer-container{
                    background-color:<?= $homePageSpecialBlockBackgroundColor ?>;
                }

                .template-homepage-special-block-inner-container{
                    text-align:center;
                    max-width:<?= $contentMaxWidth ?>;
                    margin:0 auto;
                    padding:<?= $headerItemsSpacingInPx ?>px;
                }

                .homepage-special-bearcms-elements .bearcms-heading-element-large{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-heading-element-medium{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-heading-element-small{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }

                .homepage-special-bearcms-elements .bearcms-link-element{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                    padding:0;
                    background-color:transparent;
                    line-height:180%;
                    display:inline-block;
                    text-decoration:underline;
                }
                .homepage-special-bearcms-elements .bearcms-link-element:hover{
                    color:<?= $homePageSpecialBlockHighlightColor1 ?>;
                    background-color:transparent;
                }
                .homepage-special-bearcms-elements .bearcms-link-element:active{
                    color:<?= $homePageSpecialBlockHighlightColor2 ?>;
                    background-color:transparent;
                }

                .homepage-special-bearcms-elements .bearcms-navigation-element-item a{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-navigation-element-item a:hover{
                    color:<?= $homePageSpecialBlockHighlightColor1 ?>;
                }
                .homepage-special-bearcms-elements .bearcms-navigation-element-item a:active{
                    color:<?= $homePageSpecialBlockHighlightColor2 ?>;
                }

                .homepage-special-bearcms-elements .bearcms-text-element, .homepage-special-bearcms-elements .bearcms-html-element {
                    color:<?= $homePageSpecialBlockTextColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-text-element a, .homepage-special-bearcms-elements .bearcms-html-element a{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-text-element a:hover, .homepage-special-bearcms-elements .bearcms-html-element a:hover{
                    color:<?= $homePageSpecialBlockHighlightColor1 ?>;
                }
                .homepage-special-bearcms-elements .bearcms-text-element a:active, .homepage-special-bearcms-elements .bearcms-html-element a:active{
                    color:<?= $homePageSpecialBlockHighlightColor2 ?>;
                }

                .homepage-special-bearcms-elements .bearcms-blog-posts-element-post-title{
                    color:<?= $homePageSpecialBlockHighlightColor ?>;
                }
                .homepage-special-bearcms-elements .bearcms-blog-posts-element-post-title:hover{
                    color:<?= $homePageSpecialBlockHighlightColor1 ?>;
                }
                .homepage-special-bearcms-elements .bearcms-blog-posts-element-post-title:active{
                    color:<?= $homePageSpecialBlockHighlightColor2 ?>;
                }

                .homepage-special-bearcms-elements .bearcms-blog-posts-element-post-date{
                    color:<?= $addBrightness($homePageSpecialBlockTextColor, -50) ?>;
                }

            <?php } ?>



            <?php if ($footerVisibility === '1') { ?>

                body{
                    background-color:<?= $footerBackgroundColor ?>;
                }

                .template-footer-outer-container{
                    background-color:<?= $footerBackgroundColor ?>;
                }

                .template-footer-inner-container{
                    padding:15px;
                    padding-top:<?= $headerItemsSpacingInPx ?>px;
                    padding-bottom:<?= $headerItemsSpacingInPx ?>px;
                    max-width:<?= $contentMaxWidth ?>;
                    margin:0 auto;
                }

                .footer-bearcms-elements .bearcms-heading-element-large{
                    color:<?= $footerHighlightColor ?>;
                }
                .footer-bearcms-elements .bearcms-heading-element-medium{
                    color:<?= $footerHighlightColor ?>;
                }
                .footer-bearcms-elements .bearcms-heading-element-small{
                    color:<?= $footerHighlightColor ?>;
                }

                .footer-bearcms-elements .bearcms-link-element{
                    color:<?= $footerHighlightColor ?>;
                    padding:0;
                    background-color:transparent;
                    line-height:180%;
                    display:inline-block;
                    text-decoration:underline;
                }
                .footer-bearcms-elements .bearcms-link-element:hover{
                    color:<?= $footerHighlightColor1 ?>;
                    background-color:transparent;
                }
                .footer-bearcms-elements .bearcms-link-element:active{
                    color:<?= $footerHighlightColor2 ?>;
                    background-color:transparent;
                }

                .footer-bearcms-elements .bearcms-navigation-element-item a{
                    color:<?= $footerHighlightColor ?>;
                }
                .footer-bearcms-elements .bearcms-navigation-element-item a:hover{
                    color:<?= $footerHighlightColor1 ?>;
                }
                .footer-bearcms-elements .bearcms-navigation-element-item a:active{
                    color:<?= $footerHighlightColor2 ?>;
                }

                .footer-bearcms-elements .bearcms-text-element, .footer-bearcms-elements .bearcms-html-element {
                    color:<?= $footerTextColor ?>;
                }
                .footer-bearcms-elements .bearcms-text-element a, .footer-bearcms-elements .bearcms-html-element a{
                    color:<?= $footerHighlightColor ?>;
                }
                .footer-bearcms-elements .bearcms-text-element a:hover, .footer-bearcms-elements .bearcms-html-element a:hover{
                    color:<?= $footerHighlightColor1 ?>;
                }
                .footer-bearcms-elements .bearcms-text-element a:active, .footer-bearcms-elements .bearcms-html-element a:active{
                    color:<?= $footerHighlightColor2 ?>;
                }

                .footer-bearcms-elements .bearcms-blog-posts-element-post-title{
                    color:<?= $footerHighlightColor ?>;
                }
                .footer-bearcms-elements .bearcms-blog-posts-element-post-title:hover{
                    color:<?= $footerHighlightColor1 ?>;
                }
                .footer-bearcms-elements .bearcms-blog-posts-element-post-title:active{
                    color:<?= $footerHighlightColor2 ?>;
                }

                .footer-bearcms-elements .bearcms-blog-posts-element-post-date{
                    color:<?= $addBrightness($footerTextColor, -50) ?>;
                }


                <?php if ($poweredByLinkVisibility === '1') { ?>

                    .template-powered-by-outer-container{
                        text-align:center;
                    }
                    .template-powered-by-inner-container{
                        padding-top:15px;
                        color:<?= $poweredByLinkTextColor ?>;
                        border-top:1px solid <?= $addAlpha($poweredByLinkTextColor, -0.8) ?>;
                        display:inline-block;
                        margin-top:15px;
                        padding-left:15px;
                        padding-right:15px;
                        line-height:180%;
                    }
                    .template-powered-by-inner-container a{
                        color:<?= $poweredByLinkTextColor ?>;
                        text-decoration:underline;
                    }
                    .template-powered-by-inner-container a:hover{
                        color:<?= $poweredByLinkTextColor1 ?>;
                    }
                    .template-powered-by-inner-container a:active{
                        color:<?= $poweredByLinkTextColor2 ?>;
                    }
                <?php } ?>


            <?php } ?>



            <?php if ($poweredByLinkVisibility === '1') { ?>

                .template-powered-by-outer-container{
                    text-align:center;
                }
                .template-powered-by-inner-container{
                    padding-top:15px;
                    color:<?= $poweredByLinkTextColor ?>;
                    border-top:1px solid <?= $addAlpha($poweredByLinkTextColor, -0.8) ?>;
                    display:inline-block;
                    margin-top:15px;
                    padding-left:15px;
                    padding-right:15px;
                    line-height:180%;
                }
                .template-powered-by-inner-container a{
                    color:<?= $poweredByLinkTextColor ?>;
                    text-decoration:underline;
                }
                .template-powered-by-inner-container a:hover{
                    color:<?= $poweredByLinkTextColor1 ?>;
                }
                .template-powered-by-inner-container a:active{
                    color:<?= $poweredByLinkTextColor2 ?>;
                }

            <?php } ?>

            <?= $options['customCSS'] ?>
        </style>
    </head>
    <body><?php
        $navigationContent = '';
        if ($hasPages) {
            $navigationContent .= '<div class="template-navigation-outer-container">';
            $navigationContent .= '<div class="template-navigation-inner-container">';
            $navigationContent .= '<input id="template-navigation-toggle-button" type="checkbox"/><label for="template-navigation-toggle-button"></label>';
            $navigationContent .= '<div><component src="bearcms-navigation-element" type="tree" showHomeButton="true" menuType="horizontal-down" class="template-navigation" selectedpath="' . (string) $app->request->path . '" /></div>';
            $navigationContent .= '</div>';
            $navigationContent .= '</div>';
            $navigationContent .= '<script>var f=function(){var e=document.querySelector(".template-navigation");if(e){e.setAttribute("data-nm-type", window.innerWidth <= 680 ? "none" : "horizontal-down");};};window.addEventListener("resize",f,false);window.addEventListener("load",f,false);f();</script>';
        }

        echo '<div class="template-header-outer-container">';

        if ($hasPages && $navigationPosition === '1') {
            echo $navigationContent;
        }

        echo '<div class="template-header-inner-container">';

        if (!empty($headerLogoImage)) {
            $logoTagName = $isHomePage ? 'span' : 'a';
            echo '<div class="template-header-logo-container"><' . $logoTagName . ' class="template-header-logo" href="' . $app->request->base . '"></' . $logoTagName . '></div>';
        }
        if ($headerTitleVisibility === '1') {
            echo '<div class="template-header-title-container"><a class="template-header-title" href="' . $app->request->base . '">' . htmlspecialchars($settings['title']) . '</a></div>';
        }
        if ($headerDescriptionVisibility === '1') {
            echo '<div class="template-header-description">' . htmlspecialchars($settings['description']) . '</div>';
        }

        echo '</div>';

        if ($isHomePage && $homePageSpecialBlockVisibility === '1') {
            echo '<div class="template-homepage-special-block-outer-container">';
            echo '<div class="template-homepage-special-block-inner-container">';
            echo '<component src="bearcms-elements" color="#E0880B" editable="true" class="homepage-special-bearcms-elements" id="homepage-special"/>';
            echo '</div>';
            echo '</div>';
        }

        if ($hasPages && $navigationPosition === '2') {
            echo $navigationContent;
        }

        echo '</div>';


        echo '<div class="template-content-outer-container">';
        echo '<div class="template-content-inner-container">';
        echo '{body}';
        echo '</div>';
        echo '</div>';

        if ($footerVisibility === '1') {

            echo '<div class="template-footer-outer-container">';
            echo '<div class="template-footer-inner-container">';
            echo '<component src="bearcms-elements" color="#E0880B" editable="true" class="footer-bearcms-elements" id="footer"/>';
            if ($poweredByLinkVisibility === '1') {
                echo '<div class="template-powered-by-outer-container">';
                echo '<div class="template-powered-by-inner-container">';
                echo 'Powered by <a href="https://bearcms.com/" target="_blank">Bear CMS</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        }

        if (isset($fontsHTML{0})) {
            echo '<component src="data:base64,' . base64_encode($fontsHTML) . '"/>';
        }
        ?></body>
</html>