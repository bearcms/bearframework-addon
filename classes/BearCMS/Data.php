<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;

/**
 * Contains reference to the different data types
 * 
 * @property \BearCMS\Data\BlogCategories $blogCategories Information about the blog categories
 * @property \BearCMS\Data\BlogPosts $blogPosts Information about the blog posts
 * @property \BearCMS\Data\Comments $comments Information about the comments
 * @property \BearCMS\Data\CommentsThreads $commentsThreads Information about the comments threads
 * @property \BearCMS\Data\ForumCategories $forumCategories Information about the forum categories
 * @property \BearCMS\Data\ForumPosts $forumPosts Information about the forum posts
 * @property \BearCMS\Data\ForumPostsReplies $forumPostsReplies Information about the forum replies
 * @property \BearCMS\Data\Pages $pages Information about the site pages
 * @property \BearCMS\Data\Settings $settings Information about the site settings
 * @property \BearCMS\Data\Themes $themes Information about the site themes
 * @property \BearCMS\Data\Users $users Information about the CMS users (administrators)
 * @property \BearCMS\Data\UsersInvitations $usersInvitations Information about the invited CMS users (administrators)
 */
class Data
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
//        $this->defineProperty('addons', [
//            'init' => function() {
//                return new \BearCMS\Data\Addons();
//            },
//            'readonly' => true
//        ]);
        $this->defineProperty('blogCategories', [
            'init' => function() {
                return new \BearCMS\Data\BlogCategories();
            },
            'readonly' => true
        ]);
        $this->defineProperty('blogPosts', [
            'init' => function() {
                return new \BearCMS\Data\BlogPosts();
            },
            'readonly' => true
        ]);
        $this->defineProperty('comments', [
            'init' => function() {
                return new \BearCMS\Data\Comments();
            },
            'readonly' => true
        ]);
        $this->defineProperty('commentsThreads', [
            'init' => function() {
                return new \BearCMS\Data\CommentsThreads();
            },
            'readonly' => true
        ]);
        $this->defineProperty('forumCategories', [
            'init' => function() {
                return new \BearCMS\Data\ForumCategories();
            },
            'readonly' => true
        ]);
        $this->defineProperty('forumPosts', [
            'init' => function() {
                return new \BearCMS\Data\ForumPosts();
            },
            'readonly' => true
        ]);
        $this->defineProperty('forumPostsReplies', [
            'init' => function() {
                return new \BearCMS\Data\ForumPostsReplies();
            },
            'readonly' => true
        ]);
        $this->defineProperty('pages', [
            'init' => function() {
                return new \BearCMS\Data\Pages();
            },
            'readonly' => true
        ]);
        $this->defineProperty('settings', [
            'init' => function() {
                return new \BearCMS\Data\Settings();
            },
            'readonly' => true
        ]);
        $this->defineProperty('themes', [
            'init' => function() {
                return new \BearCMS\Data\Themes();
            },
            'readonly' => true
        ]);
        $this->defineProperty('users', [
            'init' => function() {
                return new \BearCMS\Data\Users();
            },
            'readonly' => true
        ]);
        $this->defineProperty('usersInvitations', [
            'init' => function() {
                return new \BearCMS\Data\UsersInvitations();
            },
            'readonly' => true
        ]);
    }

    /**
     * Converts data:, app:, addon:id: filenames to real filenames
     * 
     * @param string $filename
     * @return ?string The real filename or null if not found
     */
    public function getRealFilename(string $filename): ?string
    {
        $app = App::get();
        if (substr($filename, 0, 5) === 'data:') {
            return $app->data->getFilename(substr($filename, 5));
        } elseif (substr($filename, 0, 4) === 'app:') {
            return $app->config->appDir . DIRECTORY_SEPARATOR . substr($filename, 4);
        } elseif (substr($filename, 0, 6) === 'addon:') {
            $temp = explode(':', $filename, 3);
            if (sizeof($temp) === 3) {
                $addon = \BearFramework\Addons::get($temp[1]);
                if ($addon !== null) {
                    return $addon->dir . DIRECTORY_SEPARATOR . $temp[2];
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return int
     */
    public function getUploadsSize()
    {
        return \BearCMS\Internal\Data\UploadsSize::getSize();
    }

}
