<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;
use BearCMS\Internal;
use BearCMS\Internal\Config;

/**
 * 
 */
class Themes
{

    /**
     * Announces a new theme.
     * 
     * @param string $id The theme ID.
     * @param callable $callback A function to define theme parameters.
     * @return self Returns a reference to itself.
     */
    public function announce(string $id, callable $callback): self
    {
        Internal\Themes::$announcements[$id] = $callback;

        if (Config::$initialized) { // Initialize to add asset dirs
            $currentThemeID = Internal\CurrentTheme::getID();
            if ($currentThemeID === $id) {
                Internal\Themes::initialize($currentThemeID);
            }
        }

        return $this;
    }

    /**
     * 
     */
    public function addDefault()
    {
        $app = App::get();
        $context = $app->contexts->get(__FILE__);
        require_once $context->dir . '/themes/themeone/index.php';
    }

    /**
     * 
     * @return \BearCMS\Themes\Options\Schema
     */
    public function makeOptionsSchema(): \BearCMS\Themes\Options\Schema
    {
        return new \BearCMS\Themes\Options\Schema();
    }

}
