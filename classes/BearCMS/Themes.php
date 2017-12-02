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

    public function add(string $id, array $options = [])
    {
        \BearCMS\Internal\Themes::add($id, $options);

        // Initialize current theme
        if (isset($options['initialize'])) {
            $currentThemeID = CurrentTheme::getID();
            if ($id === $currentThemeID) {
                if (is_callable($options['initialize'])) {
                    call_user_func($options['initialize'], CurrentTheme::getOptions());
                }
            }
        }

        return $this;
    }

    public function makeOptionsDefinition()
    {
        return new \BearCMS\Themes\OptionsDefinition();
    }

}
