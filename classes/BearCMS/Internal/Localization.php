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
 * @internal
 * @codeCoverageIgnore
 */
class Localization
{

    /**
     * 
     * @var string|null;
     */
    static private $previousLocale = null;

    /**
     * 
     * @return void
     */
    static function setAdminLocale()
    {
        $app = App::get();
        $previousLocale = $app->localization->getLocale();
        if ($previousLocale !== Config::$language) {
            $app->localization->setLocale(Config::$language);
            self::$previousLocale = $previousLocale;
        }
    }

    /**
     * 
     * @return void
     */
    static function restoreLocale()
    {
        if (self::$previousLocale !== null) {
            $app = App::get();
            $app->localization->setLocale(self::$previousLocale);
            self::$previousLocale = null;
        }
    }
}
