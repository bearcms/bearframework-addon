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
}
