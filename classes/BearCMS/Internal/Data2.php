<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

/**
 * Contains reference to the different data types
 * 
 * @property \BearCMS\Internal\Data2\BlogCategories $blogCategories Information about the blog categories
 * @property \BearCMS\Internal\Data2\Comments $comments Information about the comments
 * @property \BearCMS\Internal\Data2\CommentsThreads $commentsThreads Information about the comments threads
 * @property \BearCMS\Internal\Data2\Themes $themes Information about the site themes
 * @property \BearCMS\Internal\Data2\UsersInvitations $usersInvitations Information about the invited CMS users (administrators)
 * @internal
 * @codeCoverageIgnore
 */
class Data2
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
        //        $this->defineProperty('addons', [
        //            'init' => function() {
        //                return new \BearCMS\Internal\Data2\Addons();
        //            },
        //            'readonly' => true
        //        ]);
        $this->defineProperty('blogCategories', [
            'init' => function () {
                return new \BearCMS\Internal\Data2\BlogCategories();
            },
            'readonly' => true
        ]);
        $this->defineProperty('comments', [
            'init' => function () {
                return new \BearCMS\Internal\Data2\Comments();
            },
            'readonly' => true
        ]);
        $this->defineProperty('commentsThreads', [
            'init' => function () {
                return new \BearCMS\Internal\Data2\CommentsThreads();
            },
            'readonly' => true
        ]);
        //        $this->defineProperty('forumCategories', [
        //            'init' => function() {
        //                return new \BearCMS\Internal\Data2\ForumCategories();
        //            },
        //            'readonly' => true
        //        ]);
        //        $this->defineProperty('forumPosts', [
        //            'init' => function() {
        //                return new \BearCMS\Internal\Data2\ForumPosts();
        //            },
        //            'readonly' => true
        //        ]);
        //        $this->defineProperty('forumPostsReplies', [
        //            'init' => function() {
        //                return new \BearCMS\Internal\Data2\ForumPostsReplies();
        //            },
        //            'readonly' => true
        //        ]);
        $this->defineProperty('themes', [
            'init' => function () {
                return new \BearCMS\Internal\Data2\Themes();
            },
            'readonly' => true
        ]);
        $this->defineProperty('usersInvitations', [
            'init' => function () {
                return new \BearCMS\Internal\Data2\UsersInvitations();
            },
            'readonly' => true
        ]);
    }

    /**
     * Converts data:, addon:id: filenames to real filenames
     * 
     * @param string $filename
     * @return ?string The real filename or null if not found
     */
    public function getRealFilename(string $filename): ?string
    {
        $app = App::get();
        if (substr($filename, 0, 10) === 'appdata://') {
            return $filename;
        } elseif (substr($filename, 0, 5) === 'data:') {
            return 'appdata://' . substr($filename, 5);
        } elseif (substr($filename, 0, 6) === 'addon:') {
            $temp = explode(':', $filename, 3);
            if (sizeof($temp) === 3) {
                $addon = \BearFramework\Addons::get($temp[1]);
                if ($addon !== null) {
                    return $addon->dir . '/' . $temp[2];
                }
            }
        }
        return null;
    }

    public function fixFilename(string $filename): ?string
    {
        $newFilename = $this->getRealFilename($filename);
        if ($newFilename !== null) {
            return $newFilename;
        }
        return $filename;
    }
}
