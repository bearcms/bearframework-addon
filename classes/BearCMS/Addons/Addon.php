<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Addons;

/**
 * @property string $addonID The addonID.
 * @property ?callable $initialize A function to be called to initialize the addon.
 */
class Addon
{

    use \IvoPetkov\DataObjectTrait;

    public function __construct(string $addonID)
    {
        $this
                ->defineProperty('addonID', [
                    'type' => 'string',
                    'get' => function() use ($addonID) {
                        return $addonID;
                    },
                    'readonly' => true
                ])
                ->defineProperty('initialize', [
                    'type' => '?callable'
                ])
//                ->defineProperty('options', [
//                    'type' => 'array'
//                ])
        ;
    }

}
