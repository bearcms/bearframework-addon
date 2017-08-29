<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearCMS\Internal\CurrentTheme;

class Themes
{

    public function add(string $id, callable $initializeCallback, callable $applyCallback, array $options = [])
    {
        \BearCMS\Internal\Themes::add($id, $initializeCallback, $applyCallback, $options);

        // Initialize current theme
        $currentThemeID = CurrentTheme::getID();
        if ($id === $currentThemeID) {
            if (is_callable($initializeCallback)) {
                call_user_func($initializeCallback, CurrentTheme::getOptions());
            }
        }

        return $this;
    }

}
