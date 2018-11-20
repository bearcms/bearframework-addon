<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS;

/**
 * 
 */
class Addons
{

    /**
     * Announces a new addon.
     * 
     * @param string $addonID The addon ID.
     * @param callable $callback A function to define addon parameters.
     * @return self Returns a reference to itself.
     */
    public function announce(string $addonID, callable $callback): self
    {
        \BearCMS\Internal\Data\Addons::$announcements[$addonID] = $callback;
        return $this;
    }

}
