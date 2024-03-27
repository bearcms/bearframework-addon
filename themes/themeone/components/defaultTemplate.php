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

$settings = null;
$getSettings = function () use ($app, &$settings) {
    if ($settings === null) {
        $settings = $app->bearCMS->data->settings->get();
    }
    return $settings;
};
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

$bearCMSAddons = $app->bearCMS->addons;
$hasSearchSupport = $bearCMSAddons->exists('bearcms/search-box-element-addon');
$hasStoreSupport = $bearCMSAddons->exists('bearcms/store-addon');
$hasFormsSupport = $bearCMSAddons->exists('bearcms/forms-addon');
$hasShareButtonSupport = $bearCMSAddons->exists('bearcms/share-button-element-addon');
$hasCodeElementSupport = $bearCMSAddons->exists('bearcms/code-element-addon');
$hasForumsSupport = $bearCMSAddons->exists('bearcms/forums-addon');

$searchButtonVisibility = (string)$customizations->getValue('searchButtonVisibility');
if ($searchButtonVisibility === 'auto') {
    $showSearchButton = $hasSearchSupport && isset($app->searchBoxElement) && $app->searchBoxElement->isEnabled();
} else {
    $showSearchButton = $searchButtonVisibility === '1';
}

$storeCartButtonVisibility = (string)$customizations->getValue('storeCartButtonVisibility');
if ($storeCartButtonVisibility === 'auto') {
    $showStoreCartButton = $hasStoreSupport && isset($app->store) && $app->store->isEnabled();
} else {
    $showStoreCartButton = $storeCartButtonVisibility === '1';
}

$hasLogoImage = isset($logoImage[0]);
if ($hasLogoImage) {
    $logoImageDetails = $customizations->getAssetDetails($logoImage, ['filename', 'width', 'height']);
}

$hasLogoText = $customizations->getValue('logoTextVisibility') === '1';
$hasNavigation = $customizations->getValue('navigationVisibility') === '1';
$hasFooter = $customizations->getValue('footerVisibility') === '1';

$mainElementsVerticalSpacing = '40px';
$borderRadius = '4px';
$elementsSpacing = '20px';
$elementsSpacingHalf = '10px';
$windowPadding = '20px'; // same as $elementsSpacing

$buttonHeight = 'calc(var(--bearcms-template-text-font-size) * 3)';
$buttonPadding = 'var(--bearcms-template-text-font-size)';
$buttonPaddingHalf = 'calc(var(--bearcms-template-text-font-size) * 0.55)';
$buttonIconSize = 'calc(var(--bearcms-template-text-font-size) * 4/3)';

$textStyleCSS = 'font-family:var(--bearcms-template-text-font-family);color:var(--bearcms-template-text-color);font-weight:var(--bearcms-template-text-font-weight);font-style:var(--bearcms-template-text-font-style);font-size:var(--bearcms-template-text-font-size);line-height:var(--bearcms-template-text-line-height);letter-spacing:var(--bearcms-template-text-letter-spacing);';
$textStyleJSON = '"font-family":"var(--bearcms-template-text-font-family)","color":"var(--bearcms-template-text-color)","font-weight":"var(--bearcms-template-text-font-weight)","font-style":"var(--bearcms-template-text-font-style)","font-size":"var(--bearcms-template-text-font-size)","line-height":"var(--bearcms-template-text-line-height)","letter-spacing":"var(--bearcms-template-text-letter-spacing)"';

$elementTextStyleJSON = $textStyleJSON . ',"color":"var(--bearcms-template-context-text-color)"';
$elementAccentTextStyleJSON = '"font-family":"var(--bearcms-template-accent-text-font-family)","font-weight":"var(--bearcms-template-accent-text-font-weight)","font-style":"var(--bearcms-template-accent-text-font-style)","font-size":"var(--bearcms-template-accent-text-font-size)","line-height":"var(--bearcms-template-accent-text-line-height)","letter-spacing":"var(--bearcms-template-accent-text-letter-spacing)","color":"var(--bearcms-template-context-accent-text-color)"';

$elementHeadingLargeJSON = $elementAccentTextStyleJSON . ',"font-size":"calc(var(--bearcms-template-accent-text-font-size) * 2)"';
$elementHeadingMediumJSON = $elementAccentTextStyleJSON . ',"font-size":"calc(var(--bearcms-template-accent-text-font-size) * 1.5)"';
$elementHeadingSmallJSON = $elementAccentTextStyleJSON;
$elementLabelJSON = $elementTextStyleJSON;
$elementInputJSON = '"border-top":"1px solid var(--bearcms-template-context-text-color)","border-right":"1px solid var(--bearcms-template-context-text-color)","border-bottom":"1px solid var(--bearcms-template-context-text-color)","border-left":"1px solid var(--bearcms-template-context-text-color)",' . $elementTextStyleJSON . ',"height":"' . $buttonHeight . '","padding-left":"' . $buttonPadding . '","padding-right":"' . $buttonPadding . '","width":"100%","background-color":"transparent","background-color:hover":"rgba(0,0,0,0.02)","background-color:active":"rgba(0,0,0,0.04)","background-color:focus":"rgba(0,0,0,0.04)","border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"';
$elementTextareaJSON = '"padding-top":"' . $buttonPaddingHalf . '","padding-bottom":"' . $buttonPaddingHalf . '"';
$elementTextJSON = $elementTextStyleJSON;
$elementTextLinkJSON = $elementTextStyleJSON . ',"text-decoration":"underline"';
$elementButtonJSON = $elementTextStyleJSON . ',"background-color":"transparent","background-color:hover":"rgba(0,0,0,0.04)","background-color:focus":"rgba(0,0,0,0.08)","background-color:active":"rgba(0,0,0,0.08)","border":"1px solid var(--bearcms-template-context-text-color)","text-decoration":"none","border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '","padding-left":"' . $buttonPadding . '","padding-right":"' . $buttonPadding . '","line-height":"' . $buttonHeight . '","height":"' . $buttonHeight . '"';
$elementUserImageJSON = '"box-sizing":"border-box","width":"50px","height":"50px","margin-right":"' . $elementsSpacingHalf . '","border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"';
$elementSeparatorJSON = '"background-color":"var(--bearcms-template-context-text-color)","height":"2px","margin-top":"calc(' . $elementsSpacing . ' * 3)","margin-right":"auto","margin-bottom":"calc(' . $elementsSpacing . ' * 3)","margin-left":"auto","opacity":"0.3"';

$options = new \BearCMS\Themes\Theme\Options();
$options->addElements('', '.bearcms-template-context');
$options->addPages();

$optionsValues = [
    'HeadingLargeCSS' => '{' . $elementHeadingLargeJSON . ',"padding-top":"0"}', // Expect to be first. Todo: Use $elementsSpacingHalf when supports :first-child and set first to 0
    'HeadingMediumCSS' => '{' . $elementHeadingMediumJSON . ',"padding-top":"' . $elementsSpacingHalf . '"}', // Set Todo: set first to 0 when supports :first-child
    'HeadingSmallCSS' => '{' . $elementHeadingSmallJSON . ',"padding-top":"' . $elementsSpacingHalf . '"}', // Set Todo: set first to 0 when supports :first-child

    'TextCSS' => '{' . $elementTextStyleJSON . '}',
    'TextLinkCSS' => '{' . $elementTextLinkJSON . '}',

    'HtmlCSS' => '{' . $elementTextJSON . '}',
    'HtmlLinkCSS' => '{' . $elementTextLinkJSON . '}',

    'LinkCSS' => '{' . $elementTextLinkJSON . '}',

    'ImageCSS' => '{"border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"}',

    'ImageGalleryImageCSS' => '{"border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"}',

    'VideoCSS' => '{"border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"}',

    'NavigationItemLinkCSS' => '{' . $elementTextLinkJSON . '}',

    'SeparatorLargeCSS' => '{' . $elementSeparatorJSON . ',"width":"70%"}',
    'SeparatorMediumCSS' => '{' . $elementSeparatorJSON . ',"width":"50%"}',
    'SeparatorSmallCSS' => '{' . $elementSeparatorJSON . ',"width":"30%"}',

    'CommentsCommentCSS' => '{"margin-bottom":"' . $elementsSpacingHalf . '"}',
    'CommentsShowMoreButtonContainerCSS' => '{"padding-bottom":"' . $elementsSpacingHalf . '"}',
    'CommentsShowMoreButtonCSS' => '{' . $elementTextLinkJSON . '}',
    'CommentsAuthorImageCSS' => '{' . $elementUserImageJSON . '}',
    'CommentsAuthorNameCSS' => '{' . $elementTextLinkJSON . '}',
    'CommentsTextCSS' => '{' . $elementTextJSON . '}',
    'CommentsTextLinksCSS' => '{' . $elementTextLinkJSON . '}',
    'CommentsDateCSS' => '{' . $elementTextJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
    'CommentsTextInputCSS' => '{' . $elementInputJSON . ',' . $elementTextareaJSON . ',"height":"calc(var(--bearcms-template-text-font-size) * 8)"}',
    'CommentsSendButtonCSS' => '{"margin-top":"' . $elementsSpacingHalf . '",' . $elementButtonJSON . '}',

    'BlogPostsShowMoreButtonCSS' => '{' . $elementTextLinkJSON . '}',
    'BlogPostsPostTitleCSS' => '{' . $elementHeadingSmallJSON . ',"font-size":"calc(var(--bearcms-template-accent-text-font-size) * 1.2)","color":"var(--bearcms-template-context-text-color)","text-decoration":"underline"}',
    'BlogPostsPostDateContainerCSS' => '{"padding-top":"' . $elementsSpacingHalf . '"}',
    'BlogPostsPostDateCSS' => '{' . $elementTextJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
    'BlogPostsPostContentCSS' => '{"padding-top":"' . $elementsSpacingHalf . '"}',
    'BlogPostsSpacing' => $elementsSpacing,
    'BlogPostsShowMoreButtonContainerCSS' => '{"margin-top":"' . $elementsSpacing . '"}',

    'blogPostPageTitleCSS' => '{' . $elementHeadingLargeJSON . '}',
    'blogPostPageDateContainerCSS' => '{"padding-top":"var(--bearcms-template-text-font-size)"}',
    'blogPostPageDateCSS' => '{' . $elementTextJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
    'blogPostPageContentCSS' => '{"padding-top":"calc(var(--bearcms-template-text-font-size) * 1.6)"}',
    'blogPostPageCommentsTitleContainerCSS' => '{"padding-top":"calc(var(--bearcms-template-text-font-size) * 1.6)"}',
    'blogPostPageCommentsContainerCSS' => '{"padding-top":"calc(var(--bearcms-template-text-font-size) * 1.6)"}',
    'blogPostPageRelatedContainerCSS' => '{"padding-top":"calc(var(--bearcms-template-text-font-size) * 1.6)"}',
];


if ($hasCodeElementSupport) {
    $optionsValues = array_merge($optionsValues, [
        'CodeCSS' => '{' . $elementTextJSON . ',"font-family":"Courier,monospace","border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '","background-color":"#333","padding-left":"var(--bearcms-template-text-font-size)","padding-right":"var(--bearcms-template-text-font-size)","padding-top":"var(--bearcms-template-text-font-size)","padding-bottom":"var(--bearcms-template-text-font-size)","color":"#fff"}',
        'CodeEntityKeywordCSS' => '{"color":"#4dc16c"}',
        'CodeEntityVariableCSS' => '{"color":"#00b5c3"}',
        'CodeEntityValueCSS' => '{"color":"#ff770a"}',
        'CodeEntityCommentCSS' => '{"color":"#929292"}',
    ]);
}

if ($hasForumsSupport) {
    $optionsValues = array_merge($optionsValues, [
        'ForumPostsTitleCSS' => '{' . $elementTextJSON . ',"text-decoration":"underline"}',
        'ForumPostsRepliesCountCSS' => '{' . $elementTextJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
        'ForumPostsShowMoreButtonCSS' => '{' . $elementTextLinkJSON . '}',
        'ForumPostsShowMoreButtonContainerCSS' => '{"margin-top":"' . $elementsSpacingHalf . '"}',
        'ForumPostsNewPostButtonCSS' => '{' . $elementButtonJSON . '}',

        'forumPostPageTitleCSS' => '{' . $elementHeadingLargeJSON . ',"padding-bottom":"var(--bearcms-template-text-font-size)"}',
        'forumPostPageReplyCSS' => '{"margin-bottom":"' . $elementsSpacingHalf . '"}',
        'forumPostPageReplyAuthorImageCSS' => '{' . $elementUserImageJSON . '}',
        'forumPostPageReplyAuthorNameCSS' => '{' . $elementTextLinkJSON . '}',
        'forumPostPageReplyTextCSS' => '{' . $elementTextJSON . '}',
        'forumPostPageReplyTextLinksCSS' => '{' . $elementTextLinkJSON . '}',
        'forumPostPageReplyDateCSS' => '{' . $elementTextJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
        'forumPostPageTextInputCSS' => '{' . $elementInputJSON . ',' . $elementTextareaJSON . ',"height":"calc(var(--bearcms-template-text-font-size) * 14)"}',
        'forumPostPageSendButtonCSS' => '{"margin-top":"' . $elementsSpacingHalf . '",' . $elementButtonJSON . '}',
    ]);
}

if ($hasSearchSupport) {
    $optionsValues = array_merge($optionsValues, [
        'SearchBoxInputCSS' => '{' . $elementInputJSON . '}',
        'SearchBoxButtonCSS' => '{' . $elementButtonJSON . ',"width":"' . $buttonHeight . '","border":"0px","border-left":"1px solid var(--bearcms-template-context-text-color)","border-top-left-radius":"0","border-bottom-left-radius":"0","background-size":"auto ' . $buttonIconSize . ',","background-position":"center center","background-repeat":"no-repeat"}',
    ]);
}

if ($hasStoreSupport) {
    $optionsValues = array_merge($optionsValues, [
        'StoreItemsItemImageCSS' => '{"border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '","width":"200px"}',
        'StoreItemsItemNameCSS' => '{' . $elementHeadingSmallJSON . ',"font-size":"calc(var(--bearcms-template-accent-text-font-size) * 1.2)","color":"var(--bearcms-template-context-text-color)","text-decoration":"underline"}',
        'StoreItemsItemDescriptionCSS' => '{' . $elementTextJSON . '}',
        'StoreItemsItemPriceContainerCSS' => '{"padding-top":"' . $elementsSpacingHalf . '"}',
        'StoreItemsItemPriceCSS' => '{' . $elementTextJSON . '}',
        'StoreItemsItemPriceOriginalCSS' => '{' . $elementTextJSON . ',"text-decoration":"line-through","font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',

        'storeItemPageImageGalleryImageCSS' => '{"border-top-left-radius":"' . $borderRadius . '","border-bottom-left-radius":"' . $borderRadius . '","border-top-right-radius":"' . $borderRadius . '","border-bottom-right-radius":"' . $borderRadius . '"}',
        'storeItemPageNameCSS' => '{' . $elementHeadingLargeJSON . '}',
        'storeItemPageDescriptionCSS' => '{' . $elementTextJSON . '}',
        'storeItemPageOptionContainerCSS' => '{"padding-top":"' . $elementsSpacingHalf . '"}',
        'storeItemPageOptionLabelCSS' => '{' . $elementTextJSON . '}',
        'storeItemPageOptionSelectCSS' => '{' . $elementInputJSON . ',"width":"auto"}',
        'storeItemPagePriceContainerCSS' => '{"padding-top":"' . $elementsSpacingHalf . '"}',
        'storeItemPagePriceCSS' => '{' . $elementTextJSON . '}',
        'storeItemPagePriceOriginalCSS' => '{' . $elementTextJSON . ',"text-decoration":"line-through","font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"}',
        'storeItemPageBuyButtonContainerCSS' => '{"padding-top":"' . $elementsSpacingHalf . '","font-size":"0"}',
        'storeItemPageBuyButtonCSS' => '{' . $elementButtonJSON . '}',
    ]);
}

if ($hasFormsSupport) {
    $formFieldListOptionButtonJSON = $elementInputJSON . ',"width":"calc(var(--bearcms-template-text-font-size) * 2.5)","height":"calc(var(--bearcms-template-text-font-size) * 2.5)","padding-left":"0","padding-right":"0","padding-top":"0","padding-bottom":"0","background-position":"center center","background-repeat":"no-repeat","background-attachment":"scroll","background-size":"cover"';
    $formFieldListOptionTextJSON = $elementTextStyleJSON . ',"padding-left":"var(--bearcms-template-text-font-size)","padding-top":"calc(var(--bearcms-template-text-font-size) * 0.4)"';
    $formFieldListOptionTextboxJSON = $elementInputJSON . ',"height":"calc(var(--bearcms-template-text-font-size) * 2.5)","line-height":"calc(var(--bearcms-template-text-font-size) * 2.5 - 2px)","width":"250px","margin-left":"var(--bearcms-template-text-font-size)","padding-left":"calc(var(--bearcms-template-text-font-size) * 0.8)","padding-right":"calc(var(--bearcms-template-text-font-size) * 0.8)","font-size":"calc(var(--bearcms-template-text-font-size) * 0.9)"';
    $formFieldListOptionContainerJSON = '"value":{"padding-bottom":"5px"},"states":[[":last-child",{"padding-bottom":"0px"}]]';
    $formFieldHintJSON = $elementTextStyleJSON . ',"font-size":"calc(var(--bearcms-template-text-font-size) * 0.8)"';
    $formFieldContainerJSON = '"padding-bottom":"15px"';

    $optionsValues = array_merge($optionsValues, [
        'FormFieldTextCSS' => '{' . $elementInputJSON . '}',
        'FormFieldTextLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldTextHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldTextContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldTextareaCSS' => '{' . $elementInputJSON . ',' . $elementTextareaJSON . ',"height":"calc(var(--bearcms-template-text-font-size) * 8)"}',
        'FormFieldTextareaLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldTextareaHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldTextareaContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldNameCSS' => '{' . $elementInputJSON . '}',
        'FormFieldNameLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldNameHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldNameContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldEmailCSS' => '{' . $elementInputJSON . '}',
        'FormFieldEmailLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldEmailHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldEmailContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldPhoneCSS' => '{' . $elementInputJSON . '}',
        'FormFieldPhoneLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldPhoneHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldPhoneContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldOpenedListSingleSelectLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldOpenedListSingleSelectHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldOpenedListSingleSelectOptionButtonCSS' => '{' . $formFieldListOptionButtonJSON . ',"border-top-left-radius":"50%","border-top-right-radius":"50%","border-bottom-left-radius":"50%","border-bottom-right-radius":"50%","background-size":"25px 25px"}',
        'FormFieldOpenedListSingleSelectOptionTextCSS' => '{' . $formFieldListOptionTextJSON . '}',
        'FormFieldOpenedListSingleSelectOptionTextboxCSS' => '{' . $formFieldListOptionTextboxJSON . '}',
        'FormFieldOpenedListSingleSelectOptionContainerCSS' => '{' . $formFieldListOptionContainerJSON . '}',
        'FormFieldOpenedListSingleSelectContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldOpenedListMultiSelectLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldOpenedListMultiSelectHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldOpenedListMultiSelectOptionButtonCSS' => '{' . $formFieldListOptionButtonJSON . ',"background-size":"16px 16px"}',
        'FormFieldOpenedListMultiSelectOptionTextCSS' => '{' . $formFieldListOptionTextJSON . '}',
        'FormFieldOpenedListMultiSelectOptionTextboxCSS' => '{' . $formFieldListOptionTextboxJSON . '}',
        'FormFieldOpenedListMultiSelectOptionContainerCSS' => '{' . $formFieldListOptionContainerJSON . '}',
        'FormFieldOpenedListMultiSelectContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormFieldClosedListCSS{' . $elementInputJSON . '}',
        'FormFieldClosedListLabelCSS' => '{' . $elementLabelJSON . '}',
        'FormFieldClosedListHintCSS' => '{' . $formFieldHintJSON . '}',
        'FormFieldClosedListContainerCSS' => '{' . $formFieldContainerJSON . '}',
        'FormSubmitButtonCSS' => '{' . $elementButtonJSON . '}',
    ]);
}

if ($hasShareButtonSupport) {
    $optionsValues = array_merge($optionsValues, [
        'ShareButtonContainerCSS' => '{"font-size":"0"}',
        'ShareButtonCSS' => '{' . $elementButtonJSON . ',"background-color":"transparent"}',
    ]);
}

// Temp (remove in the future)
$optionsValues = array_merge($optionsValues, [
    'ContactFormEmailLabelCSS' => '{' . $elementLabelJSON . '}',
    'ContactFormEmailInputCSS' => '{' . $elementInputJSON . '}',
    'ContactFormMessageLabelCSS' => '{' . $elementLabelJSON . ',"margin-top":"' . $elementsSpacingHalf . '"}',
    'ContactFormMessageInputCSS' => '{' . $elementInputJSON . ',' . $elementTextareaJSON . ',"height":"calc(var(--bearcms-template-text-font-size) * 12)"}',
    'ContactFormSendButtonCSS' => '{"background-color":"transparent","margin-top":"' . $elementsSpacingHalf . '",' . $elementButtonJSON . '}',
]);

// Temp (remove in the future)
$optionsValues = array_merge($optionsValues, [
    'PollOptionContainerCSS' => '{"margin-bottom":"' . $elementsSpacingHalf . '"}',
    'PollOptionNotCheckedCSS' => '{' . $elementButtonJSON . ',"padding-left":"0","padding-right":"0","padding-top":"0","padding-bottom":"0","width":"' . $buttonHeight . '"}',
    'PollOptionCheckedCSS' => '{' . $elementButtonJSON . ',"padding-left":"0","padding-right":"0","padding-top":"0","padding-bottom":"0","width":"' . $buttonHeight . '","background-size":"auto ' . $buttonIconSize . '","background-position":"center center","background-repeat":"no-repeat"}',
    'PollOptionTextCSS' => '{' . $elementTextJSON . ',"padding-top":"calc(var(--bearcms-template-text-font-size) / 2)","padding-right":"0","padding-bottom":"var(--bearcms-template-text-font-size)","padding-left":"' . $elementsSpacingHalf . '"}',
    'PollOptionLinkCSS' => '{' . $elementTextJSON . ',"padding-top":"calc(var(--bearcms-template-text-font-size) / 2)","padding-right":"0","padding-bottom":"var(--bearcms-template-text-font-size)","padding-left":"' . $elementsSpacingHalf . '"}',
]);

$options->setValues($optionsValues);

$optionsHTML = $options->getHTML($app->bearCMS->currentUser->exists() ? ['internalIncludeEditorData'] : []);

echo '<html>';
echo '<head>';
echo '<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,minimal-ui">';
if ($optionsHTML !== '') {
    echo str_replace(['<html><head>', '</head></html>'], '', $optionsHTML);
}

echo '<style>';
echo 'html,body{padding:0;margin:0;min-height:100%;}';
echo 'body{background-color:var(--bearcms-template-footer-background-color);}';
echo '*{outline:none;-webkit-tap-highlight-color:rgba(0,0,0,0);}';
echo '.bearcms-template-container{min-height:100vh;display:flex;flex-direction:column;}';
echo '.bearcms-template-header{box-sizing:border-box;width:100%;max-width:calc(' . $contentWidth . ' + var(--bearcms-template-text-font-size) * 2.2);margin:0 auto;padding:' . $windowPadding . ' ' . $windowPadding . ' 0 ' . $windowPadding . ';}'; // 2.2 = twice the nav buttons padding
if ($hasLanguagesPicker) {
    echo '.bearcms-template-languages{position:absolute;top:0;right:' . ($app->currentUser->exists() ? '74px' : '10px') . ';}';
    echo '.bearcms-template-languages *{' . $textStyleCSS . 'display:inline-block;box-sizing:border-box;text-align:center;font-size:calc(var(--bearcms-template-text-font-size) * 0.8);text-decoration:none;line-height:calc(var(--bearcms-template-text-font-size) * 2);padding:0 calc(var(--bearcms-template-text-font-size) * 0.6);min-width:calc(var(--bearcms-template-text-font-size) * 2);height:calc(var(--bearcms-template-text-font-size) * 2);border-bottom-left-radius:' . $borderRadius . ';border-bottom-right-radius:' . $borderRadius . ';}';
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
    echo '.bearcms-template-navigation .bearcms-navigation-element-item a{' . $textStyleCSS . 'padding:0 ' . $buttonPadding . ';line-height:' . $buttonHeight . ';height:' . $buttonHeight . ';min-width:' . $buttonHeight . ';text-decoration:none;display:inline-block;max-width:100%;text-overflow:ellipsis;overflow:hidden;box-sizing:border-box;display:block;}';
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
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children>.bearcms-navigation-element-item a{' . $textStyleCSS . 'padding:0 ' . $buttonPadding . ';line-height:' . $buttonHeight . ';height:' . $buttonHeight . ';color:#fff;}';
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
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:first-child{border-top-left-radius:' . $borderRadius . ' !important;border-top-right-radius:' . $borderRadius . ' !important;border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:last-child{border-bottom-left-radius:' . $borderRadius . ' !important;border-bottom-right-radius:' . $borderRadius . ' !important;border-top-left-radius:0 !important;border-top-right-radius:0 !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item:first-child:last-child{border-bottom-left-radius:' . $borderRadius . ' !important;border-bottom-right-radius:' . $borderRadius . ' !important;border-top-left-radius:' . $borderRadius . ' !important;border-top-right-radius:' . $borderRadius . ' !important;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item a{display:block !important;color:#fff !important;text-align:left;}';
    echo '.bearcms-template-navigation .bearcms-navigation-element-item-children{display:none !important;}';
    echo '#bearcms-template-navigation-menu-button+label{display:inline-block;background-size:auto calc(var(--bearcms-template-text-font-size) + 9px);background-position:center center;background-repeat:no-repeat;border-top-left-radius:' . $borderRadius . ';border-bottom-left-radius:' . $borderRadius . ';}';
    echo '#bearcms-template-navigation-menu-button+label+div{display:none;}';
    echo '#bearcms-template-navigation-menu-button:checked+label+div{display:block;width:100%;box-sizing:border-box;padding-top:10px;}';
    echo '}';
}

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
    $imageHTML = '<component src="bearcms-image-element" class="bearcms-template-logo"' . ($isHomePage ? '' : ' onClick="openUrl" url="' . htmlentities($app->urls->get()) . '"') . ' filename="' . htmlentities((string)$logoImageDetails['filename']) . '" fileWidth="' . htmlentities((string)$logoImageDetails['width']) . '" fileHeight="' . htmlentities((string)$logoImageDetails['height']) . '"/>';
    echo '<div class="bearcms-template-logo-container">' . $imageHTML . '</div>';
}
if ($hasLogoText) {
    echo '<div class="bearcms-template-logo-text-container"><' . ($isHomePage ? 'span' : 'a href="' . htmlentities($app->urls->get()) . '"') . ' class="bearcms-template-logo-text' . ($isHomePage ? '' : ' bearcms-template-inner-page-logo-text') . '">' . htmlspecialchars($getSettings()->getTitle((string)$language)) . '</' . ($isHomePage ? 'span' : 'a') . '></div>';
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

echo '<section class="bearcms-template-context bearcms-template-main" style="--bearcms-template-context-accent-text-color:var(--bearcms-template-accent-text-color);--bearcms-template-context-text-color:var(--bearcms-template-text-color);">';
echo '{{body}}';
echo '</section>';

if ($hasFooter) {
    echo '<footer class="bearcms-template-context bearcms-template-footer" style="--bearcms-template-context-accent-text-color:var(--bearcms-template-footer-text-color);--bearcms-template-context-text-color:var(--bearcms-template-footer-text-color);"><div>';
    echo '<component src="bearcms-elements" editable="true" class="footer-bearcms-elements" id="footer' . $elementsLanguageSuffix . '"/>';
    echo '</div></footer>';
}
echo '</div></body>';
echo '</html>';
