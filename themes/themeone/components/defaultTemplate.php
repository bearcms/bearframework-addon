<?php
/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$settings = $app->bearCMS->data->settings->get();
$isHomePage = (string) $app->request->path === '/';

$backgroundColor = $options['backgroundColor'];
$textColor = $options['textColor'];
$accentColor = $options['accentColor'];

$headerLogoImage = $options['headerLogoImage'];

$hasHeaderLogo = strlen($headerLogoImage) > 0;
if ($hasHeaderLogo) {
    $headerLogoImageSize = $app->images->getSize($app->bearCMS->data->getRealFilename($headerLogoImage));
    $headerLogoMaxWidth = $headerLogoImageSize[0] * ($isHomePage ? 180 : 90) / $headerLogoImageSize[1];
}

$hasHeaderTitle = $options['headerTitleVisibility'] === '1';
$hasNavigation = $options['navigationVisibility'] === '1';
$hasFooter = $options['footerVisibility'] === '1';

$elementsDefaults = $app->bearCMS->themes->makeOptionsDefinition();
$elementsDefaults->addElements('container', '.template-container');
$elementsDefaults->addPages();
$cssRules = $elementsDefaults->getCSSRules();
$cssStyles = [];
foreach ($cssRules as $cssRuleSelector => $cssRuleValue) {
    $cssStyles[] = $cssRuleSelector . '{' . $cssRuleValue . '}';
}
?><html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,minimal-ui">
        <style>
<?= implode('', $cssStyles); ?>
            .template-container *{margin:0;padding:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;outline:none;-webkit-tap-highlight-color:rgba(0,0,0,0);}
            .template-container{min-height:100vh;font-family:Helvetica,Arial,sans-serif;font-size:14px;background-color:<?= $backgroundColor ?>;color:<?= $textColor ?>;overflow:auto;}

            .template-header{max-width:860px;margin:0 auto;padding:0 15px;}
<?php if ($hasHeaderLogo) { ?>
                .template-header-logo-container{margin-top:40px;}
                .template-header-logo{max-width:<?= $headerLogoMaxWidth ?>px;margin:0 auto;}
<?php } ?>
<?php if ($hasHeaderTitle) { ?>
                .template-header-title-container{margin-top:40px;text-align:center;}
                .template-header-title{text-decoration:none;color:<?= $accentColor ?>;font-size:<?= $isHomePage ? 25 : 18 ?>px;}
<?php } ?>
            .template-content{min-height:400px;max-width:860px;margin:35px auto;padding:0 15px;}

            .template-footer{max-width:860px;margin:35px auto;padding:0 15px;}
            
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
                .template-navigation{margin-top:30px;text-align:center;}
                .template-navigation .template-navigation-content{font-size:0;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item{font-size:0;border-radius:2px;border:1px solid transparent;margin-left:5px;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:first-child{margin-left:0;}
                .template-navigation .bearcms-navigation-element-item a{color:<?= $textColor ?>;padding:10px;font-size:14px;text-decoration:none;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:hover{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item:active{border:1px solid <?= $textColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected{border:1px solid <?= $accentColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected:hover{border:1px solid <?= $accentColor ?>;}
                .template-navigation .template-navigation-content > .bearcms-navigation-element-item-selected:active{border:1px solid <?= $accentColor ?>;}
                .template-navigation .bearcms-navigation-element-item-selected > a{color:<?= $accentColor ?>;}
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
                    height:39px;
                    width:39px;
                }
                @media(max-width: 680px) {
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
                        margin-top:10px;
                    }
                                                                                                                
                    #template-navigation-toggle-button:checked + label + div .template-navigation-content > .bearcms-navigation-element-item{
                        margin-left:0;
                        margin-top:5px;
                    }
                }
<?php } ?>
<?php
$elementsStyles = '.template-content .bearcms-heading-element-large{color:' . $accentColor . ';font-size:21px;line-height:180%;text-align:center;}
            .template-content .bearcms-heading-element-medium{color:' . $accentColor . ';font-size:18px;line-height:180%;text-align:center;}
            .template-content .bearcms-heading-element-small{color:' . $accentColor . ';;font-size:15px;line-height:180%;text-align:center;}
            .template-content .bearcms-text-element{line-height:180%;}
            .template-content .bearcms-text-element a{text-decoration:underline;color:' . $accentColor . ';}
            .template-content .bearcms-html-element{line-height:180%;}
            .template-content .bearcms-html-element a{text-decoration:underline;color:' . $accentColor . ';}
            .template-content .bearcms-link-element{line-height:180%;text-decoration:underline;color:' . $accentColor . ';}
            .template-content .bearcms-image-element-image{border-radius:2px;}
            .template-content .bearcms-image-gallery-element-image{border-radius:2px;}
            .template-content .bearcms-video-element{border-radius:2px;}
            .template-content .bearcms-blog-posts-element{}
            .template-content .bearcms-comments-element{}
            .template-content .bearcms-navigation-element-item a{line-height:180%;text-decoration:underline;color:' . $accentColor . ';}
                                        
            @media(min-width: 500px) {
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-large{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-medium{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-heading-element-small{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-text-element{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-html-element{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-link-element{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-blog-posts-element{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-comments-element{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-element-container > .bearcms-navigation-element{margin:0 20px !important;}
                .template-content .bearcms-elements > .bearcms-elements-columns{margin:0 20px;}
                .template-content .bearcms-elements > .bearcms-elements-floating-box{margin:0 20px;}
            }

            .template-content .bearcms-comments-comment{margin-bottom:10px;}
            .template-content .bearcms-comments-show-more-button-container{padding-bottom:10px;}
            .template-content .bearcms-comments-show-more-button{text-decoration:underline;color:' . $textColor . ';}
            .template-content .bearcms-comments-comment-author-image{width:50px;height:50px;margin-right:10px;border-radius:2px;}
            .template-content .bearcms-comments-comment-author-name{text-decoration:underline;color:' . $textColor . ';}
            .template-content .bearcms-comments-comment-text{line-height:180%;}
            .template-content .bearcms-comments-comment-date{font-size:11px;}
            .template-content .bearcms-comments-element-text-input{border:1px solid ' . $textColor . ';color:' . $textColor . ';margin-bottom:10px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:180%;height:100px;padding:5px 10px;width:100%;background-color:transparent;border-radius:2px;}
            .template-content .bearcms-comments-element-send-button{color:' . $accentColor . ';text-decoration:underline;}
            .template-content .bearcms-comments-element-send-button-waiting{color:' . $textColor . ';text-decoration:none;}
            
            .template-content .bearcms-blog-posts-element-post:not(:first-child){margin-top:15px;}
            .template-content .bearcms-blog-posts-element-show-more-button-container{margin-top:15px;}
            .template-content .bearcms-blog-posts-element-show-more-button{text-decoration:underline;color:' . $accentColor . ';}
            .template-content .bearcms-blog-posts-element-post-title{font-size:18px;text-decoration:underline;color:' . $accentColor . ';}
            .template-content .bearcms-blog-posts-element-post-date-container{padding-top:10px;}
            .template-content .bearcms-blog-posts-element-post-date{font-size:11px;}
            .template-content .bearcms-blog-posts-element-post-content{padding-top:10px;}
                                                                                    
            .template-content .bearcms-blogpost-page-title{color:' . $accentColor . ';font-size:21px;line-height:180%;text-align:center;}
            .template-content .bearcms-blogpost-page-date-container{padding-top:15px;}
            .template-content .bearcms-blogpost-page-date{font-size:11px;line-height:180%;text-align:center;}
            .template-content .bearcms-blogpost-page-content{padding-top:15px;}';

echo $elementsStyles;
if ($hasFooter) {
    echo str_replace('.template-content', '.template-footer', $elementsStyles);
}
?>            
            .template-separator{margin:0 auto;max-width:250px;border-top:1px solid <?= $textColor ?>;}
<?php if (!$isWhitelabel) { ?>
                .template-powered-by-link-container{max-width:860px;margin:35px auto;text-align:center;}
                .template-powered-by-link{color:<?= $textColor ?> !important;text-decoration:none !important;}
<?php } ?>
        </style>
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
                echo '<div class="template-separator"></div>';
                echo '<footer class="template-footer">';
                echo '<component src="bearcms-elements" editable="true" color="#c15303" class="footer-bearcms-elements" id="footer"/>';
                echo '</footer>';
            }
            if (!$isWhitelabel) {
                echo '<div class="template-separator"></div>';
                echo '<div class="template-powered-by-link-container">';
                echo '<a class="template-powered-by-link" href="https://bearcms.com/" target="_blank" rel="noopener" title="This website is powered by Bear CMS.">Powered by Bear CMS</a>';
                echo '</div>';
            }
            ?></div></body>
</html>