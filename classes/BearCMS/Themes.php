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

    /**
     * 
     * @return array
     */
    public function getIDs(): array
    {
        return array_keys(\BearCMS\Internal\Themes::$list);
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getManifest(string $id): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $result = \BearCMS\Internal\Themes::getManifest($id);
        $styles = \BearCMS\Internal\Themes::getStyles($id);
        $result['styles'] = [];
        foreach ($styles as $style) {
            $result['styles'][] = [
                'id' => $style['id'],
                'name' => $style['name'],
                'media' => $style['media']
            ];
        }
        return $result;
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getOptions(string $id): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        return \BearCMS\Internal\Themes::getOptions($id);
    }

    /**
     * 
     * @param string $id
     * @return ?array
     */
    public function getStyleValues(string $id, string $styleID): ?array
    {
        if (!isset(\BearCMS\Internal\Themes::$list[$id])) {
            return null;
        }
        $styles = \BearCMS\Internal\Themes::getStyles($id);
        foreach ($styles as $style) {
            if ($style['id'] === $styleID) {
                if (isset($style['values'])) {
                    return $style['values'];
                }
            }
        }
        return [];
    }

    /**
     * 
     */
    public function addDefault()
    {
        $app = App::get();
        $context = $app->context->get(__FILE__);
        require $context->dir . '/themes/universal/index.php';
    }

    /**
     * 
     * @param mixed $definition
     * @return $this Returns a reference to itself.
     */
    public function defineElementOption($definition)
    {
        \BearCMS\Internal\Themes::defineElementOption($definition);
        return $this;
    }

    /**
     * 
     * @return \BearCMS\Themes\OptionsDefinition
     */
    public function makeOptionsDefinition(): \BearCMS\Themes\OptionsDefinition
    {
        return new \BearCMS\Themes\OptionsDefinition();
    }

}
