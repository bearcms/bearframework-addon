<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal2;

$app = App::get();

$settings = Internal2::$data2->settings->get();
$isHomePage = (string) $app->request->path === '/';

$backgroundColor = $options->get('backgroundColor');
$textColor = $options->get('textColor');
$accentColor = $options->get('accentColor');
$textSizeOptionValue = $options->get('textSize');
$contentWidthOptionValue = $options->get('contentWidth');

$headerLogoImage = $options->get('headerLogoImage');

$hasHeaderLogo = strlen($headerLogoImage) > 0;
if ($hasHeaderLogo) {
    $headerLogoImageSize = $app->images->getSize(Internal2::$data2->getRealFilename($headerLogoImage));
    $headerLogoMaxWidth = $headerLogoImageSize[0] * ($isHomePage ? 180 : 90) / $headerLogoImageSize[1];
}

$hasHeaderTitle = $options->get('headerTitleVisibility') === '1';
$hasNavigation = $options->get('navigationVisibility') === '1';
$hasFooter = $options->get('footerVisibility') === '1';

$elementsDefaults = $app->bearCMS->themes->makeOptionsSchema();
$elementsDefaults->addElements('container', '.template-container');
$elementsDefaults->addPages();
$cssRules = $elementsDefaults->getCSSRules();

$cssStyles = [];
foreach ($cssRules as $cssRuleSelector => $cssRuleValue) {
    $cssStyles[] = $cssRuleSelector . '{' . $cssRuleValue . '}';
}

$fontFamily = 'Helvetica,Arial,sans-serif';
$spacing = '1.5rem';

switch ((int) $textSizeOptionValue) {
    case 1:
        $fontSize = '0.9rem';
        break;
    case 3:
        $fontSize = '1.1rem';
        break;
    default:
        $fontSize = '1rem';
        break;
}

switch ((int) $contentWidthOptionValue) {
    case 1:
        $contentWidth = '45rem';
        break;
    case 3:
        $contentWidth = '70rem';
        break;
    default:
        $contentWidth = '55rem';
        break;
}
?><html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,minimal-ui">
        <style>
            body{margin:0;}
            <?= implode('', $cssStyles); ?>
            .template-container *{margin:0;padding:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;outline:none;-webkit-tap-highlight-color:rgba(0,0,0,0);}
            .template-container{min-height:100vh;font-family:<?= $fontFamily ?>;font-size:<?= $fontSize ?>;background-color:<?= $backgroundColor ?>;color:<?= $textColor ?>;display:flex;flex-direction:column;}

            .template-header{width:100%;max-width:<?= $contentWidth ?>;margin:0 auto;padding:0 1rem;}
            <?php if ($hasHeaderLogo) { ?>
                .template-header-logo-container{margin-top:3rem;}
                .template-header-logo{max-width:<?= $headerLogoMaxWidth ?>px;margin:0 auto;}
            <?php } ?>
            <?php if ($hasHeaderTitle) { ?>
                .template-header-title-container{margin-top:<?= ($hasHeaderLogo ? '2rem' : '3rem') ?>;text-align:center;}
                .template-header-title{text-decoration:none;color:<?= $accentColor ?>;font-size:<?= $isHomePage ? 1.6 : 1.3 ?>rem;}
            <?php } ?>
            .template-content{width:100%;min-height:40rem;max-width:<?= $contentWidth ?>;margin:0 auto;padding:3rem 1.2rem;flex:1 0 auto;}

            .template-footer{width:100%;background-color:#111;}
            .template-footer > div{max-width:<?= $contentWidth ?>;margin:0 auto;padding:3rem 1.2rem;}

            <?php if ($hasNavigation) { ?>
                .template-navigation ul, .template-navigation li{
                    list-style-type: none;
                    list-style-position: outside;
                }
                .template-navigation ul{
                    padding: 0;
                    margin: 0;
                    z-index: 10;
                }
                .template-navigation{margin-top:<?= ($hasHeaderLogo || $hasHeaderTitle ? '2rem' : '3rem') ?>;text-align:center;}
                .template-navigation .template-navigation-content{font-size:0;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item{font-size:0;border-radius:2px;border:1px solid transparent;margin-left:0.5rem;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:first-child{margin-left:0;}
                .template-navigation .bearcms-navigation-element-item a{color:<?= $textColor ?>;padding:0.7rem 0.8rem;font-size:<?= $fontSize ?>;text-decoration:none;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:hover{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:active{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected:hover{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected:active{border:1px solid <?= $textColor ?>;}
                .template-navigation .bearcms-navigation-element-item-selected > a{color:<?= $textColor ?>;}
                .template-navigation .bearcms-navigation-element-item-children{
                    border-radius:2px;
                    border:1px solid <?= $textColor ?>;
                    text-align:left;
                    background-color:<?= $backgroundColor ?>;
                    margin-left:-1px !important;
                    margin-top:-1px !important;
                }
                .template-navigation .bearcms-navigation-element-item-more{
                    cursor: pointer;
                }
                .template-navigation .bearcms-navigation-element-item-more > a:before{
                    content: "...";
                }
                #template-navigation-toggle-button{
                    display: none;
                }
                #template-navigation-toggle-button + label{
                    display: none;
                    height:2.55rem;
                    width:3.1rem;
                }
                @media(max-width: 40rem) {
                    .template-navigation{
                        display: block !important;
                    }
                    .template-navigation .bearcms-navigation-element-item{
                        display: block !important;
                    }
                    .template-navigation .bearcms-navigation-element-item-children{
                        display: none !important;
                    }
                    #template-navigation-toggle-button + label{
                        display: block;
                        margin: 0 auto;
                        cursor: pointer;
                        background-image: url('data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" fill="' . $textColor . '"><path d="M512 192l-96-96-160 160L96 96 0 192l256 256z"/></svg>') ?>');
                        background-size: auto 50%;
                        background-position: center center;
                        background-repeat: no-repeat;
                        border:1px solid <?= $textColor ?>;
                        border-radius:2px;
                    }
                    #template-navigation-toggle-button + label + div{
                        display: none;
                    }
                    #template-navigation-toggle-button:checked + label + div{
                        display: block;
                        width: 100%;
                        box-sizing: border-box;
                        margin-top:1rem;
                    }
                    #template-navigation-toggle-button:checked + label + div .template-navigation-content > .bearcms-navigation-element-item{
                        margin-left:0;
                        margin-top:1px;
                    }
                }
            <?php } ?>
            <?php
            for ($i = 0; $i < ($hasFooter ? 2 : 1); $i++) {
                $containerClassName = $i === 0 ? '.template-content' : '.template-footer';
                $elementsAccentColor = $i === 0 ? $accentColor : '#aaa';
                $elementsTextColor = $i === 0 ? $textColor : '#fff';
                echo '' . $containerClassName . ' .bearcms-heading-element-large{color:' . $elementsAccentColor . ';font-size:calc(' . $fontSize . ' * 1.6);line-height:180%;padding-top:1rem;}
            ' . $containerClassName . ' .bearcms-elements-element-container:first-child > .bearcms-heading-element-large{padding-top:0;}
            ' . $containerClassName . ' .bearcms-heading-element-medium{color:' . $elementsAccentColor . ';font-size:calc(' . $fontSize . ' * 1.3);line-height:180%;padding-top:1rem;}
            ' . $containerClassName . ' .bearcms-elements-element-container:first-child > .bearcms-heading-element-medium{padding-top:0;}
            ' . $containerClassName . ' .bearcms-heading-element-small{color:' . $elementsAccentColor . ';font-size:' . $fontSize . ';line-height:180%;padding-top:1rem;}
            ' . $containerClassName . ' .bearcms-elements-element-container:first-child > .bearcms-heading-element-small{padding-top:0;}
            ' . $containerClassName . ' .bearcms-text-element{line-height:180%;color:' . $elementsTextColor . ';margin:-0.3rem 0;}
            ' . $containerClassName . ' .bearcms-text-element a{text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-html-element{line-height:180%;color:' . $elementsTextColor . ';margin:-0.3rem 0;}
            ' . $containerClassName . ' .bearcms-html-element a{text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-link-element{line-height:180%;text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-image-element-image{border-radius:2px;}
            ' . $containerClassName . ' .bearcms-image-gallery-element-image{border-radius:2px;}
            ' . $containerClassName . ' .bearcms-video-element{border-radius:2px;}
            ' . $containerClassName . ' .bearcms-blog-posts-element{}
            ' . $containerClassName . ' .bearcms-comments-element{}
            ' . $containerClassName . ' .bearcms-navigation-element-item a{line-height:180%;text-decoration:underline;color:' . $elementsTextColor . ';}
            
            ' . $containerClassName . ' .bearcms-comments-comment{margin-bottom:1rem;}
            ' . $containerClassName . ' .bearcms-comments-show-more-button-container{padding-bottom:1rem;}
            ' . $containerClassName . ' .bearcms-comments-show-more-button{text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-comments-comment-author-image{width:50px;height:50px;margin-right:0.8rem;border-radius:2px;}
            ' . $containerClassName . ' .bearcms-comments-comment-author-name{text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-comments-comment-text{line-height:180%;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-comments-comment-date{font-size:calc(' . $fontSize . ' * 0.8);color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-comments-element-text-input{border:1px solid ' . $elementsTextColor . ';color:' . $elementsTextColor . ';margin-bottom:10px;font-family:' . $fontFamily . ';font-size:' . $fontSize . ';line-height:180%;height:calc(' . $fontSize . ' * 8);padding:calc(' . $fontSize . ' * 0.5) ' . $fontSize . ';width:100%;background-color:transparent;border-radius:2px;}
            ' . $containerClassName . ' .bearcms-comments-element-send-button{color:' . $elementsTextColor . ';text-decoration:underline;}
            ' . $containerClassName . ' .bearcms-comments-element-send-button-waiting{color:' . $elementsTextColor . ';text-decoration:none;}
            
            ' . $containerClassName . ' .bearcms-blog-posts-element-post:not(:first-child){margin-top:' . $spacing . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-show-more-button-container{margin-top:' . $spacing . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-show-more-button{text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-post-title{font-size:calc(' . $fontSize . ' * 1.3);text-decoration:underline;color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-post-date-container{padding-top:' . $fontSize . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-post-date{font-size:calc(' . $fontSize . ' * 0.8);color:' . $elementsTextColor . ';}
            ' . $containerClassName . ' .bearcms-blog-posts-element-post-content{padding-top:calc(1.5rem);}
                                                                                    
            ' . $containerClassName . ' .bearcms-blogpost-page-title{color:' . $elementsAccentColor . ';font-size:calc(' . $fontSize . ' * 1.6);line-height:180%;}
            ' . $containerClassName . ' .bearcms-blogpost-page-date-container{padding-top:' . $fontSize . ';}
            ' . $containerClassName . ' .bearcms-blogpost-page-date{font-size:calc(' . $fontSize . ' * 0.8);line-height:180%;}
            ' . $containerClassName . ' .bearcms-blogpost-page-content{padding-top:calc(' . $fontSize . ' * 1.6);}
            
            @media(min-width: 40rem) {
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-large{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-medium{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-small{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-text-element{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-html-element{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-link-element{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-blog-posts-element{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-comments-element{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-element-container > .bearcms-navigation-element{margin:0 ' . $spacing . ' !important;}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-columns{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-elements > .bearcms-elements-floating-box{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                
                ' . $containerClassName . ' .bearcms-blogpost-page-title{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
                ' . $containerClassName . ' .bearcms-blogpost-page-date-container{margin-left: ' . $spacing . ';margin-right: ' . $spacing . ';}
            }';
            }
            ?></style>
    </head>
    <body><div class="template-container"><?php
            echo '<header class="template-header">';

            if ($hasHeaderLogo) {
                $imageHTML = '<component src="bearcms-image-element" class="template-header-logo"' . ($isHomePage ? '' : ' onClick="openUrl" url="' . htmlentities($app->urls->get()) . '"') . ' filename="' . htmlentities($headerLogoImage) . '"/>';
                echo '<div class="template-header-logo-container">' . $imageHTML . '</div>';
            }
            if ($hasHeaderTitle) {
                echo '<div class="template-header-title-container"><' . ($isHomePage ? 'span' : 'a href="' . htmlentities($app->urls->get()) . '"') . ' class="template-header-title">' . htmlspecialchars($settings['title']) . '</' . ($isHomePage ? 'span' : 'a') . '></div>';
            }

            if ($hasNavigation) {
                echo '<nav class="template-navigation">';
                echo '<div>';
                echo '<input id="template-navigation-toggle-button" type="checkbox"/><label for="template-navigation-toggle-button"></label>';
                echo '<div><component src="bearcms-navigation-element" editable="true" id="main-navigation" source="allPages" showHomeLink="true" menuType="horizontal-down" class="template-navigation-content" selectedPath="' . (string) $app->request->path . '" data-responsive-attributes="w<650=>menuType=none,w>=650=>menuType=horizontal-down" /></div>';
                echo '</div>';
                echo '</nav>';
            }

            echo '</header>';

            echo '<section class="template-content">';
            echo '{{body}}';
            echo '</section>';

            if ($hasFooter) {
                echo '<footer class="template-footer"><div>';
                echo '<component src="bearcms-elements" editable="true" class="footer-bearcms-elements" id="footer"/>';
                echo '</div></footer>';
            }
            ?></div></body>
</html>