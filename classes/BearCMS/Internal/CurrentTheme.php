<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearCMS\Internal;
use BearFramework\App;

/**
 * @internal
 * @codeCoverageIgnore
 */
class CurrentTheme
{

    /**
     * Local cache
     * 
     * @var array 
     */
    private static $cache = [];

    /**
     * Returns the id of the current active theme or theme in preview
     * 
     * @return string The id of the current active theme or theme in preview
     */
    static public function getID(): string
    {
        if (!isset(self::$cache['id'])) {
            $app = App::get();
            if ($app->currentUser->exists()) {
                $cookies = Internal\Cookies::getList(Internal\Cookies::TYPE_SERVER);
            } else {
                $cookies = [];
            }
            self::$cache['id'] = isset($cookies['tmpr']) ? $cookies['tmpr'] : Internal\Themes::getActiveThemeID();
        }
        return self::$cache['id'];
    }
}
