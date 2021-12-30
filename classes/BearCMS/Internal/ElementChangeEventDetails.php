<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @property string $elementID
 * @property string $containerID
 * @internal
 * @codeCoverageIgnore
 */
class ElementChangeEventDetails
{

    use \IvoPetkov\DataObjectTrait;

    /**
     * 
     * @param string $elementID
     * @param string|null $containerID
     */
    public function __construct(string $elementID, string $containerID = null)
    {
        $this
            ->defineProperty('elementID', [
                'type' => 'string'
            ])
            ->defineProperty('containerID', [
                'type' => '?string'
            ]);
        $this->elementID = $elementID;
        $this->containerID = $containerID;
    }
}
