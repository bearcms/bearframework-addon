<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('BearCMS', 'classes/BearCMS.php')
        ->add('BearCMS\Addons', 'classes/BearCMS/Addons.php')
        ->add('BearCMS\Addons\Addon', 'classes/BearCMS/Addons/Addon.php')
        ->add('BearCMS\CurrentUser', 'classes/BearCMS/CurrentUser.php')
        ->add('BearCMS\Data', 'classes/BearCMS/Data.php')
        ->add('BearCMS\Data\BlogPosts\BlogPost', 'classes/BearCMS/Data/BlogPosts/BlogPost.php')
        ->add('BearCMS\Data\BlogPosts', 'classes/BearCMS/Data/BlogPosts.php')
        ->add('BearCMS\Data\Pages\Page', 'classes/BearCMS/Data/Pages/Page.php')
        ->add('BearCMS\Data\Pages', 'classes/BearCMS/Data/Pages.php')
        ->add('BearCMS\Data\Settings', 'classes/BearCMS/Data/Settings.php')
        ->add('BearCMS\Data\Settings\Settings', 'classes/BearCMS/Data/Settings/Settings.php')
        ->add('BearCMS\Data\Users\User', 'classes/BearCMS/Data/Users/User.php')
        ->add('BearCMS\Data\Users', 'classes/BearCMS/Data/Users.php')
        ->add('BearCMS\Themes', 'classes/BearCMS/Themes.php')
        ->add('BearCMS\Themes\Options', 'classes/BearCMS/Themes/Options.php')
        ->add('BearCMS\Themes\Options\GroupSchema', 'classes/BearCMS/Themes/Options/GroupSchema.php')
        ->add('BearCMS\Themes\Options\OptionSchema', 'classes/BearCMS/Themes/Options/OptionSchema.php')
        ->add('BearCMS\Themes\Options\Schema', 'classes/BearCMS/Themes/Options/Schema.php')
        ->add('BearCMS\Themes\Theme', 'classes/BearCMS/Themes/Theme.php')
        ->add('BearCMS\Internal2', 'classes/BearCMS/Internal2.php')
        ->add('BearCMS\Internal\Data2', 'classes/BearCMS/Internal/Data2.php')
        ->add('BearCMS\Internal\Data2\Addon', 'classes/BearCMS/Internal/Data2/Addon.php')
        ->add('BearCMS\Internal\Data2\Addons', 'classes/BearCMS/Internal/Data2/Addons.php')
        ->add('BearCMS\Internal\Data2\BlogCategories', 'classes/BearCMS/Internal/Data2/BlogCategories.php')
        ->add('BearCMS\Internal\Data2\BlogCategory', 'classes/BearCMS/Internal/Data2/BlogCategory.php')
        ->add('BearCMS\Internal\Data2\Comment', 'classes/BearCMS/Internal/Data2/Comment.php')
        ->add('BearCMS\Internal\Data2\Comments', 'classes/BearCMS/Internal/Data2/Comments.php')
        ->add('BearCMS\Internal\Data2\CommentsThread', 'classes/BearCMS/Internal/Data2/CommentsThread.php')
        ->add('BearCMS\Internal\Data2\CommentsThreads', 'classes/BearCMS/Internal/Data2/CommentsThreads.php')
//        ->add('BearCMS\Internal\Data2\ForumCategories', 'classes/BearCMS/Internal/Data2/ForumCategories.php')
//        ->add('BearCMS\Internal\Data2\ForumCategory', 'classes/BearCMS/Internal/Data2/ForumCategory.php')
//        ->add('BearCMS\Internal\Data2\ForumPost', 'classes/BearCMS/Internal/Data2/ForumPost.php')
//        ->add('BearCMS\Internal\Data2\ForumPosts', 'classes/BearCMS/Internal/Data2/ForumPosts.php')
//        ->add('BearCMS\Internal\Data2\ForumPostReply', 'classes/BearCMS/Internal/Data2/ForumPostReply.php')
//        ->add('BearCMS\Internal\Data2\ForumPostsReplies', 'classes/BearCMS/Internal/Data2/ForumPostsReplies.php')
        ->add('BearCMS\Internal\Data2\Settings', 'classes/BearCMS/Internal/Data2/Settings.php')
        ->add('BearCMS\Internal\Data2\Themes', 'classes/BearCMS/Internal/Data2/Themes.php')
        ->add('BearCMS\Internal\Data2\UserInvitation', 'classes/BearCMS/Internal/Data2/UserInvitation.php')
        ->add('BearCMS\Internal\Data2\UsersInvitations', 'classes/BearCMS/Internal/Data2/UsersInvitations.php')
        ->add('BearCMS\Internal\Data', 'classes/BearCMS/Internal/Data.php')
        ->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php')
        ->add('BearCMS\Internal\Data\BlogPosts', 'classes/BearCMS/Internal/Data/BlogPosts.php')
        ->add('BearCMS\Internal\Data\Comments', 'classes/BearCMS/Internal/Data/Comments.php')
        ->add('BearCMS\Internal\Data\Files', 'classes/BearCMS/Internal/Data/Files.php')
//        ->add('BearCMS\Internal\Data\ForumPosts', 'classes/BearCMS/Internal/Data/ForumPosts.php')
//        ->add('BearCMS\Internal\Data\ForumPostsReplies', 'classes/BearCMS/Internal/Data/ForumPostsReplies.php')
        ->add('BearCMS\Internal\Data\Pages', 'classes/BearCMS/Internal/Data/Pages.php')
        ->add('BearCMS\Internal\Data\UploadsSize', 'classes/BearCMS/Internal/Data/UploadsSize.php')
        ->add('BearCMS\Internal\DataSchema', 'classes/BearCMS/Internal/DataSchema.php')
        ->add('BearCMS\Internal\Controller', 'classes/BearCMS/Internal/Controller.php')
        ->add('BearCMS\Internal\Cookies', 'classes/BearCMS/Internal/Cookies.php')
        ->add('BearCMS\Internal\CurrentTheme', 'classes/BearCMS/Internal/CurrentTheme.php')
        ->add('BearCMS\Internal\ElementsHelper', 'classes/BearCMS/Internal/ElementsHelper.php')
        ->add('BearCMS\Internal\ElementsTypes', 'classes/BearCMS/Internal/ElementsTypes.php')
        ->add('BearCMS\Internal\Config', 'classes/BearCMS/Internal/Config.php')
        ->add('BearCMS\Internal\PublicProfile', 'classes/BearCMS/Internal/PublicProfile.php')
        ->add('BearCMS\Internal\Server', 'classes/BearCMS/Internal/Server.php')
        ->add('BearCMS\Internal\ServerCommands', 'classes/BearCMS/Internal/ServerCommands.php')
        ->add('BearCMS\Internal\TempClientData', 'classes/BearCMS/Internal/TempClientData.php')
        ->add('BearCMS\Internal\Themes', 'classes/BearCMS/Internal/Themes.php')
        ->add('BearCMS\Internal\ThemesOptionsGroupSchemaTrait', 'classes/BearCMS/Internal/ThemesOptionsGroupSchemaTrait.php')
        ->add('BearCMS\Internal\UserProvider', 'classes/BearCMS/Internal/UserProvider.php');

$app->addons
        ->add('ivopetkov/users-bearframework-addon', [
            'useDataCache' => Config::$useDataCache
        ]);

$context->assets
        ->addDir('assets')
        ->addDir('components/bearcmsBlogPostsElement/assets')
        ->addDir('components/bearcmsCommentsElement/assets');

$app->localization
        ->addDictionary('en', function() use ($context) {
            return include $context->dir . '/locales/en.php';
        })
        ->addDictionary('bg', function() use ($context) {
            return include $context->dir . '/locales/bg.php';
        })
        ->addDictionary('ru', function() use ($context) {
            return include $context->dir . '/locales/ru.php';
        });

$app->shortcuts
        ->add('bearCMS', function() {
            return new BearCMS();
        });

BearCMS\Internal2::initialize();

if ($app->request->method === 'GET') {
    if (strlen($app->config->assetsPathPrefix) > 0 && strpos($app->request->path, $app->config->assetsPathPrefix) === 0) {
        // skip
    } else {
        $cacheBundlePath = $app->request->path->get();
        Internal\Data::loadCacheBundle($cacheBundlePath);
        $app->hooks->add('responseSent', function() use ($cacheBundlePath) {
            Internal\Data::saveCacheBundle($cacheBundlePath);
        });
    }
}

$app->hooks
        ->add('dataItemChanged', function($key) use (&$app) {
            $prefixes = [
                'bearcms/pages/page/',
                'bearcms/blog/post/'
            ];
            foreach ($prefixes as $prefix) {
                if (strpos($key, $prefix) === 0) {
                    $dataBundleID = 'bearcmsdataprefix-' . $prefix;
                    if ($app->data->exists($key)) {
                        $app->dataBundle->addItem($dataBundleID, $key);
                    } else {
                        $app->dataBundle->removeItem($dataBundleID, $key);
                    }
                    break;
                }
            }
        });

//$app->hooks
//        ->add('dataItemChanged', function($key) use ($app) { // has theme change
//            if (strpos($key, '.temp/bearcms/userthemeoptions/') === 0 || strpos($key, 'bearcms/themes/theme/') === 0) {
//                $currentThemeID = Internal\CurrentTheme::getID();
//                if ($app->bearCMS->currentUser->exists()) {
//                    $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID, $app->bearCMS->currentUser->getID());
//                    if ($cacheItemKey !== null) {
//                        $app->cache->delete($cacheItemKey);
//                    }
//                }
//                $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID);
//                if ($cacheItemKey !== null) {
//                    $app->cache->delete($cacheItemKey);
//                }
//            }
//        });

$app->hooks
        ->add('responseCreated', function() use ($app) {
            if (Internal\Data::$hasContentChange) {
                $app->hooks->execute('bearCMSContentChanged');
            }
        });
