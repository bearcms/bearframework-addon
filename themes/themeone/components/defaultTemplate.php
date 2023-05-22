<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$elementsLanguageSuffix = '';
$homePath = '/';
if (isset($languages[0]) && $languages[0] !== $language) {
    $elementsLanguageSuffix = '-' . $language;
    $homePath = '/' . $language . '/';
}

$settings = $app->bearCMS->data->settings->get();
$isHomePage = (string) $app->request->path === $homePath;

$hasLanguagesPicker = sizeof($languages) > 1;

switch ((int)$customizations->getValue('contentWidth')) {
    case 1:
        $contentWidth = '680px';
        break;
    case 3:
        $contentWidth = '1100px';
        break;
    default:
        $contentWidth = '850px';
        break;
}

$logoImage = (string)$customizations->getValue('logoImage');
$logoImageWidth = (string)$customizations->getValue('logoImageWidth');
$logoImageEffect = (string)$customizations->getValue('logoImageEffect');

$hasSearchSupport = $app->bearCMS->addons->exists('bearcms/search-box-element-addon');
$hasStoreSupport = $app->bearCMS->addons->exists('bearcms/store-addon');
$hasFormsSupport = $app->bearCMS->addons->exists('bearcms/forms-addon');

$showSearchButton = $hasSearchSupport && $customizations->getValue('searchButtonVisibility') === '1';
$showStoreCartButton = $hasStoreSupport && $customizations->getValue('storeCartButtonVisibility') === '1';

$hasLogoImage = isset($logoImage[0]);
if ($hasLogoImage) {
    $logoImageDetails = $customizations->getAssetDetails($logoImage, ['filename', 'width', 'height']);
}

$hasLogoText = $customizations->getValue('logoTextVisibility') === '1';
$hasNavigation = $customizations->getValue('navigationVisibility') === '1';
$hasFooter = $customizations->getValue('footerVisibility') === '1';

$elementsDefaults = new \BearCMS\Themes\Theme\Options();
$elementsDefaults->addElements('container', '.bearcms-template-container');
$elementsDefaults->addPages();
$html = $elementsDefaults->getHTML();
$elementsDefaultsHTML = '';
if ($html !== '') {
    $elementsDefaultsHTML = str_replace(['<html><head>', '</head></html>'], '', $html);
}

$mainElementsVerticalSpacing = '40px';
$borderRadius = '4px';
$elementsSpacing = '20px';
$windowPadding = '20px'; // same as $elementsSpacing

$buttonHeight = 'calc(var(--bearcms-template-text-font-size) * 3)';
$buttonPadding = 'var(--bearcms-template-text-font-size)';
$buttonPaddingHalf = 'calc(var(--bearcms-template-text-font-size) * 0.55)';
$buttonIconSize = 'calc(var(--bearcms-template-text-font-size) * 4/3)';

$textStyle = 'font-family:var(--bearcms-template-text-font-family);color:var(--bearcms-template-text-color);font-weight:var(--bearcms-template-text-font-weight);font-style:var(--bearcms-template-text-font-style);font-size:var(--bearcms-template-text-font-size);line-height:var(--bearcms-template-text-line-height);letter-spacing:var(--bearcms-template-text-letter-spacing);';
$accentTextStyle = 'font-family:var(--bearcms-template-accent-text-font-family);color:var(--bearcms-template-accent-text-color);font-weight:var(--bearcms-template-accent-text-font-weight);font-style:var(--bearcms-template-accent-text-font-style);font-size:var(--bearcms-template-accent-text-font-size);line-height:var(--bearcms-template-accent-text-line-height);letter-spacing:var(--bearcms-template-accent-text-letter-spacing);';

echo '<html>';
echo '<head>';
echo '<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,minimal-ui">';
echo $elementsDefaultsHTML;

echo '<style>';
echo 'html,body{padding:0;margin:0;min-height:100%;}';
echo 'body{background-color:var(--bearcms-template-footer-background-color);}';
echo '*{outline:none;-webkit-tap-highlight-color:rgba(0,0,0,0);}';
echo '.bearcms-template-container{min-height:100vh;display:flex;flex-direction:column;}';
echo '.bearcms-template-header{box-sizing:border-box;width:100%;max-width:calc(' . $contentWidth . ' + var(--bearcms-template-text-font-size) * 2.2);margin:0 auto;padding:' . $windowPadding . ' ' . $windowPadding . ' 0 ' . $windowPadding . ';}'; // 2.2 = twice the nav buttons padding
if ($hasLanguagesPicker) {
    echo '.bearcms-template-languages{position:absolute;top:0;right:' . ($app->currentUser->exists() ? '74px' : '10px') . ';}';
    echo '.bearcms-template-languages *{' . $textStyle . 'display:inline-block;box-sizing:border-box;text-align:center;font-size:calc(var(--bearcms-template-text-font-size) * 0.8);text-decoration:none;line-height:calc(var(--bearcms-template-text-font-size) * 2);padding:0 calc(var(--bearcms-template-text-font-size) * 0.6);min-width:calc(var(--bearcms-template-text-font-size) * 2);height:calc(var(--bearcms-template-text-font-size) * 2);border-bottom-left-radius:' . $borderRadius . ';border-bottom-right-radius:' . $borderRadius . ';}';
    echo '.bearcms-template-languages span{background-color:rgba(0,0,0,0.04);cursor:default;}';
    echo '.bearcms-template-languages a:hover{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-languages a:focus{background-color:rgba(0,0,0,0.12);}';
    echo '.bearcms-template-languages a:active{background-color:rgba(0,0,0,0.12);}';
}

if ($hasLogoImage) {
    echo '.bearcms-template-logo-container{margin-top:' . $mainElementsVerticalSpacing . ';}';
    echo '.bearcms-template-logo{box-sizing:border-box;' . ($logoImageWidth !== '' ? 'max-width:' . ($isHomePage ? $logoImageWidth : 'calc(' . $logoImageWidth . ' / 2)') . ';' : '') . 'margin:0 auto;' . ($logoImageEffect === '1' ? 'border-radius:50%;overflow:hidden;' : '') . '}';
}
if ($hasLogoText) {
    echo '.bearcms-template-logo-text-container{margin-top:' . ($hasLogoImage ? 'calc(' . $mainElementsVerticalSpacing . ' * 1/2)' : $mainElementsVerticalSpacing) . ';text-align:center;}';
}
echo '.bearcms-template-main{box-sizing:border-box;width:100%;min-height:400px;max-width:' . $contentWidth . ';margin:0 auto;padding:calc(' . $mainElementsVerticalSpacing . ' + ' . $windowPadding . ') ' . $windowPadding . ' ' . $mainElementsVerticalSpacing . ' ' . $windowPadding . ';flex:1 0 auto;}';
echo '.bearcms-template-footer{box-sizing:border-box;width:100%;background-color:var(--bearcms-template-footer-background-color);}';
echo '.bearcms-template-footer > div{box-sizing:border-box;max-width:' . $contentWidth . ';margin:0 auto;padding:' . $mainElementsVerticalSpacing . ' ' . $windowPadding . ';}';

if ($hasNavigation) {
    echo '.bearcms-template-navigation ul, .bearcms-template-navigation li{list-style-type:none;list-style-position:outside;}';
    echo '.bearcms-template-navigation ul{padding:0;margin:0;z-index:10;}';
    echo '.bearcms-template-navigation{margin-top:' . $mainElementsVerticalSpacing . ';}';
    echo '.bearcms-template-navigation>div{font-size:0;position:relative;z-index:1;}';
    echo '.bearcms-template-navigation>div:before{content:"";width:100%;background-color:rgba(0,0,0,0.04);height:' . $buttonHeight . ';position:absolute;display:block;border-radius:' . $borderRadius . ';}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item{font-size:0;display:inline-block;}';
    echo '.bearcms-template-navigation :not(.bearcms-navigation-element-item-children)>.bearcms-navigation-element-item:first-child{border-top-left-radius:' . $borderRadius . ';border-bottom-left-radius:' . $borderRadius . ';}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item a{' . $textStyle . 'padding:0 ' . $buttonPadding . ';line-height:' . $buttonHeight . ';height:' . $buttonHeight . ';min-width:' . $buttonHeight . ';text-decoration:none;display:inline-block;max-width:100%;text-overflow:ellipsis;overflow:hidden;box-sizing:border-box;display:block;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:hover{background-color:rgba(0,0,0,0.04);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:focus{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:active{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-selected{background-color:rgba(0,0,0,0.04);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-selected:hover{background-color:rgba(0,0,0,0.04);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-selected:focus{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-selected:active{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children{text-align:left;background-color:rgba(0,0,0,0);padding-top:10px;padding-left:5px;padding-right:5px;padding-bottom:5px;max-width:calc(100vw - 20px);box-sizing:border-box;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item{background-color:#222;display:block;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item:first-child{border-top-left-radius:' . $borderRadius . ';border-top-right-radius:' . $borderRadius . ';}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item:last-child{border-bottom-left-radius:' . $borderRadius . ';border-bottom-right-radius:' . $borderRadius . ';}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item a{' . $textStyle . 'padding:0 ' . $buttonPadding . ';line-height:' . $buttonHeight . ';height:' . $buttonHeight . ';color:#fff;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item:hover{background-color:#292929;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item:focus{background-color:#333;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item:active{background-color:#333;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-more{cursor:pointer;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-more > a:before{content:"...";}';
    $customButtonsCount = 0;
    if ($showSearchButton) {
        echo '.bearcms-template-navigation-custom-item-search{float:right;background-size:auto ' . $buttonIconSize . ';background-position:center center;background-repeat:no-repeat;}';
        $customButtonsCount++;
    }
    if ($showStoreCartButton) {
        echo '.bearcms-template-navigation-custom-item-store-cart{float:right;background-size:auto ' . $buttonIconSize . ';background-position:center center;background-repeat:no-repeat;}';
        $customButtonsCount++;
    }
    echo '.bearcms-template-navigation-custom-item{display:inline-block;position:relative;z-index:2;box-sizing:border-box;height:' . $buttonHeight . ';width:' . $buttonHeight . ';cursor:pointer;}';
    echo '.bearcms-template-navigation-custom-item:hover{background-color:rgba(0,0,0,0.04);}';
    echo '.bearcms-template-navigation-custom-item:focus{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation-custom-item:active{background-color:rgba(0,0,0,0.08);}';
    echo '.bearcms-template-navigation-custom-item:first-child{border-top-right-radius:' . $borderRadius . ';border-bottom-right-radius:' . $borderRadius . ';}';
    echo '#bearcms-template-navigation-menu-button{display:none;}';
    echo '#bearcms-template-navigation-menu-button+label{display:none;}';
    echo '#bearcms-template-navigation-menu-button+label+div{width:calc(100% - ' . $customButtonsCount . '*' . $buttonHeight . ');}';
    echo '@media(max-width:600px){';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item{background-color:#222;display:block !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:hover{background-color:#292929;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:focus{background-color:#333;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:active{background-color:#333;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:first-child{border-top-left-radius:' . $borderRadius . ';border-top-right-radius:' . $borderRadius . ';border-bottom-left-radius:0 !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:last-child{border-bottom-left-radius:' . $borderRadius . ';border-bottom-right-radius:' . $borderRadius . ';border-top-right-radius:0 !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item a{display:block !important;color:#fff !important;text-align:left;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children{display:none !important;}';
    echo '#bearcms-template-navigation-menu-button+label{display:inline-block;background-size:auto calc(var(--bearcms-template-text-font-size) + 9px);background-position:center center;background-repeat:no-repeat;border-top-left-radius:' . $borderRadius . ';border-bottom-left-radius:' . $borderRadius . ';}';
    echo '#bearcms-template-navigation-menu-button+label+div{display:none;}';
    echo '#bearcms-template-navigation-menu-button:checked+label+div{display:block;width:100%;box-sizing:border-box;padding-top:10px;}';
    echo '}';
}

$elementTextStyle = $textStyle . 'color:var(--bearcms-template-context-text-color);';
$elementAccentTextStyle = $accentTextStyle . 'color:var(--bearcms-template-context-accent-text-color);';

$elementHeadingLarge = $elementAccentTextStyle . 'font-size:calc(var(--bearcms-template-accent-text-font-size) * 2);';
$elementHeadingMedium = $elementAccentTextStyle . 'font-size:calc(var(--bearcms-template-accent-text-font-size) * 1.5);';
$elementHeadingSmall = $elementAccentTextStyle;
$elementLabel = $elementTextStyle;
$elementInput = 'border:1px solid var(--bearcms-template-context-text-color);' . $elementTextStyle . 'height:' . $buttonHeight . ';padding:0 ' . $buttonPadding . ';width:100%;background-color:transparent;border-radius:' . $borderRadius . ';';
$elementInputOver = 'background-color:rgba(0,0,0,0.02);';
$elementInputActive = 'background-color:rgba(0,0,0,0.04);';
$elementTextarea = 'padding-top:' . $buttonPaddingHalf . ';padding-bottom:' . $buttonPaddingHalf . ';';
$elementText = $elementTextStyle;
$elementLink = $elementTextStyle . 'text-decoration:underline;';
$elementButton = $elementTextStyle . 'background-color:transparent;border:1px solid var(--bearcms-template-context-text-color);text-decoration:none;border-radius:' . $borderRadius . ';padding:0 ' . $buttonPadding . ';line-height:' . $buttonHeight . ';height:' . $buttonHeight . ';';
$elementButtonOver = 'background-color:rgba(0,0,0,0.04);';
$elementButtonActive = 'background-color:rgba(0,0,0,0.08);';
$elementUserImage = 'box-sizing:border-box;width:50px;height:50px;margin-right:calc(' . $elementsSpacing . ' / 2);border-radius:' . $borderRadius . ';';
$elementSeparator = 'background-color:var(--bearcms-template-context-text-color);height:2px;margin:calc(' . $elementsSpacing . ' * 3) auto calc(' . $elementsSpacing . ' * 3) auto;opacity:0.3;';

// bearcms-tc fixes collistions with the theme options

$customStyleSelector = ' .bearcms-element:not([class*="bearcms-element-style-"]) >';

echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-heading-element-large{' . $elementHeadingLarge . 'padding-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-element:first-child > .bearcms-heading-element-large{padding-top:0;}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-heading-element-medium{' . $elementHeadingMedium . 'padding-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-element:first-child > .bearcms-heading-element-medium{padding-top:0;}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-heading-element-small{' . $elementHeadingSmall . 'padding-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-element:first-child > .bearcms-heading-element-small{padding-top:0;}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-text-element{' . $elementText . '}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-text-element a{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-html-element{' . $elementText . '}';
echo '.bearcms-tc .bearcms-html-element a{' . $elementLink . '}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-link-element a{' . $elementLink . '}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-image-element .bearcms-image-element-image{border-radius:' . $borderRadius . ';}';
echo '.bearcms-tc' . $customStyleSelector . ' .bearcms-image-gallery-element .bearcms-image-gallery-element-image{border-radius:' . $borderRadius . ';}';
echo '.bearcms-tc .bearcms-video-element{border-radius:' . $borderRadius . ';}';
echo '.bearcms-tc .bearcms-navigation-element-item a{' . $elementLink . '}';

echo '.bearcms-tc .bearcms-comments-comment{margin-bottom:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-comments-show-more-button-container{padding-bottom:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-comments-show-more-button{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-comments-comment-author-image{' . $elementUserImage . '}';
echo '.bearcms-tc .bearcms-comments-comment-author-name{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-comments-comment-text{' . $elementText . '}';
echo '.bearcms-tc .bearcms-comments-comment-text a{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-comments-comment-date{' . $elementText . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);}';
echo '.bearcms-tc .bearcms-comments-element-text-input{' . $elementInput . $elementTextarea . 'height:calc(var(--bearcms-template-text-font-size) * 8);}';
echo '.bearcms-tc .bearcms-comments-element-text-input:hover{' . $elementInputOver . '}';
echo '.bearcms-tc .bearcms-comments-element-text-input:focus{' . $elementInputActive . '}';
echo '.bearcms-tc .bearcms-comments-element-text-input:active{' . $elementInputActive . '}';
echo '.bearcms-tc .bearcms-comments-element [data-form-element-type="submit-button"]{font-size:0;}';
echo '.bearcms-tc .bearcms-comments-element-send-button{margin-top:calc(' . $elementsSpacing . ' / 2);' . $elementButton . '}';
echo '.bearcms-tc .bearcms-comments-element-send-button:not(.bearcms-comments-element-send-button-waiting):hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .bearcms-comments-element-send-button:not(.bearcms-comments-element-send-button-waiting):focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .bearcms-comments-element-send-button:not(.bearcms-comments-element-send-button-waiting):active{' . $elementButtonActive . '}';

echo '.bearcms-tc .bearcms-blog-posts-element-show-more-button{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-blog-posts-element-post-title{' . $elementHeadingSmall . 'font-size:calc(var(--bearcms-template-accent-text-font-size) * 1.2);color:var(--bearcms-template-context-text-color);text-decoration:underline;}';
echo '.bearcms-tc .bearcms-blog-posts-element-post-date-container{padding-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-blog-posts-element-post-date{' . $elementText . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);}';
echo '.bearcms-tc .bearcms-blog-posts-element-post-content{padding-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-blog-posts-element-post:not(:last-child){margin-bottom:' . $elementsSpacing . ';}';
echo '.bearcms-tc .bearcms-blog-posts-element-show-more-button-container{margin-top:' . $elementsSpacing . ';}';

echo '.bearcms-tc .bearcms-forum-posts-post-title{' . $elementText . 'text-decoration:underline;}';
echo '.bearcms-tc .bearcms-forum-posts-post-replies-count{' . $elementText . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);}';
//echo '.bearcms-tc .bearcms-forum-posts-show-more-button-container{font-size:0;}';
echo '.bearcms-tc .bearcms-forum-posts-show-more-button{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-forum-posts-new-post-button-container{margin-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-forum-posts-new-post-button{' . $elementButton . '}';
echo '.bearcms-tc .bearcms-forum-posts-new-post-button:hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .bearcms-forum-posts-new-post-button:focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .bearcms-forum-posts-new-post-button:active{' . $elementButtonActive . '}';

echo '.bearcms-tc .bearcms-blogpost-page-title{' . $elementHeadingLarge . '}';
echo '.bearcms-tc .bearcms-blogpost-page-date-container{padding-top:var(--bearcms-template-text-font-size);}';
echo '.bearcms-tc .bearcms-blogpost-page-date{' . $elementText . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);}';
echo '.bearcms-tc .bearcms-blogpost-page-content{padding-top:calc(var(--bearcms-template-text-font-size) * 1.6);}';
echo '.bearcms-tc .bearcms-blogpost-page-comments-title-container{padding-top:calc(var(--bearcms-template-text-font-size) * 1.6);}';
echo '.bearcms-tc .bearcms-blogpost-page-comments-container{padding-top:calc(var(--bearcms-template-text-font-size) * 1.6);}';
echo '.bearcms-tc .bearcms-blogpost-page-related-container{padding-top:calc(var(--bearcms-template-text-font-size) * 1.6);}';

echo '.bearcms-tc .bearcms-forum-post-page-title{' . $elementHeadingLarge . 'padding-bottom:var(--bearcms-template-text-font-size);}';
echo '.bearcms-tc .bearcms-forum-post-page-reply{margin-bottom:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .bearcms-forum-post-page-reply-author-image{' . $elementUserImage . '}';
echo '.bearcms-tc .bearcms-forum-post-page-reply-author-name{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-forum-post-page-reply-text{' . $elementText . '}';
echo '.bearcms-tc .bearcms-forum-post-page-reply-text a{' . $elementLink . '}';
echo '.bearcms-tc .bearcms-forum-post-page-reply-date{' . $elementText . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);}';
echo '.bearcms-tc .bearcms-forum-post-page-text-input{' . $elementInput . $elementTextarea . 'height:calc(var(--bearcms-template-text-font-size) * 14);}';
echo '.bearcms-tc .bearcms-forum-post-page-text-input:hover{' . $elementInputOver . '}';
echo '.bearcms-tc .bearcms-forum-post-page-text-input:focus{' . $elementInputActive . '}';
echo '.bearcms-tc .bearcms-forum-post-page-text-input:active{' . $elementInputActive . '}';
echo '.bearcms-tc .bearcms-forum-post-page-content [data-form-element-type="submit-button"]{font-size:0;}';
echo '.bearcms-tc .bearcms-forum-post-page-send-button{margin-top:calc(' . $elementsSpacing . ' / 2);' . $elementButton . '}';
echo '.bearcms-tc .bearcms-forum-post-page-send-button:not(.bearcms-forum-post-page-send-button-waiting):hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .bearcms-forum-post-page-send-button:not(.bearcms-forum-post-page-send-button-waiting):focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .bearcms-forum-post-page-send-button:not(.bearcms-forum-post-page-send-button-waiting):active{' . $elementButtonActive . '}';

echo '.bearcms-tc .bearcms-code-element{' . $elementText . 'font-family:Courier,monospace;border-radius:' . $borderRadius . ';background-color:#333;padding:var(--bearcms-template-text-font-size);color:#fff;}';
echo '.bearcms-tc .bearcms-code-element .bearcms-code-element-entity-keyword{color:#4dc16c;}';
echo '.bearcms-tc .bearcms-code-element .bearcms-code-element-entity-variable{color:#00b5c3;}';
echo '.bearcms-tc .bearcms-code-element .bearcms-code-element-entity-value{color:#ff770a;}';
echo '.bearcms-tc .bearcms-code-element .bearcms-code-element-entity-comment{color:#929292;}';

echo '.bearcms-tc .bearcms-separator-element-large{' . $elementSeparator . 'width:70%;}';
echo '.bearcms-tc .bearcms-separator-element-medium{' . $elementSeparator . 'width:50%;}';
echo '.bearcms-tc .bearcms-separator-element-small{' . $elementSeparator . 'width:30%;}';

if ($hasSearchSupport) {
    echo '.bearcms-tc .bearcms-search-box-element-input{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-search-box-element-input:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-search-box-element-input:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-search-box-element-input:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-search-box-element-button{' . $elementButton . 'width:' . $buttonHeight . ';border:0px;border-left:1px solid var(--bearcms-template-context-text-color);border-top-left-radius:0;border-bottom-left-radius:0;background-size:auto ' . $buttonIconSize . ';background-position:center center;background-repeat:no-repeat;}';
    echo '.bearcms-tc .bearcms-search-box-element-button:hover{' . $elementButtonOver . '}';
    echo '.bearcms-tc .bearcms-search-box-element-button:focus{' . $elementButtonActive . '}';
    echo '.bearcms-tc .bearcms-search-box-element-button:active{' . $elementButtonActive . '}';
}

if ($hasStoreSupport) {
    echo '.bearcms-tc .bearcms-store-items-element-item-image{border-radius:' . $borderRadius . ';}';
    echo '.bearcms-tc .bearcms-store-items-element-item-name{' . $elementHeadingSmall . 'font-size:calc(var(--bearcms-template-accent-text-font-size) * 1.2);color:var(--bearcms-template-context-text-color);text-decoration:underline;}';
    echo '.bearcms-tc .bearcms-store-items-element-item-description{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-items-element-item-price-container{padding-top:calc(' . $elementsSpacing . ' / 2);}';
    echo '.bearcms-tc .bearcms-store-items-element-item-price{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-items-element-item-price-original{' . $elementText . '}';

    echo '.bearcms-tc .bearcms-store-item-page-images-image{border-radius:' . $borderRadius . ';}';
    echo '.bearcms-tc .bearcms-store-item-page-name{' . $elementHeadingLarge . '}';
    echo '.bearcms-tc .bearcms-store-item-page-description{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-item-page-option-container{padding-top:calc(' . $elementsSpacing . ' / 2);}';
    echo '.bearcms-tc .bearcms-store-item-page-option-label{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-item-page-option-select{' . $elementInput . 'width:auto;}';
    echo '.bearcms-tc .bearcms-store-item-page-option-select:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-store-item-page-option-select:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-store-item-page-option-select:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-store-item-page-price-container{padding-top:calc(' . $elementsSpacing . ' / 2);}';
    echo '.bearcms-tc .bearcms-store-item-page-price{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-item-page-price-original{' . $elementText . '}';
    echo '.bearcms-tc .bearcms-store-item-page-buy-button-container{padding-top:calc(' . $elementsSpacing . ' / 2);font-size:0;}';
    echo '.bearcms-tc .bearcms-store-item-page-buy-button{' . $elementButton . '}';
    echo '.bearcms-tc .bearcms-store-item-page-buy-button:hover{' . $elementButtonOver . '}';
    echo '.bearcms-tc .bearcms-store-item-page-buy-button:focus{' . $elementButtonActive . '}';
    echo '.bearcms-tc .bearcms-store-item-page-buy-button:active{' . $elementButtonActive . '}';
}

if ($hasFormsSupport) {
    $formFieldListOptionButton = $elementInput . 'width:calc(var(--bearcms-template-text-font-size) * 2.5);height:calc(var(--bearcms-template-text-font-size) * 2.5);padding:0;background-position:center center;background-repeat:no-repeat;background-attachment:scroll;background-size:cover;';
    $formFieldListOptionButtonOver = $elementInputOver;
    $formFieldListOptionButtonActive = $elementInputActive;
    $formFieldListOptionText = $elementTextStyle . 'padding-left:var(--bearcms-template-text-font-size);padding-top:calc(var(--bearcms-template-text-font-size) * 0.4);';
    $formFieldListOptionTextbox = $elementInput . 'height:calc(var(--bearcms-template-text-font-size) * 2.5);line-height:calc(var(--bearcms-template-text-font-size) * 2.5 - 2px);width:250px;margin-left:var(--bearcms-template-text-font-size);padding:0 calc(var(--bearcms-template-text-font-size) * 0.8);font-size:calc(var(--bearcms-template-text-font-size) * 0.9);';
    $formFieldListOptionContainer = 'padding-bottom:5px;';
    $formFieldHint = $elementTextStyle . 'font-size:calc(var(--bearcms-template-text-font-size) * 0.8);';
    $formFieldContainer = 'padding-bottom:15px;';

    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="input"]{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="input"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="input"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="input"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container [data-form-element-type="textbox"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-text-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="textarea"]{' . $elementInput . $elementTextarea . 'height:calc(var(--bearcms-template-text-font-size) * 8);}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="textarea"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="textarea"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="textarea"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container [data-form-element-type="textarea"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-textarea-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="input"]{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="input"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="input"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="input"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container [data-form-element-type="textbox"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-name-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="input"]{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="input"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="input"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="input"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container [data-form-element-type="textbox"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-email-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="input"]{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="input"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="input"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="input"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container [data-form-element-type="textbox"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-phone-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-input"]{' . $formFieldListOptionButton . 'border-top-left-radius:50%;border-top-right-radius:50%;border-bottom-left-radius:50%;border-bottom-right-radius:50%;background-size:25px 25px;}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-input"]:hover{' . $formFieldListOptionButtonOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-input"]:focus{' . $formFieldListOptionButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-input"]:active{' . $formFieldListOptionButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-label"]{' . $formFieldListOptionText . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option-textbox"]{' . $formFieldListOptionTextbox . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option"]{padding:0;}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container [data-form-element-type="radio-list"] [data-form-element-component="radio-list-option"]:not(:last-child){' . $formFieldListOptionContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-single-select-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-input"]{' . $formFieldListOptionButton . 'background-size:16px 16px;}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-input"]:hover{' . $formFieldListOptionButtonOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-input"]:focus{' . $formFieldListOptionButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-input"]:active{' . $formFieldListOptionButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-label"]{' . $formFieldListOptionText . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option-textbox"]{' . $formFieldListOptionTextbox . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option"]{padding:0;}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container [data-form-element-type="checkbox-list"] [data-form-element-component="checkbox-list-option"]:not(:last-child){' . $formFieldListOptionContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-opened-list-multi-select-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="select"]{' . $elementInput . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="select"]:hover{' . $elementInputOver . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="select"]:focus{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="select"]:active{' . $elementInputActive . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="label"]{' . $elementLabel . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container [data-form-element-type="select"] [data-form-element-component="hint"]{' . $formFieldHint . '}';
    echo '.bearcms-tc .bearcms-form-element-field-closed-list-container{' . $formFieldContainer . '}';
    echo '.bearcms-tc .bearcms-form-element-submit-button-container [data-form-element-type="submit-button"] [data-form-element-component="button"]{' . $elementButton . '}';
    echo '.bearcms-tc .bearcms-form-element-submit-button-container [data-form-element-type="submit-button"] [data-form-element-component="button"]:not([disabled]):hover{' . $elementButtonOver . '}';
    echo '.bearcms-tc .bearcms-form-element-submit-button-container [data-form-element-type="submit-button"] [data-form-element-component="button"]:not([disabled]):focus{' . $elementButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-submit-button-container [data-form-element-type="submit-button"] [data-form-element-component="button"]:not([disabled]):active{' . $elementButtonActive . '}';
    echo '.bearcms-tc .bearcms-form-element-submit-button-container [data-form-element-type="submit-button"] [data-form-element-component="button"][disabled]{' . $elementButtonActive . '}';
}

// Temp (remove in the future)
echo '.bearcms-tc .allebg-contact-form-element-email-label{' . $elementLabel . '}';
echo '.bearcms-tc .allebg-contact-form-element-email{' . $elementInput . '}';
echo '.bearcms-tc .allebg-contact-form-element-email:hover{' . $elementInputOver . '}';
echo '.bearcms-tc .allebg-contact-form-element-email:focus{' . $elementInputActive . '}';
echo '.bearcms-tc .allebg-contact-form-element-email:active{' . $elementInputActive . '}';
echo '.bearcms-tc .allebg-contact-form-element-message-label{' . $elementLabel . 'margin-top:calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .allebg-contact-form-element-message{' . $elementInput . $elementTextarea . 'height:calc(var(--bearcms-template-text-font-size) * 12);}';
echo '.bearcms-tc .allebg-contact-form-element-message:hover{' . $elementInputOver . '}';
echo '.bearcms-tc .allebg-contact-form-element-message:focus{' . $elementInputActive . '}';
echo '.bearcms-tc .allebg-contact-form-element-message:active{' . $elementInputActive . '}';
echo '.bearcms-tc .allebg-contact-form-element-send-button{background-color:transparent;margin-top:calc(' . $elementsSpacing . ' / 2);' . $elementButton . '}';
echo '.bearcms-tc .allebg-contact-form-element-send-button:hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .allebg-contact-form-element-send-button:focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .allebg-contact-form-element-send-button:active{' . $elementButtonActive . '}';

// Temp (remove in the future)
echo '.bearcms-tc .allebg-poll-element-answer:not(:last-child){margin-bottom:calc(' . $elementsSpacing . ' / 2)}';
echo '.bearcms-tc .allebg-poll-element-answer-unchecked{' . $elementButton . 'padding:0;width:' . $buttonHeight . ';}';
echo '.bearcms-tc .allebg-poll-element-answer-unchecked:hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .allebg-poll-element-answer-unchecked:focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .allebg-poll-element-answer-unchecked:active{' . $elementButtonActive . '}';
echo '.bearcms-tc .allebg-poll-element-answer-checked{' . $elementButton . 'padding:0;width:' . $buttonHeight . ';background-size:auto ' . $buttonIconSize . ';background-position:center center;background-repeat:no-repeat;}';
echo '.bearcms-tc .allebg-poll-element-answer-checked:hover{' . $elementButtonOver . '}';
echo '.bearcms-tc .allebg-poll-element-answer-checked:focus{' . $elementButtonActive . '}';
echo '.bearcms-tc .allebg-poll-element-answer-checked:active{' . $elementButtonActive . '}';
echo '.bearcms-tc .allebg-poll-element-answer-text{' . $elementText . 'padding:calc(var(--bearcms-template-text-font-size) / 2) 0 var(--bearcms-template-text-font-size) calc(' . $elementsSpacing . ' / 2);}';
echo '.bearcms-tc .allebg-poll-element-answer-count{' . $elementText . 'padding:calc(var(--bearcms-template-text-font-size) / 2) 0 var(--bearcms-template-text-font-size) calc(' . $elementsSpacing . ' / 2);}';

echo '.bearcms-tc .bearcms-share-button-element{font-size:0;}';
echo '.bearcms-tc .bearcms-share-button-element-button{' . $elementButton . 'background-color:transparent;}';
echo '.bearcms-tc .bearcms-share-button-element-button:hover{' . $elementButtonOver . ';}';
echo '.bearcms-tc .bearcms-share-button-element-button:focus{' . $elementButtonActive . ';}';
echo '.bearcms-tc .bearcms-share-button-element-button:active{' . $elementButtonActive . ';}';

echo '</style>';
if ($hasNavigation) {
    echo '<link rel="client-packages-embed" name="responsiveAttributes">';
}
if ($showStoreCartButton) {
    echo '<link rel="client-packages-embed" name="-bearcms-store">';
}
if ($showSearchButton) {
    echo '<link rel="client-packages-embed" name="-bearcms-search">';
}
echo '</head>';

echo '<body><div class="bearcms-template-container">';
echo '<header class="bearcms-template-header">';

if ($hasLanguagesPicker) {
    echo '<div class="bearcms-template-languages">';
    foreach ($languages as $_language) {
        if ($_language === $language) {
            echo '<span>' . strtoupper($_language) . '</span>';
        } else {
            echo '<a href="' . htmlentities($app->urls->get(($languages[0] === $_language ? '/' : '/' . $_language . '/'))) . '">' . strtoupper($_language) . '</a>';
        }
    }
    echo '</div>';
}

if ($hasLogoImage) {
    $imageHTML = '<component src="bearcms-image-element" class="bearcms-template-logo"' . ($isHomePage ? '' : ' onClick="openUrl" url="' . htmlentities($app->urls->get()) . '"') . ' filename="' . htmlentities($logoImageDetails['filename']) . '" fileWidth="' . htmlentities($logoImageDetails['width']) . '" fileHeight="' . htmlentities($logoImageDetails['height']) . '"/>';
    echo '<div class="bearcms-template-logo-container">' . $imageHTML . '</div>';
}
if ($hasLogoText) {
    echo '<div class="bearcms-template-logo-text-container"><' . ($isHomePage ? 'span' : 'a href="' . htmlentities($app->urls->get()) . '"') . ' class="bearcms-template-logo-text' . ($isHomePage ? '' : ' bearcms-template-inner-page-logo-text') . '">' . htmlspecialchars($settings->getTitle((string) $language)) . '</' . ($isHomePage ? 'span' : 'a') . '></div>';
}

if ($hasNavigation) {
    echo '<nav class="bearcms-template-navigation">';
    echo '<div>';
    if ($showStoreCartButton) {
        echo '<div class="bearcms-template-navigation-custom-item bearcms-template-navigation-custom-item-store-cart bearcms-template-navigation-custom-item-store-cart-icon" onclick="bearCMS.store.openCart();" title="' . htmlentities(__('bearcms.themes.themeone.Open store cart')) . '"></div>';
    }
    if ($showSearchButton) {
        echo '<div class="bearcms-template-navigation-custom-item bearcms-template-navigation-custom-item-search bearcms-template-navigation-custom-item-search-icon" onclick="bearCMS.search.open();" title="' . htmlentities(__('bearcms.themes.themeone.Open site search')) . '"></div>';
    }
    echo '<input id="bearcms-template-navigation-menu-button" type="checkbox"/><label for="bearcms-template-navigation-menu-button" class="bearcms-template-navigation-custom-item bearcms-template-navigation-menu-button-icon"></label>';
    echo '<div><component src="bearcms-navigation-element" editable="true" id="main-navigation' . $elementsLanguageSuffix . '" source="allPages" showHomeLink="true" menuType="horizontal-down" class="bearcms-template-navigation-content" selectedPath="' . (string) $app->request->path . '" data-responsive-attributes="vw<600=>menuType=none,vw>=600=>menuType=horizontal-down" /></div>';
    echo '</div>';
    echo '</nav>';
}

echo '</header>';

echo '<section class="bearcms-tc bearcms-template-context bearcms-template-main" style="--bearcms-template-context-accent-text-color:var(--bearcms-template-accent-text-color);--bearcms-template-context-text-color:var(--bearcms-template-text-color);">';
echo '{{body}}';
echo '</section>';

if ($hasFooter) {
    echo '<footer class="bearcms-tc bearcms-template-context bearcms-template-footer" style="--bearcms-template-context-accent-text-color:var(--bearcms-template-footer-text-color);--bearcms-template-context-text-color:var(--bearcms-template-footer-text-color);"><div>';
    echo '<component src="bearcms-elements" editable="true" class="footer-bearcms-elements" id="footer' . $elementsLanguageSuffix . '"/>';
    echo '</div></footer>';
}
echo '</div></body>';
echo '</html>';
