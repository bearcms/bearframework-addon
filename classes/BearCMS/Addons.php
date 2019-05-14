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

/**
 * 
 */
class Addons
{

    /**
     * Register a new addon.
     * 
     * @param string $id The addon ID.
     * @param callable $callback A function to define addon parameters.
     * @return self Returns a reference to itself.
     */
    public function register(string $id, callable $callback): self
    {
        Internal\Data\Addons::$registrations[$id] = $callback;
        return $this;
    }

    /**
     * Adds an addon.
     * 
     * @param string $id
     * @param array $options
     * @return self
     */
    public function add(string $id, array $options = []): self
    {
        $app = App::get();
        if (\BearFramework\Addons::exists($id)) {
            $app->addons->add($id);
            if (isset(Internal\Data\Addons::$registrations[$id])) {
                $addon = new \BearCMS\Addons\Addon($id);
                call_user_func(Internal\Data\Addons::$registrations[$id], $addon);
                if (is_callable($addon->initialize)) {
                    call_user_func($addon->initialize, $options);
                }
            }
        }
        return $this;
    }

}
