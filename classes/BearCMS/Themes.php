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

    /**
     * 
     * @param string $id
     * @param array|callable $options
     */
    public function add(string $id, $options = [])
    {
        $currentThemeID = CurrentTheme::getID();
        $initialize = $id === $currentThemeID;
        if ($initialize && is_callable($options)) {
            $options = call_user_func($options);
        }

        \BearCMS\Internal\Themes::add($id, $options);

        // Initialize current theme
        if ($initialize && isset($options['initialize']) && is_callable($options['initialize'])) {
            call_user_func($options['initialize'], CurrentTheme::getOptions());
        }

        return $this;
    }

    public function makeOptionsDefinition()
    {
        return new \BearCMS\Themes\OptionsDefinition();
    }

}
