<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;
use BearCMS\Internal\Cookies;
use BearCMS\Internal\Themes as InternalThemes;

/**
 * Information about the current theme
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
            $cookies = Cookies::getList(Cookies::TYPE_SERVER);
            self::$cache['id'] = isset($cookies['tmpr']) ? $cookies['tmpr'] : \BearCMS\Internal\Themes::getActiveThemeID();
        }
        return self::$cache['id'];
    }

}
