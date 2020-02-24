<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Addons;

/**
 * @property-read string $id The addonID.
 * @property callable|null $initialize A function to be called to initialize the addon.
 */
class Addon
{

    use \IvoPetkov\DataObjectTrait;

    public function __construct(string $id)
    {
        $this
            ->defineProperty('id', [
                'type' => 'string',
                'get' => function () use ($id) {
                    return $id;
                },
                'readonly' => true
            ])
            ->defineProperty('initialize', [
                'type' => '?callable'
            ]);
    }
}
