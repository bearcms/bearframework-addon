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
        ->add('BearCMS\Themes', 'classes/BearCMS/Themes.php')
        ->add('BearCMS\Themes\Options', 'classes/BearCMS/Themes/Options.php')
        ->add('BearCMS\Themes\OptionsSchema', 'classes/BearCMS/Themes/OptionsSchema.php')
        ->add('BearCMS\Themes\OptionsGroupSchema', 'classes/BearCMS/Themes/OptionsGroupSchema.php')
        ->add('BearCMS\Themes\Theme', 'classes/BearCMS/Themes/Theme.php')
        ->add('BearCMS\Internal\DataList', 'classes/BearCMS/Internal/DataList.php')
        ->add('BearCMS\Internal\DataObject', 'classes/BearCMS/Internal/DataObject.php')
        ->add('BearCMS\Internal2', 'classes/BearCMS/Internal2.php')
        ->add('BearCMS\Internal\Data2', 'classes/BearCMS/Internal/Data2.php')
        ->add('BearCMS\Internal\Data2\Addon', 'classes/BearCMS/Internal/Data2/Addon.php')
        ->add('BearCMS\Internal\Data2\Addons', 'classes/BearCMS/Internal/Data2/Addons.php')
        ->add('BearCMS\Internal\Data2\BlogCategories', 'classes/BearCMS/Internal/Data2/BlogCategories.php')
        ->add('BearCMS\Internal\Data2\BlogCategory', 'classes/BearCMS/Internal/Data2/BlogCategory.php')
        ->add('BearCMS\Internal\Data2\BlogPost', 'classes/BearCMS/Internal/Data2/BlogPost.php')
        ->add('BearCMS\Internal\Data2\BlogPosts', 'classes/BearCMS/Internal/Data2/BlogPosts.php')
        ->add('BearCMS\Internal\Data2\Comment', 'classes/BearCMS/Internal/Data2/Comment.php')
        ->add('BearCMS\Internal\Data2\Comments', 'classes/BearCMS/Internal/Data2/Comments.php')
        ->add('BearCMS\Internal\Data2\CommentsThread', 'classes/BearCMS/Internal/Data2/CommentsThread.php')
        ->add('BearCMS\Internal\Data2\CommentsThreads', 'classes/BearCMS/Internal/Data2/CommentsThreads.php')
        ->add('BearCMS\Internal\Data2\ForumCategories', 'classes/BearCMS/Internal/Data2/ForumCategories.php')
        ->add('BearCMS\Internal\Data2\ForumCategory', 'classes/BearCMS/Internal/Data2/ForumCategory.php')
        ->add('BearCMS\Internal\Data2\ForumPost', 'classes/BearCMS/Internal/Data2/ForumPost.php')
        ->add('BearCMS\Internal\Data2\ForumPosts', 'classes/BearCMS/Internal/Data2/ForumPosts.php')
        ->add('BearCMS\Internal\Data2\ForumPostReply', 'classes/BearCMS/Internal/Data2/ForumPostReply.php')
        ->add('BearCMS\Internal\Data2\ForumPostsReplies', 'classes/BearCMS/Internal/Data2/ForumPostsReplies.php')
        ->add('BearCMS\Internal\Data2\Page', 'classes/BearCMS/Internal/Data2/Page.php')
        ->add('BearCMS\Internal\Data2\Pages', 'classes/BearCMS/Internal/Data2/Pages.php')
        ->add('BearCMS\Internal\Data2\Settings', 'classes/BearCMS/Internal/Data2/Settings.php')
        ->add('BearCMS\Internal\Data2\Themes', 'classes/BearCMS/Internal/Data2/Themes.php')
        ->add('BearCMS\Internal\Data2\User', 'classes/BearCMS/Internal/Data2/User.php')
        ->add('BearCMS\Internal\Data2\UserInvitation', 'classes/BearCMS/Internal/Data2/UserInvitation.php')
        ->add('BearCMS\Internal\Data2\Users', 'classes/BearCMS/Internal/Data2/Users.php')
        ->add('BearCMS\Internal\Data2\UsersInvitations', 'classes/BearCMS/Internal/Data2/UsersInvitations.php')
        ->add('BearCMS\Internal\Data', 'classes/BearCMS/Internal/Data.php')
        ->add('BearCMS\Internal\Data\Addons', 'classes/BearCMS/Internal/Data/Addons.php')
        ->add('BearCMS\Internal\Data\BlogPosts', 'classes/BearCMS/Internal/Data/BlogPosts.php')
        ->add('BearCMS\Internal\Data\Comments', 'classes/BearCMS/Internal/Data/Comments.php')
        ->add('BearCMS\Internal\Data\Files', 'classes/BearCMS/Internal/Data/Files.php')
        ->add('BearCMS\Internal\Data\ForumPosts', 'classes/BearCMS/Internal/Data/ForumPosts.php')
        ->add('BearCMS\Internal\Data\ForumPostsReplies', 'classes/BearCMS/Internal/Data/ForumPostsReplies.php')
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
        ->add('BearCMS\Internal\UserProvider', 'classes/BearCMS/Internal/UserProvider.php');

$app->addons
        ->add('ivopetkov/users-bearframework-addon', [
            'useDataCache' => Config::$useDataCache
        ]);

$context->assets
        ->addDir('assets')
        ->addDir('components/bearcmsBlogPostsElement/assets')
        ->addDir('components/bearcmsCommentsElement/assets')
        ->addDir('components/bearcmsContactFormElement/assets')
        ->addDir('components/bearcmsForumPostsElement/assets');

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

$app->hooks
        ->add('dataItemChanged', function($key) use ($app) { // has theme change
            if (strpos($key, '.temp/bearcms/userthemeoptions/') === 0 || strpos($key, 'bearcms/themes/theme/') === 0) {
                $currentThemeID = Internal\CurrentTheme::getID();
                if ($app->bearCMS->currentUser->exists()) {
                    $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID, $app->bearCMS->currentUser->getID());
                    if ($cacheItemKey !== null) {
                        $app->cache->delete($cacheItemKey);
                    }
                }
                $cacheItemKey = Internal\Themes::getCacheItemKey($currentThemeID);
                if ($cacheItemKey !== null) {
                    $app->cache->delete($cacheItemKey);
                }
            }
        });

$app->hooks
        ->add('responseCreated', function() use ($app) {
            if (Internal\Data::$hasContentChange) {
                $app->hooks->execute('bearCMSContentChanged');
            }
        });
