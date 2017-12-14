<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;
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
        $app = App::get();
        $currentThemeID = CurrentTheme::getID();
        $initialize = $id === $currentThemeID;
        if ($initialize && is_callable($options)) {
            $options = call_user_func($options);
        }
        if ($app->hooks->exists('bearCMSThemeAdd')) {
            if (is_callable($options)) {
                $options = call_user_func($options);
            }
            $app->hooks->execute('bearCMSThemeAdd', $id, $options);
        }

        \BearCMS\Internal\Themes::add($id, $options);

        // Initialize current theme
        if ($initialize && isset($options['initialize']) && is_callable($options['initialize'])) {
            call_user_func($options['initialize'], CurrentTheme::getOptions());
        }

        if ($app->hooks->exists('bearCMSThemeAdded')) {
            $app->hooks->execute('bearCMSThemeAdded', $id, $options);
        }

        return $this;
    }

    public function addDefault()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        require $context->dir . '/themes/universal/index.php';
    }

    public function defineElementOption($definition)
    {
        \BearCMS\Internal\Themes::defineElementOption($definition);
        return $this;
    }

    public function makeOptionsDefinition()
    {
        return new \BearCMS\Themes\OptionsDefinition();
    }

}
