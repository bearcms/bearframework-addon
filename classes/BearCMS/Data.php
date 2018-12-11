<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * 
 * @property-read \BearCMS\Data\BlogPosts $blogPosts
 * @property-read \BearCMS\Data\Pages $pages
 * @property-read \BearCMS\Data\Settings $settings
 * @property-read \BearCMS\Data\Users $users
 */
class Data
{

    use \IvoPetkov\DataObjectTrait;

    function __construct()
    {
        $this
                ->defineProperty('blogPosts', [
                    'init' => function() {
                        return new \BearCMS\Data\BlogPosts();
                    },
                    'readonly' => true
                ])
                ->defineProperty('pages', [
                    'init' => function() {
                        return new \BearCMS\Data\Pages();
                    },
                    'readonly' => true
                ])
                ->defineProperty('settings', [
                    'init' => function() {
                        return new \BearCMS\Data\Settings();
                    },
                    'readonly' => true
                ])
                ->defineProperty('users', [
                    'init' => function() {
                        return new \BearCMS\Data\Users();
                    },
                    'readonly' => true
                ])
        ;
    }

}
