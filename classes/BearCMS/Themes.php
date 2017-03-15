<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearFramework\App;

class Themes
{

    static private $initializedThemes = [];

    public function add(string $id, callable $initializeCallback, callable $applyCallback, array $options = [])
    {
        \BearCMS\Internal\Themes::add($id, $initializeCallback, $applyCallback, $options);
        return $this;
    }

    public function initialize(string $id)
    {
        if (isset(\BearCMS\Internal\Themes::$list[$id])) {
            if (!isset(self::$initializedThemes[$id])) {
                $callback = \BearCMS\Internal\Themes::$list[$id][0];
                call_user_func($callback);
                self::$initializedThemes[$id] = 1;
            }
        }
    }

    public function initializeAll()
    {
        foreach (\BearCMS\Internal\Themes::$list as $id => $data) {
            self::initialize($id);
        }
    }

    public function apply(\BearFramework\App\Response $response)
    {
        $app = App::get();
        $currentThemeID = $app->bearCMS->currentTheme->getID();
        if (isset(\BearCMS\Internal\Themes::$list[$currentThemeID])) {
            $callback = \BearCMS\Internal\Themes::$list[$currentThemeID][1];
            call_user_func($callback, $response, $app->bearCMS->currentTheme->getOptions());
        }
    }

}
