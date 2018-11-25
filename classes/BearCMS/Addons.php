<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

use BearCMS\Internal;

/**
 * 
 */
class Addons
{

    /**
     * Announces a new addon.
     * 
     * @param string $id The addon ID.
     * @param callable $callback A function to define addon parameters.
     * @return self Returns a reference to itself.
     */
    public function announce(string $id, callable $callback): self
    {
        Internal\Data\Addons::$announcements[$id] = $callback;
        return $this;
    }

}
