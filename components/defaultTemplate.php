<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$options = $app->bearCMS->currentTemplate->getOptions();

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

$hasPages = sizeof($app->bearCMS->data->pages->getList());
$settings = $app->bearCMS->data->settings->get();
$isHomePage = (string) $app->request->path === '/';

$bodyMaxWidth = $options['bodyMaxWidth'];

$headerMaxWidth = $options['headerMaxWidth'];
$headerLogoImage = $options['headerLogoImage'];
$headerTitleVisibility = $options['headerTitleVisibility'];
$headerDescriptionVisibility = $options['headerDescriptionVisibility'];

$navigationMaxWidth = $options['navigationMaxWidth'];
$navigationPosition = $options['navigationPosition'];

$homePageSpecialContentBlockVisibility = $options['homePageSpecialContentBlockVisibility'];

$contentMaxWidth = $options['contentMaxWidth'];

$footerMaxWidth = $options['footerMaxWidth'];
$footerVisibility = $options['footerVisibility'];
$poweredByLinkVisibility = $options['poweredByLinkVisibility'];
?><html>
    <head>
        <style>
            html, body{
                padding: 0;
                margin: 0;
                height: 100%;
            }
            .body > div {
                max-width: <?= $bodyMaxWidth ?>;
                margin: 0 auto;
            }
            .template-header > div {
                max-width: <?= $headerMaxWidth ?>;
                margin: 0 auto;
            }
            .template-navigation-container > div {
                max-width: <?= $navigationMaxWidth ?>;
                margin: 0 auto;
            }
            <?php if ($hasPages) { ?>
                .template-navigation .bearcms-navigation-element-item-more > a:before{
                    content: "...";
                }
                #template-navigation-toggle-button{
                    display: none;
                }
                #template-navigation-toggle-button + label{
                    display: none;
                }
                @media(max-width: 680px) {
                    .template-navigation{
                        padding-left: 0;
                        padding-right: 0;
                    }
                    .template-navigation .bearcms-navigation-element-item{
                        display: block;
                    }
                    #template-navigation-toggle-button + label{
                        display: block;
                        width: 100%;
                        height: 48px;
                        margin: 0 auto;
                        background-color: red;
                        transition: background-color 200ms;
                        cursor: pointer;
                        background-image: url('data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" fill="#11cc55"><path d="M512 192l-96-96-160 160L96 96 0 192l256 256z"/></svg>') ?>');
                        background-size: 50% 50%;
                        background-position: center center;
                        background-repeat: no-repeat;
                    }
                    #template-navigation-toggle-button:checked + label{
                        background-color: pink;
                        background-image: url('data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#11cc55"><path d="M63.42 51.42L44 32 63.4 12.58c.02 0 .02 0 .02 0 .2-.2.36-.45.45-.7.27-.72.12-1.55-.45-2.13L54.23.58c-.57-.57-1.4-.72-2.1-.46-.27.1-.52.25-.73.46.02 0 .02 0 0 0L32 20 12.58.58c-.2-.2-.45-.36-.7-.46-.72-.26-1.55-.1-2.13.46L.58 9.75c-.57.58-.72 1.4-.46 2.12.1.26.25.5.46.7L20 32 .58 51.4s0 0 0 .02c-.2.2-.36.45-.45.7-.27.72-.12 1.55.45 2.12l9.18 9.17c.57.6 1.4.74 2.1.47.27-.1.5-.25.72-.45L32 44 51.4 63.4c.02 0 .02 0 .02.02.2.2.45.35.7.45.72.27 1.55.12 2.12-.46l9.17-9.16c.6-.57.74-1.4.47-2.1-.1-.27-.25-.52-.45-.72z"/></svg>') ?>');
                    }
                    #template-navigation-toggle-button + label + div{
                        display: none;
                        padding: 15px;
                    }
                    #template-navigation-toggle-button:checked + label + div{
                        display: block;
                        position: absolute;
                        width: 100%;
                        background-color: orange;
                        z-index: 100;
                        box-sizing: border-box;
                    }
                }
            <?php } ?>
            .template-content > div {
                max-width: <?= $contentMaxWidth ?>;
                margin: 0 auto;
            }
            .template-footer > div {
                max-width: <?= $footerMaxWidth ?>;
                margin: 0 auto;
            }
            <?= $app->bearCMS->currentTemplate->getOptionsCss() ?>
        </style>
    </head>
    <body><div><?php
        $navigationContent = '';
        if ($hasPages) {
            $navigationContent .= '<nav class="template-navigation-container">';
            $navigationContent .= '<div>';
            $navigationContent .= '<input id="template-navigation-toggle-button" type="checkbox"/><label for="template-navigation-toggle-button"></label>';
            $navigationContent .= '<div><component src="bearcms-navigation-element" type="tree" showHomeButton="true" menuType="horizontal-down" class="template-navigation" selectedPath="' . (string) $app->request->path . '" /></div>';
            $navigationContent .= '<script>var f=function(){var e=document.querySelector(".template-navigation");if(e){e.setAttribute("data-nm-type", window.innerWidth <= 680 ? "none" : "horizontal-down");};};window.addEventListener("resize",f,false);window.addEventListener("load",f,false);f();</script>';
            $navigationContent .= '</div>';
            $navigationContent .= '</nav>';
        }

        if ($hasPages && $navigationPosition === '1') {
            echo $navigationContent;
        }

        echo '<header class="template-header">';
        echo '<div>';

        if (!empty($headerLogoImage)) {
            $logoTagName = $isHomePage ? 'span' : 'a';
            echo '<div style="text-align:center;"><div class="template-header-logo-container"><' . $logoTagName . ' class="template-header-logo" href="' . $app->request->base . '"></' . $logoTagName . '></div></div>';
        }
        if ($headerTitleVisibility === '1') {
            echo '<div style="text-align:center;"><div class="template-header-title-container"><a class="template-header-title" href="' . $app->request->base . '">' . htmlspecialchars($settings['title']) . '</a></div></div>';
        }
        if ($headerDescriptionVisibility === '1') {
            echo '<div style="text-align:center;"><div class="template-header-description-container"><div class="template-header-description">' . htmlspecialchars($settings['description']) . '</div></div></div>';
        }

        echo '</div>';
        echo '</header>';

        if ($isHomePage && $homePageSpecialContentBlockVisibility === '1') {
            echo '<div class="template-homepage-special-content-block">';
            echo '<div>';
            echo '<component src="bearcms-elements" color="#E0880B" editable="true" class="homepage-special-bearcms-elements" id="homepage-special"/>';
            echo '</div>';
            echo '</div>';
        }

        if ($hasPages && $navigationPosition === '2') {
            echo $navigationContent;
        }

        echo '<section class="template-content">';
        echo '<div>';
        echo '{body}';
        echo '</div>';
        echo '</section>';

        if ($footerVisibility === '1') {
            echo '<footer class="template-footer">';
            echo '<div>';
            echo '<component src="bearcms-elements" color="#E0880B" editable="true" class="footer-bearcms-elements" id="footer"/>';
            if ($poweredByLinkVisibility === '1') {
                echo '<div style="text-align:center;">';
                echo '<div class="template-powered-by-link-container">';
                echo '<a class="template-powered-by-link" href="https://bearcms.com/" target="_blank">Powered by Bear CMS</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</footer>';
        }
        ?></div></body>
</html>