<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property string $containerID
 * @internal
 * @codeCoverageIgnore
 */
class ElementsContainerChangeEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param string $containerID
     */
    public function __construct(string $containerID)
    {
        $this
            ->defineProperty('containerID', [
                'type' => 'string'
            ]);
        $this->containerID = $containerID;
    }
}
